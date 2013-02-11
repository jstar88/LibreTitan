package controllers.pages;


import play.*;
import play.mvc.*;

import views.html.game.menu;
import play.data.*;
import models.*;
import services.*;

public class Index extends Controller
{
	public static Result show()
	{
		return ok(menu.render());
	}
	
}