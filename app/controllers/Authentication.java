package controllers;

import play.*;
import play.mvc.*;

import views.html.*;
import play.data.*;
import static play.data.Form.*;
import models.*;
import services.*;
import play.data.validation.ValidationError;
import java.util.*;
import java.security.NoSuchAlgorithmException;

public class Authentication extends Controller {
	//an empty array of form infos
	static Form<Login> loginFormInfo = form(Login.class);

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
	public static Result authenticate() throws NoSuchAlgorithmException {
		
		// if user is already connected
		if (Authenticator.isCurrentUserLogged()) {
			return ok(alreadyConnected.render());
		}
		
		// populate the empty array of form infos with request data
		Form<Login> filledForm = loginFormInfo.bindFromRequest();
		
		// check if form has errors
		if (filledForm.hasErrors()) {
			return badRequest(loginForm.render(filledForm));
		} else {
			
			// create login object from the populated form array
			Login loginData = filledForm.get();
			
			// encrypt password
			loginData.password = Encrypter.sha1(loginData.password);
			
			// login
			if (Authenticator.loginCurrentUser(loginData) == null) {
				filledForm.reject("name", "wrong name or password");
				return badRequest(loginForm.render(filledForm));
			}
			
			//redirect to index
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