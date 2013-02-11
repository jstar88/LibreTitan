package controllers.rights;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import models.*;
import services.*;
import java.lang.reflect.*;

public class PLAYER extends SimpleRights {

	public static Result validate(String page, String method) throws ClassNotFoundException, IllegalAccessException,
	InvocationTargetException, ExceptionInInitializerError,NoSuchMethodException
	{
		return SimpleRights.validate(page,method,PLAYER.class);
	}
	public static User check()
	{
		return Authenticator.getCurrentUser();
	}
}