package models.system;

import play.mvc.*;
import play.libs.*;
import play.libs.F.*;

import scala.concurrent.Await;
import scala.concurrent.duration.Duration;
import akka.actor.*;
import akka.dispatch.*;
import static akka.pattern.Patterns.ask;

import org.codehaus.jackson.*;
import org.codehaus.jackson.node.*;



import java.util.*;

import static java.util.concurrent.TimeUnit.*;

/**
* A chat room is an Actor.
*/
public class ChatRoom extends UntypedActor {
    
    // Default room.
    static ActorRef defaultRoom = Akka.system().actorOf(new Props(ChatRoom.class));
    
    /**
* Join the default room.
*/
    public static void join(final String username, WebSocket.In<JsonNode> in, WebSocket.Out<JsonNode> out) throws Exception{
        
        // Send the Join message to the room
        String result = (String)Await.result(ask(defaultRoom,new Join(username, out), 1000), Duration.create(1, SECONDS));
        
        if("OK".equals(result)) {
            
            // For each event received on the socket,
            in.onMessage(new Callback<JsonNode>() {
               public void invoke(JsonNode event) {
                   
            	   
                   // Send a Talk message to the room.
                   defaultRoom.tell(new Talk(username, event.path("text").getTextValue()), null);
                   
               }
            });
            
            // When the socket is closed.
            in.onClose(new Callback0() {
               public void invoke() {
                   
                   // Send a Quit message to the room.
                   defaultRoom.tell(new Quit(username), null);
                   
               }
            });
            
        } else {
            
            // Cannot connect, create a Json error.
            ObjectNode error = Json.newObject();
            error.put("error", result);
            
            // Send the error to the socket.
            out.write(error);
            
        }
        
    }
    
    // Members of this room.
    Map<String, WebSocket.Out<JsonNode>> members = new HashMap<String, WebSocket.Out<JsonNode>>();
    
    public void onReceive(Object message) throws Exception {
        
        if(message instanceof Join) {
            
            // Received a Join message
            Join join = (Join)message;
            
            // Check if this username is free.
            if(members.containsKey(join.username)) {
                getSender().tell("This username is already used", getSelf());
            } else {
                members.put(join.username, join.channel);
                notifyAll("join", join.username, "has entered the room");
                getSender().tell("OK", getSelf());
            }
            
        } else if(message instanceof Talk) {
            
            // Received a Talk message
            Talk talk = (Talk)message;
            
            notifyAll("talk", talk.username, talk.text);
            
        } else if(message instanceof Quit) {
            
            // Received a Quit message
            Quit quit = (Quit)message;
            
            members.remove(quit.username);
            
            notifyAll("quit", quit.username, "has leaved the room");
        
        } else {
            unhandled(message);
        }
        
    }
    
    // Send a Json event to all members
    public void notifyAll(String kind, String user, String text) {
        for(WebSocket.Out<JsonNode> channel: members.values()) {
            
            ObjectNode event = Json.newObject();
            event.put("kind", kind);
            event.put("user", user);
            event.put("message", text);
            
            ArrayNode m = event.putArray("members");
            for(String u: members.keySet()) {
                m.add(u);
            }
            
            channel.write(event);
        }
    }
    
    // -- Messages
    
    public static class Join {
        
        final String username;
        final WebSocket.Out<JsonNode> channel;
        
        public Join(String username, WebSocket.Out<JsonNode> channel) {
            this.username = username;
            this.channel = channel;
        }
        
    }
    
    public static class Talk {
        
        final String username;
        final String text;
        
        public Talk(String username, String text) {
            this.username = username;
            this.text = text;
        }
        
    }
    
    public static class Quit {
        
        final String username;
        
        public Quit(String username) {
            this.username = username;
        }
        
    }
    
}