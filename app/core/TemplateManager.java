package core;

import services.*;
import play.*;
import java.io.*;

public class TemplateManager {
	public static File getFile(File template, File cache) throws Exception {
		if (cache.exists()) {
			return cache;
		}
		String templateContent = FileIO.readFile(template);
		String engine = FileIO.readFile(Play.application().getFile("/public/javascripts/hogan.js"));
		String starter = FileIO.readFile(Play.application().getFile("/app/core/starter.js"));

		String compiled = (String) VM.runJavascript(engine + starter,"template", templateContent);

		FileIO.writeFile(cache, compiled);
		return cache;
	}
}