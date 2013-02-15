package services;
import java.io.*;
import java.nio.channels.FileChannel;
import java.nio.MappedByteBuffer;
import java.nio.charset.Charset;

public abstract class FileIO {
	private static String readFile(String path) throws IOException {
		FileInputStream stream = new FileInputStream(new File(path));
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

	public static void writeFile(String path,String content) throws IOException {
		Writer out = new BufferedWriter(new OutputStreamWriter(
				new FileOutputStream(path), Charset.defaultCharset()));
		try {
			out.write(content);
		} finally {
			out.close();
		}
	}
}