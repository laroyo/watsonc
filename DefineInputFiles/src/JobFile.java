import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Random;
import java.util.Set;

import au.com.bytecode.opencsv.CSVReader;
import au.com.bytecode.opencsv.CSVWriter;


public class JobFile {

	public static List<FileReader> newFileReaderJob = new ArrayList<FileReader>();
	
	public void readRowFile(List<FileReader> csvFilenames, int noSentences) {
		List<CSVReader> csvReader = new ArrayList<CSVReader>();
		for (int i = 0; i < csvFilenames.size(); i ++) {
			csvReader.add(new CSVReader(csvFilenames.get(i)));
		}

		try {
			CSVWriter csvWriter = new CSVWriter(new FileWriter("job-sentences.csv"));
			String fileRow = "index#relation-type" + "#" + "term1" + "#" + "b1" + "#" + "e1" + "#" + "term2" + "#" + "b2" + "#" + "e2" + "#" + "sentence";
			String[] fileRowArray = fileRow.split("#");
			csvWriter.writeNext(fileRowArray);
			
			List<List<String[]>> allRows = new ArrayList<List<String[]>>();
			for (int i = 0; i < csvReader.size(); i ++) {
				allRows.add(i, csvReader.get(i).readAll());
				allRows.get(i).remove(0);

			}

			for (int i = 0; i < csvReader.size(); i ++) {
				Set<Integer> file1 = randomChoices(noSentences, allRows.get(i).size());
				Iterator<Integer> it = file1.iterator();
				while (it.hasNext()) {
					csvWriter.writeNext(allRows.get(i).get((Integer)it.next()));
				} 
			}

			for (int i = 0; i < csvFilenames.size(); i ++) {
				csvReader.get(i).close();
			}
			csvWriter.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	public Set<Integer> randomChoices(int noValues, int noRows) {
		Random random = new Random();

		Set<Integer> set = new HashSet<Integer>(noValues);

		while(set.size()< noValues) {
			while (set.add(random.nextInt(noRows)) != true)
				continue;
		}
		return set;
	}
	
	public static boolean isNumeric(String str)
	{
	    for (char c : str.toCharArray())
	    {
	        if (!Character.isDigit(c)) return false;
	    }
	    return true;
	}
	
	public static void main(String args[]) {
		int noSentences;
		String[] jobFiles = new String[args.length - 1];
		JobFile job = new JobFile();
		
		if (!isNumeric(args[0])) {
			System.out.println("The first argument should be the number of sentences selected from each file");
			System.out.println("The following arguments should be a list of files");
			return;
		}
		
		noSentences = Integer.parseInt(args[0]);
		for (int i = 1; i < args.length; i ++) {
			jobFiles[i - 1] = args[i]; 
		}
		try {
			
			for (int i = 0; i < jobFiles.length; i ++) {
				newFileReaderJob.add(new FileReader(jobFiles[i]));
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		job.readRowFile(newFileReaderJob, noSentences);
	}

}


