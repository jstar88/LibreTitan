package controllers;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import models.*;
import services.*;

import play.libs.F.*;
import org.codehaus.jackson.*;

public class Application extends Controller {

	public static Result index() {
		if(Authenticator.isCurrentUserLogged())
			flash("logged","logged");
		else
			flash("logged","not logged");
		return ok(index.render());
	}
	
	/**
	 * Handle the chat websocket.
	 */
	public static WebSocket<JsonNode> chat() {
		return new WebSocket<JsonNode>() {

			// Called when the Websocket Handshake is done.
			public void onReady(WebSocket.In<JsonNode> in,
					WebSocket.Out<JsonNode> out) {

				// Join the chat room.
				/*
				 * try { ChatRoom.join("tizio", in, out); } catch (Exception ex)
				 * { ex.printStackTrace(); }
				 */
			}
		};
	}
}