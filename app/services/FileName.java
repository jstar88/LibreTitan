package services;

/**
 * This class assumes that the string used to initialize fullPath has a
 * directory path, filename, and extension. The methods won't work if it
 * doesn't.
 */
public class FileName {
  private String fullPath;
  private char pathSeparator, extensionSeparator;

  public FileName(String str, char sep, char ext) {
    fullPath = str;
    pathSeparator = sep;
    extensionSeparator = ext;
  }
  
  public FileName(String str)
  {
	  fullPath = str;
	  pathSeparator = '/';
	  extensionSeparator = '.';
  }

  public String extension() {
    int dot = fullPath.lastIndexOf(extensionSeparator);
    return fullPath.substring(dot + 1);
  }

  public String filename() { // gets filename without extension
    int dot = fullPath.lastIndexOf(extensionSeparator);
    int sep = fullPath.lastIndexOf(pathSeparator);
    return fullPath.substring(sep + 1, dot);
  }

  public String path() {
    int sep = fullPath.lastIndexOf(pathSeparator);
    return fullPath.substring(0, sep);
  }
}