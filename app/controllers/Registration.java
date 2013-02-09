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

public class Registration extends Controller {

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
	public static Result registers() {
		if (Authenticator.isCurrentUserLogged()) {
			return ok(alreadyConnected.render());
		}
		Form<User> filledForm = signupFormInfo.bindFromRequest();
		if (filledForm.hasErrors()) {
			return badRequest(signupForm.render(filledForm));
		} else {
			User user = filledForm.get();
			if(User.exist(user))
			{
				//ValidationError e = new ValidationError("name", "user already exist",new ArrayList());
				filledForm.errors().put("name","s");
				return badRequest(signupForm.render(filledForm));
			}
			user.save();
			Authenticator.loginCurrentUser(user);
			return redirect(routes.Application.index());
		}
	}

	/**
	 * Remove the account from database and logout
	 */
	public static Result delete() {
		// todo
	}

}