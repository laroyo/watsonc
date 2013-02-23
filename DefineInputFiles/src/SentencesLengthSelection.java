import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.List;

import au.com.bytecode.opencsv.CSVReader;
import au.com.bytecode.opencsv.CSVWriter;


public class SentencesLengthSelection {
	
	public void readRowFile(FileReader csvFilename, String fileName) {
		CSVReader csvReader = new CSVReader(csvFilename);
		int sentenceIndex = -1;
		try {
			String outputFile = fileName.substring(0, fileName.length() - 4);
			CSVWriter csvWriter1 = new CSVWriter(new FileWriter(outputFile.concat("-long.csv")));
			CSVWriter csvWriter2 = new CSVWriter(new FileWriter(outputFile.concat("-shortAndAverage.csv")));
			int lengthCounter = 0;
			int sentences = 0;
			int minLength = 1000000;
			int maxLength = 0;
			List<String[]> allRows = csvReader.readAll();
			
			for (int i = 0; i < allRows.get(0).length; i ++) {
				if (allRows.get(0)[i].equals("sentence")) 
					sentenceIndex = i;
			}
			
			for (int i = 0; i < allRows.size(); i ++) {
				if (allRows.get(i)[sentenceIndex].length() < minLength) 
					minLength = allRows.get(i)[sentenceIndex].length();
				if (allRows.get(i)[sentenceIndex].length() > maxLength) 
					maxLength = allRows.get(i)[sentenceIndex].length();
				
				lengthCounter += allRows.get(i)[sentenceIndex].length();
				sentences ++;
			}	
			
			int averageLength = (int)lengthCounter/sentences;
			int threshold = averageLength + (int)(maxLength - averageLength) / 2;
			System.out.println(threshold);
			System.out.println(averageLength);
			System.out.println(minLength);
			System.out.println(maxLength);
			
			String fileRow = "index#relation-type" + "#" + "term1" + "#" + "b1" + "#" + "e1" + "#" + "term2" + "#" + "b2" + "#" + "e2" + "#" + "sentence";
			String[] fileRowArray = fileRow.split("#");
			int header1 = 0, header2 = 0;
			
			for (int i = 1; i < allRows.size(); i ++) {
				if (allRows.get(i)[sentenceIndex].length() <= threshold) {
					if (header2 == 0) {
						csvWriter2.writeNext(fileRowArray);
					}
					header2 = 1;
					csvWriter2.writeNext(allRows.get(i));
				}
				else {
					if (header1 == 0) {
						csvWriter1.writeNext(fileRowArray);
					}
					header1 = 1;
					csvWriter1.writeNext(allRows.get(i));
				}
			}	
			
			csvReader.close();
			csvWriter1.close();
			csvWriter2.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
}
