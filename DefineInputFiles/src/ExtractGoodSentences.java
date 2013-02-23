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


public class ExtractGoodSentences {

	public void readRowFile(FileReader csvFilename, String fileName) {
		String[] row = null;
		CSVReader csvReader = new CSVReader(csvFilename);
		CSVWriter writer1 = null, writer2 = null, writer3 = null, writer4 = null;
		int term1Index = -1, term2Index = -1, sentenceIndex = -1;
		
		try {
			String outputFile = fileName.substring(0, fileName.length() - 4);
						
			FileWriter semicolonSentencesFile = new FileWriter(outputFile.concat("-withSemicolon.csv"));
			FileWriter noSemicolonSentencesFile = new FileWriter(outputFile.concat("-noSemicolon.csv"));
			
			FileWriter keyWordBetweenBracketsSentencesFile = new FileWriter(outputFile.concat("-withKeyWordBetweenBrackets.csv"));
			FileWriter noKeyWordBetweenBracketsSentencesFile = new FileWriter(outputFile.concat("-noKeyWordBetweenBrackets.csv"));
			
			String fileRow = "index#relation-type" + "#" + "term1" + "#" + "b1" + "#" + "e1" + "#" + "term2" + "#" + "b2" + "#" + "e2" + "#" + "sentence";
			String[] fileRowArray = fileRow.split("#");
			int header1 = 0, header2 = 0, header3 = 0, header4 = 0;
			
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
				// true = contains ; between the key words
				if (containsSemicolon(row[sentenceIndex], row[term1Index], row[term2Index]) == true) {
					writer1 = new CSVWriter(semicolonSentencesFile);
					if (header1 == 0) {
						writer1.writeNext(fileRowArray);
					}
					header1 = 1;
					writer1.writeNext(row);
				}
				else {
					writer2 = new CSVWriter(noSemicolonSentencesFile);
					if (header2 == 0) {
						writer2.writeNext(fileRowArray);
					}
					header2 = 1;
					writer2.writeNext(row);
				}				
				// true = one key word is between parenthesis
				if (containsKeyWordBetweenBrackets(row[sentenceIndex], row[term1Index], row[term2Index]) == true) {
					writer3 = new CSVWriter(keyWordBetweenBracketsSentencesFile);
					if (header3 == 0) {
						writer3.writeNext(fileRowArray);
					}
					header3 = 1;
					writer3.writeNext(row);
				}
				else {					
					writer4 = new CSVWriter(noKeyWordBetweenBracketsSentencesFile);
					if (header4 == 0) {
						writer4.writeNext(fileRowArray);
					}
					header4 = 1;
					writer4.writeNext(row);
				}					
			}	
			csvReader.close();
			writer1.close();
			writer2.close();
			writer3.close();
			writer4.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	public boolean containsSemicolon(String sentence, String term1, String term2) {
		// This regex searches for the following pattern:
		// ... Term1|Term2 ... ;|: ... Term2|Term1 ...
		List<String> tokens = new ArrayList<String>();
		tokens.add("(.*" + Pattern.quote(term2) + ".*;.*" + Pattern.quote(term1) + ".*)");
		tokens.add("(.*" + Pattern.quote(term1) + ".*;.*" + Pattern.quote(term2) + ".*)");
		String patternString = StringUtils.join(tokens, "|");
		Pattern p1 = Pattern.compile(patternString);
		Matcher m1 = p1.matcher(Matcher.quoteReplacement(sentence));

		if (m1.find()) {
			return true;
		}
		return false;
	}
	
	public List<String> containsBrackets(String sentence) {
			String pattern = "\\(.*?\\)";
			Pattern p1 = Pattern.compile(pattern);
			Matcher m1 = p1.matcher(sentence);
			List<String> matches = new ArrayList<String>(); 
			while(m1.find()) {
			    matches.add(m1.group()); 
			}
			return matches;
	}
	
	public boolean containsKeyWordBetweenBrackets(String sentence, String term1, String term2) {
		List<String> matches = new ArrayList<String>();
		matches = containsBrackets(sentence);
		int contains = 0;
		List<String> tokens = new ArrayList<String>();
		tokens.add(Pattern.quote(term1));
		tokens.add(Pattern.quote(term2));

		for(int i = 0; i < matches.size(); i ++) {
					
			if (matches.get(i).contains(term1) && matches.get(i).contains(term2)) {
				continue;
			}
			if (!matches.get(i).contains(term1) && !matches.get(i).contains(term2)) {
				continue;
			}
			if (matches.get(i).contains(term1) && !matches.get(i).contains(term2)) {
				contains ++;
			}
			if (matches.get(i).contains(term2) && !matches.get(i).contains(term1)) {
				contains ++;
			}
		}

		if (contains == 0) {
			return false;
		}
		else {
			return true;
		}

	}

}
