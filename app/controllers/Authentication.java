package controllers;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import static play.data.Form.*;
import models.*;
import services.*;

public class Authentication extends Controller {

	static Form<User> signupForm = form(User.class);

	public static Result login() {
		if (Authenticator.isCurrentUserLogged())
		{
			return ok(alreadyConnected.render());
		}
		return ok(form.render(signupForm));
	}

	/**
	 * Handle login form submission.
	 */
	public static Result authenticate() {
		if (Authenticator.isCurrentUserLogged())
		{
			return ok(alreadyConnected.render());
		}
		Form<User> filledForm = signupForm.bindFromRequest();
		if (filledForm.hasErrors()) {
			return badRequest(form.render(filledForm));
		} else {
			User created = filledForm.get();
			return redirect(routes.Application.index());
		}
	}

	public static Result logout() {
		if (!Authenticator.isCurrentUserLogged())
			return ok(notConnected.render());
		return redirect(routes.Application.index());
	}

}