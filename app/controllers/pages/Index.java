package controllers.pages;


import play.*;
import play.mvc.*;

import views.html.game.index;
import play.data.*;
import models.*;
import services.*;

public class Index extends Controller
{
	public static Result show()
	{
		return ok(index.render());
	}
	
}