package controllers.rights;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import models.*;
import services.*;
import java.lang.reflect.*;

public class GO extends SimpleRights {

	public static Result validate(String page, String method) throws ClassNotFoundException, IllegalAccessException,
	InvocationTargetException, ExceptionInInitializerError,NoSuchMethodException
	{
		return SimpleRights.validate(page,method,GO.class);
	}
	public static User check()
	{
		User currentUser = PLAYER.check();
		if(currentUser == null || currentUser.type < 1)
		{
			return null;
		}
		return currentUser;
	}
}