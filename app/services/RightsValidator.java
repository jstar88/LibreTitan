package services;

import models.User;

public class RightsValidator {
	public static boolean haveAccess(User user, String rights) {
		if (user == null) {
			return false;
		}
		if(rights.equalsIgnoreCase("player"))
		{
			return user.type > 0;
		}
		if(rights.equalsIgnoreCase("go"))
		{
			return user.type > 1;
		}
		if(rights.equalsIgnoreCase("sgo"))
		{
			return user.type > 2;
		}
		if(rights.equalsIgnoreCase("ga"))
		{
			return user.type > 3;
		}
		return false;
	}

}