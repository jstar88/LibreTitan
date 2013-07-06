package controllers.pages.player;

import play.*;
import play.mvc.*;
//import views.html.game.ingame;
import views.html.game.*;
//import views.js.*;

import play.data.*;
import models.*;
import models.system.*;
import services.*;
import org.codehaus.jackson.*;
import org.codehaus.jackson.node.*;
import java.io.PrintWriter;
import java.io.StringWriter;

public class Chat extends Controller {

	/**
	 * Display the home page.
	 */
	public static Result show(User user) {
		try{
			return ok(chatIndex.render(user.skin));
		}
		catch(Exception ex)
		{
			StringWriter errors = new StringWriter();
			ex.printStackTrace(new PrintWriter(errors));
			return ok(errors.toString());
		}
	}

	/**
	 * Display the chat room.
	 */
	public static Result chatRoom(User user) {
		return ok(chatRoom.render(user.skin,user.name));
	}

	public static Result chatRoomJs(String username) {
		return ok(chatRoomJs.render(username));
	}

	/**
	 * Handle the chat websocket.
	 */
	public static WebSocket<JsonNode> chat(final User user) {
		return new WebSocket<JsonNode>() {

			// Called when the Websocket Handshake is done.
			public void onReady(WebSocket.In<JsonNode> in,
					WebSocket.Out<JsonNode> out) {

				// Join the chat room.
				try {
					ChatRoom.join(user.name, in, out);
				} catch (Exception ex) {
					ex.printStackTrace();
				}
			}
		};
	}

}