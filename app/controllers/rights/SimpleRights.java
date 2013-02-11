package controllers.rights;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import models.*;
import services.*;

import java.lang.reflect.*;

class SimpleRights extends Controller {
	public static Result validate(String page, String methodName,Class className)
			throws ClassNotFoundException, IllegalAccessException,
			InvocationTargetException, ExceptionInInitializerError,
			NoSuchMethodException {
		
		User currentUser =(User) className.getDeclaredMethod("check",
				new Class[] {}).invoke(null, new Object[] {});
		if (currentUser != null) {
			page = page.substring(0, 1).toUpperCase() + page.substring(1);
			return (Result) Class.forName("controllers.pages."+page).getDeclaredMethod(methodName,
					new Class[] {}).invoke(null, new Object[] {});
		} else {
			return onInvalid();
		}
	}

	public static User check()  {
		return null;
	}

	public static Result onInvalid() {
		return badRequest(notAllowed.render());
	}
}