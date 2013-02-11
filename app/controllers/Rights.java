package controllers;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import models.*;
import services.*;

import java.lang.reflect.*;

public class Rights extends Controller {
	public static Result validate(String rights, String page, String methodName)
			throws ClassNotFoundException, IllegalAccessException,
			InvocationTargetException, ExceptionInInitializerError,
			NoSuchMethodException {

		if (!RightsValidator.haveAccess(Authenticator.getCurrentUser(), rights)) {
			return badRequest(notAllowed.render());
		}

		page = page.substring(0, 1).toUpperCase() + page.substring(1);
		return (Result) Class
				.forName(
						"controllers.pages." + rights.toLowerCase() + "."
								+ page)
				.getDeclaredMethod(methodName, new Class[] {})
				.invoke(null, new Object[] {});

	}
}