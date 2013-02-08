package services;

import play.cache.Cache;
import play.*;
import play.mvc.*;
import play.data.*;
import models.*;
import views.html.*;

public abstract class Authenticator {
	public static boolean isCurrentUserLogged() {
		return session("id_user") != null;
	}

	public static User loginCurrentUser(String name, String password) {
		User user = User.authenticate(name, password);
		if (user == null)
			return;
		session("id_user", user.getId());
		Cache.set("User:" + user.getId(), user);
		return user;
	}

	public static void logoutCurrentUser() {
		Cache.remove(session("id_user"));
		session().clear();
		return;
	}

	public static User getCurrentUser() {
		Long idUser = session("id_user");
		User user = Cache.get("User:" + idUser);
		if (user == null) {
			user = User.findById(idUser);
			Cache.set("User:" + idUser, user);
		}
		return user;
	}
}