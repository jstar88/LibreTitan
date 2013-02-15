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

public class Rights extends Controller {

	/**
	 * Execute the request
	 */
	public static Result validate(final String rights,final String page,final String methodName)
			throws ClassNotFoundException, IllegalAccessException,
			InvocationTargetException, ExceptionInInitializerError,
			NoSuchMethodException {
		
		// check if current user have access to target controller
		final User user = Authenticator.getCurrentUser();
		if (!RightsValidator.haveAccess(user, rights)) {
			return badRequest(notAllowed.render());
		}

		// elaborate the Result
		Promise<Result> promiseOfResult = play.libs.Akka
				.future(new Callable<Result>() {
					public Result call()
							throws ClassNotFoundException, IllegalAccessException,
							InvocationTargetException, ExceptionInInitializerError,
							NoSuchMethodException
					{
						return  callController(rights, page, methodName, user);
					}
				});
		
		// when the Result is ready return it
		return async(promiseOfResult.map(new Function<Result, Result>() {
			public Result apply(Result result) {
				return result;
			}
		}));

	}

	/**
	 * Call the corrispective controller and return the Result
	 */
	private static Result callController(String rights, String page,
			String methodName, User user) throws ClassNotFoundException,
			IllegalAccessException, InvocationTargetException,
			ExceptionInInitializerError, NoSuchMethodException {
		
		page = page.substring(0, 1).toUpperCase() + page.substring(1);
		return (Result) Class
				.forName(
						"controllers.pages." + rights.toLowerCase() + "."
								+ page)
				.getDeclaredMethod(methodName, new Class[] { User.class })
				.invoke(null, new Object[] { user });
	}
}