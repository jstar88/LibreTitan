package services;

import models.User;

public class RightsValidator {
	public static boolean haveAccess(User user, String rights) {
		if (user == null) {
			return false;
		}
		switch (rights.toLowerCase()) {
		case "player":
			return user.type > 0;
		case "go":
			return user.type > 1;
		case "sgo":
			return user.type > 2;
		case "ga":
			return user.type > 3;
		default:
			return false;
		}
	}

}