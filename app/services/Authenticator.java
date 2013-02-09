package services;

import play.cache.Cache;
import play.*;
import play.mvc.*;
import play.data.*;
import models.*;
import views.html.*;
import interfaces.Loginable;

public class Authenticator {
	/**
	 * Return true if the current user is logged 
	 */
	public static boolean isCurrentUserLogged() {
		return Controller.session("id_user") != null;
	}
	
	/**
	 * Login the current visitor and return its User object or null on insuccess 
	 */
	public static User loginCurrentUser(Loginable men) {
		User user = User.authenticate(men);
		if (user == null)
			return null;
		Controller.session("id_user", user.id+"");
		Cache.set("User:" + user.id, user);
		return user;
	}
	
	/**
	 * Logout the current user 
	 */
	public static void logoutCurrentUser() {
		Cache.remove("User:"+Controller.session("id_user"));
		Controller.session().clear();
		return;
	}

	/**
	 * Return the current user from cache and return its User object or null if user is not logged
	 */
	public static User getCurrentUser() {
		String idUser = Controller.session("id_user");
		if (idUser  == null)
		{
			return null;
		}
		Object user = Cache.get("User:" + idUser);
		if (user == null) {
			user = User.findById(Long.valueOf(idUser));
			Cache.set("User:" + idUser, (User)user);
		}
		return (User)user;
	}
}