package controllers;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import static play.data.Form.*;
import models.*;
import services.*;

public class Authentication extends Controller {

	static Form<Login> loginFormInfo = form(Login.class);

	// --------- user managment -----------
	/**
	 * Show a blank login form
	 */
	public static Result login() {
		if (Authenticator.isCurrentUserLogged()) {
			return ok(alreadyConnected.render());
		}
		return ok(loginForm.render(loginFormInfo));
	}

	/**
	 * Authenticate the user by login data
	 */
	public static Result authenticate() {
		if (Authenticator.isCurrentUserLogged()) {
			return ok(alreadyConnected.render());
		}
		Form<Login> filledForm = loginFormInfo.bindFromRequest();
		if (filledForm.hasErrors()) {
			return badRequest(loginForm.render(filledForm));
		} else {
			Login loginData = filledForm.get();
			Authenticator
					.loginCurrentUser(loginData);
			return redirect(routes.Application.index());
		}
	}

	/**
	 * Logout the user
	 */
	public static Result logout() {
		if (!Authenticator.isCurrentUserLogged())
			return ok(notConnected.render());
		Authenticator.logoutCurrentUser();
		return redirect(routes.Application.index());
	}

}