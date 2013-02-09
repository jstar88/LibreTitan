package services;

import play.cache.Cache;
import play.*;
import play.mvc.*;
import play.data.*;
import models.*;
import views.html.*;
import interfaces.Loginable;

public class Authenticator {
	public static boolean isCurrentUserLogged() {
		return Controller.session("id_user") != null;
	}

	public static User loginCurrentUser(Loginable men) {
		User user = User.authenticate(men);
		if (user == null)
			return null;
		Controller.session("id_user", user.id+"");
		Cache.set("User:" + user.id, user);
		return user;
	}

	public static void logoutCurrentUser() {
		Cache.remove("User:"+Controller.session("id_user"));
		Controller.session().clear();
		return;
	}

	public static User getCurrentUser() {
		String idUser = Controller.session("id_user");
		Object user = Cache.get("User:" + idUser);
		if (user == null) {
			user = User.findById(Long.valueOf(idUser));
			Cache.set("User:" + idUser, (User)user);
		}
		return (User)user;
	}
}