package services;

import javax.script.*;

public abstract class VM {
	public static Object runJavascript(String code) throws ScriptException {
		// create a script engine manager
		ScriptEngineManager factory = new ScriptEngineManager();
		// create a JavaScript engine
		ScriptEngine engine = factory.getEngineByName("JavaScript");
		// evaluate JavaScript code from String
		return engine.eval(code);
	}
}