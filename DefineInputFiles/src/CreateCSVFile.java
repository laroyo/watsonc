import java.io.BufferedReader;
import java.io.DataInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;


import au.com.bytecode.opencsv.CSVWriter;


public class CreateCSVFile {

	public static void main(String[] args) {
		
		String directory = args[0];
		File folder = new File(directory);
		File[] allFiles = folder.listFiles();
		ArrayList<File> files = new ArrayList<File>();
		for (File file: allFiles) {
		    files.add(file);
		}
		ArrayList<CSVWriter> results = new ArrayList<CSVWriter>(files.size());

		String fileRow = "index#relation-type#term1#b1#e1#term2#b2#e2#sentence";
		
		String[] fileRowArray = fileRow.split("#");
		
		for (int i = 0; i < files.size(); i ++) {
			try {
				results.add(new CSVWriter(new FileWriter(files.get(i).toString() + ".csv")));
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			results.get(i).writeNext(fileRowArray);
		}
		
		for (int i = 0; i < files.size(); i ++) {
			try{
				FileInputStream fstream = new FileInputStream(files.get(i));
				DataInputStream in = new DataInputStream(fstream);
				BufferedReader br = new BufferedReader(new InputStreamReader(in));
				String strLine;
				while ((strLine = br.readLine()) != null)   {
					String[] row = strLine.split("\t");
					results.get(i).writeNext(row);
				}

				in.close();
				results.get(i).close();
			}catch (Exception e){//Catch exception if any
				System.err.println("Error: " + e.getMessage());
			}
		}
	}

}
