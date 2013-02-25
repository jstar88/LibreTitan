package controllers.pages.player;


import play.*;
import play.mvc.*;

import views.html.game.galaxy;
import play.data.*;
import models.*;
import services.*;

public class Galaxy extends Controller
{
	public static Result show(User user)
	{
		return ok(galaxy.render(user.skin));
	}
	
}