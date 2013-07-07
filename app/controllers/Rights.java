package controllers;

import play.*;
import play.mvc.*;
import views.html.*;
import play.data.*;
import models.*;
import services.*;
import java.lang.reflect.*;
import play.libs.F;
import play.libs.F.*;
import java.util.concurrent.Callable;
import org.codehaus.jackson.*;

public class Rights extends Controller {

	/**
	 * Execute the http request and return the http response
	 */
	public static Result validate(final String rights, final String page,
			final String methodName) throws Exception {

		// check if current user have access to target controller
		final User user = Authenticator.getCurrentUser();
		if (!RightsValidator.haveAccess(user, rights)) {
			return badRequest(notAllowed.render());
		}

		return (Result) callController(rights, page, methodName, user);
		/*
		 * // elaborate the Result Promise<Result> promiseOfResult =
		 * play.libs.Akka .future(new Callable<Result>() { public Result call()
		 * throws Exception { return (Result) callController(rights, page,
		 * methodName, user); } });
		 * 
		 * // when the Result is ready return it return
		 * async(promiseOfResult.map(new Function<Result, Result>() { public
		 * Result apply(Result result) { return result; } }));
		 */

	}

	/**
	 * Execute the http request and return the websocket
	 */
	public static WebSocket validateWebSocket(String rights, String page,
			String methodName) throws Exception {

		// check if current user have access to target websocket
		final User user = Authenticator.getCurrentUser();
		if (!RightsValidator.haveAccess(user, rights)) {
			throw new Exception("ee");
		}

		// call the controller that return a websocket
		return (WebSocket) callController(rights, page, methodName, user);

	}
	public static WebSocket<JsonNode> validateWebSocketJsonNode(String rights, String page,
			String methodName) throws Exception {
		return (WebSocket<JsonNode>) validateWebSocket(rights,page,methodName);
	}
	

	/**
	 * Call the corrispective controller and return the Result
	 */
	private static Object callController(String rights, String page,
			String methodName, User user) throws Exception {

		page = page.substring(0, 1).toUpperCase() + page.substring(1);
		Object returns = null;
		try {
			returns = Class
					.forName(
							"controllers.pages." + rights.toLowerCase() + "."
									+ page)
					.getDeclaredMethod(methodName, new Class[] { User.class })
					.invoke(null, new Object[] { user });
		} catch (Exception e) {
			throw new Exception(rights + ":" + page + ":" + methodName);
		}

		return returns;

	}
}