package controllers;

import org.apache.http.impl.cookie.DateUtils;
import java.util.*;
import play.mvc.*;
import services.FileName;
import core.TemplateManager;
import play.*;
import java.io.*;

public class StaticFilesController extends Controller {

	private static String nextYearString = StaticFilesController
			.getNextYearAsString();

	public static Result getImg(String path) {

		FileName fileName = new FileName(path);
		response().setHeader(EXPIRES, nextYearString);
		response().setContentType("image/" + fileName.extension());
		return ok(Play.application().getFile("/public/images/" + path));
	}

	public static Result getBoostrapImg(String path) {

		FileName fileName = new FileName(path);
		response().setHeader(EXPIRES, nextYearString);
		response().setContentType("image/" + fileName.extension());
		return ok(Play.application().getFile(
				"/public/images/" + fileName.filename() + "."
						+ fileName.extension()));
	}

	public static Result getCss(String path) {

		response().setHeader(EXPIRES, nextYearString);
		response().setContentType("text/css");
		return ok(Play.application().getFile("/public/stylesheets/" + path));
	}

	public static Result getJs(String path) {

		response().setHeader(EXPIRES, nextYearString);
		response().setContentType("application/x-javascript");
		return ok(Play.application().getFile("/public/javascripts/" + path));
	}

	private static String getNextYearAsString() {
		Calendar calendar = new GregorianCalendar();
		calendar.add(Calendar.YEAR, 1);
		return DateUtils.formatDate(calendar.getTime());
	}
	
	public static Result getTemplate(String path) throws Exception
	{
		response().setHeader(EXPIRES, nextYearString);
		response().setContentType("application/x-javascript");
		File template = Play.application().getFile("app/views/templates/" + path + ".mustache");
		File cache = Play.application().getFile("/public/javascripts/templates/" + path + ".js");
		File file = TemplateManager.getFile(template,cache);
		return ok(file);
	}

}