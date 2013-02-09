package controllers;

import play.*;
import play.mvc.*;
import java.util.*;
import views.html.*;
import play.data.*;
import play.data.validation.ValidationError;
import static play.data.Form.*;
import models.*;
import services.*;
import java.security.NoSuchAlgorithmException;

public class Registration extends Controller {
	// an empty array of form info
	static Form<User> signupFormInfo = form(User.class);

	/**
	 * Show a blank register form
	 */
	public static Result signup() {
		if (Authenticator.isCurrentUserLogged()) {
			return ok(alreadyConnected.render());
		}
		return ok(signupForm.render(signupFormInfo));
	}

	/**
	 * Register the user by signup form data
	 */
	public static Result registers() throws NoSuchAlgorithmException {
		if (Authenticator.isCurrentUserLogged()) {
			return ok(alreadyConnected.render());
		}

		// populate the empty array of form infos with request data
		Form<User> filledForm = signupFormInfo.bindFromRequest();

		// check if form has errors
		if (filledForm.hasErrors()) {
			return badRequest(signupForm.render(filledForm));
		}

		// check accept conditions
		if (!"true".equals(filledForm.field("accept").value())) {
			filledForm.reject("accept",
					"You must accept the terms and conditions");
		}

		// create user object from request data
		User user = filledForm.get();

		// check if the email is already taken
		if (User.existEmail(user.email)) {
			filledForm.reject("email", "This email is already taken");
		}

		// check if the name is valid
		if (user.name.equals("admin") || user.name.equals("guest")) {
			filledForm.reject("username", "This username is already taken");
		}

		// check if the name is already taken
		if (User.existName(user.name)) {
			filledForm.reject("name", "This username is already taken");
		}

		// recheck if form has errors
		if (filledForm.hasErrors()) {
			return badRequest(signupForm.render(filledForm));
		}

		// encrypt password
		user.password = Encrypter.sha1(user.password);

		// save user in db
		user.save();

		// login the user
		Authenticator.loginCurrentUser(user);

		// redirect
		return redirect(routes.Application.index());

	}

	/**
	 * Remove the account from database and logout
	 */
	public static Result delete() {
		// todo
		return redirect(routes.Application.index());
	}

}