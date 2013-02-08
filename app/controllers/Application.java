package controllers;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import models.*;
import services.*;

public class Application extends Controller {

	public static Result index() {
		if(Authenticator.isCurrentUserLogged())
			flash("logged","logged");
		else
			flash("logged","not logged");
		return ok(index.render());
	}
}