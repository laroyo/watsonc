import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.commons.lang3.StringUtils;

import au.com.bytecode.opencsv.CSVReader;
import au.com.bytecode.opencsv.CSVWriter;


public class ClusterOnRelations {
	
	public void readRowFile(FileReader csvFilename, String fileName) {
		String[] row = null;
		CSVReader csvReader = new CSVReader(csvFilename);
		CSVWriter writer1 = null, writer2 = null, writer3 = null;
		int term1Index = -1, term2Index = -1, sentenceIndex = -1;
		try {
			String outputFile = fileName.substring(0, fileName.length() - 4);
			FileWriter sentencesWithRelationsBetweenFile = new FileWriter(outputFile.concat("-withRelationsBetween.csv"));
			FileWriter sentencesWithRelationsOutsideFile = new FileWriter(outputFile.concat("-withRelationsOutside.csv"));
			FileWriter sentencesNoRelationsFile = new FileWriter(outputFile.concat("-noRelations.csv"));
			int[] response = new int[3]; 
			
			String fileRow = "index#relation-type" + "#" + "term1" + "#" + "b1" + "#" + "e1" + "#" + "term2" + "#" + "b2" + "#" + "e2" + "#" + "sentence";
			String[] fileRowArray = fileRow.split("#");
			int header1 = 0, header2 = 0, header3 = 0;
			
			row = csvReader.readNext();
			for (int i = 0; i < row.length; i ++) {
				if (row[i].equals("term1")) 
					term1Index = i;
				if (row[i].equals("term2")) 
					term2Index = i;
				if (row[i].equals("sentence")) 
					sentenceIndex = i;
			}
			
			while((row = csvReader.readNext()) != null) {
				// true = contains ; or : between the key words
				response = searchForRelations(row[sentenceIndex], row[term1Index], row[term2Index]);
				if (response[0] == 1) {
					writer1 = new CSVWriter(sentencesWithRelationsBetweenFile);
					if (header1 == 0) {
						writer1.writeNext(fileRowArray);
					}
					header1 = 1;
					writer1.writeNext(row);
				} 
				if (response[1] == 1) {
					writer2 = new CSVWriter(sentencesWithRelationsOutsideFile);
					if (header2 == 0) {
						writer2.writeNext(fileRowArray);
					}
					header2 = 1;
					writer2.writeNext(row);
				} 
				if (response[2] == 1) {
					writer3 = new CSVWriter(sentencesNoRelationsFile);
					if (header3 == 0) {
						writer3.writeNext(fileRowArray);
					}
					header3 = 1;
					writer3.writeNext(row);
				} 
				
			}
			writer1.close();
			writer2.close();
			writer3.close();
			csvReader.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	public String extractSentenceBetweenKeyWords(String sentence, String term1, String term2) {
		List<String> tokens = new ArrayList<String>();
		tokens.add("(" + Pattern.quote(term1) + ")(.*?)(" + Pattern.quote(term2) + ")");
		tokens.add("(" + Pattern.quote(term2) + ")(.*?)(" + Pattern.quote(term1) + ")");
		String patternString = StringUtils.join(tokens, "|");
		
		Pattern p1 = Pattern.compile(patternString);
		Matcher m1 = p1.matcher(sentence);
		
		if (m1.find()) {
//			System.out.println(m1.group());
			return m1.group();
		}
		else {
//			System.out.println("----------------------------");
//			System.out.println(sentence);
			return "";
		}
	}
	
	public boolean containsRelation(String sequence) {
		String[] relations = {"treat", "prevent", "cause", "is a", "part of", "side effect", 
				"contraindicate", "symptom", "locat", "manifestation", "associated with", "diagnose" };
		int counter = 0;
		for (int i = 0; i < relations.length; i ++) {
			if (sequence.contains(relations[i])) {
	//			System.out.println(relations[i]);
				counter ++;	
			}
		}	
		if (counter == 0)
			return false;
		else 
			return true;
	}
	
	public int[] searchForRelations(String sentence, String term1, String term2) {
		int[] cases = new int[3];
		cases[0] = 0;
		cases[1] = 0;
		cases[2] = 0;
//		System.out.println(sentence);
		String sequence = extractSentenceBetweenKeyWords(sentence, term1, term2);
		if (containsRelation(sequence)) {
			cases[0] = 1; 
	//		System.out.println("between key words");
		}
		if (containsRelation(sentence.replace(sequence, ""))) {
			cases[1] = 1;
	//		System.out.println("outside key words");
		}
		if (!containsRelation(sequence) && !containsRelation(sentence.replace(sequence, ""))) {
			cases[2] = 1;
	//		System.out.println("no relation");
		}
		return cases;
	}
}
