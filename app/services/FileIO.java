package services;
import java.io.*;
import java.nio.channels.FileChannel;
import java.nio.MappedByteBuffer;
import java.nio.charset.Charset;

public abstract class FileIO {
	public static String readFile(String path) throws IOException {
		return readFile(new File(path));
	}
	public static String readFile(File file) throws IOException {
		
			FileInputStream stream = new FileInputStream(file);
			try {
				FileChannel fc = stream.getChannel();
				MappedByteBuffer bb = fc.map(FileChannel.MapMode.READ_ONLY, 0,
						fc.size());
				/* Instead of using default, pass in a decoder. */
				return Charset.defaultCharset().decode(bb).toString();
			} finally {
				stream.close();
			}
	}
	public static void writeFile(File file,String content) throws IOException {
		if(!file.exists()){
			file. getParentFile().mkdirs();
			file.createNewFile();
		}
		Writer out = new BufferedWriter(new OutputStreamWriter(
				new FileOutputStream(file), Charset.defaultCharset()));
		try {
			out.write(content);
		} finally {
			out.close();
		}
	}
	public static void writeFile(String path,String content) throws IOException {
		writeFile(new File(path),content);
	}
}