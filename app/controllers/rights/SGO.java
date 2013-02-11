package controllers.rights;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import models.*;
import services.*;
import java.lang.reflect.*;

public class SGO extends SimpleRights {

	public static Result validate(String page, String method) throws ClassNotFoundException, IllegalAccessException,
	InvocationTargetException, ExceptionInInitializerError,NoSuchMethodException
	{
		return SimpleRights.validate(page,method,SGO.class);
	}
	public static User check()
	{
		User currentUser = GO.check();
		if(currentUser == null || currentUser.type < 2)
		{
			return null;
		}
		return currentUser;
	}
}