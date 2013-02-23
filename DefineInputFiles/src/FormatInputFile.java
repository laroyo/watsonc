import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import org.apache.commons.lang3.tuple.ImmutablePair;
import org.apache.commons.lang3.tuple.Pair;

import au.com.bytecode.opencsv.CSVReader;
import au.com.bytecode.opencsv.CSVWriter;

public class FormatInputFile {
	int index = 1;
	ArrayList<Pair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>>> globalList = new ArrayList<Pair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>>>();
	CSVWriter writer = null;
	String outputFile = null;
	public void readRowFile(FileReader csvFilename, String fileName) {
		String[] row = null;
		CSVReader csvReader = new CSVReader(csvFilename, ',');
		String fileRow = null;
		int term1Index = -1, term2Index = -1, sentenceIndex = -1, relationIndex = -1;
		try {
			outputFile = fileName.substring(0, fileName.length() - 4).concat("-all.csv");
			writer = new CSVWriter(new FileWriter(outputFile));
			fileRow = "index#relation-type" + "#" + "term1" + "#" + "b1" + "#" + "e1" + "#" + "term2" + "#" + "b2" + "#" + "e2" + "#" + "sentence";
			String[] fileRowArray = fileRow.split("#");
			writer.writeNext(fileRowArray);
			
			row = csvReader.readNext();
			for (int i = 0; i < row.length; i ++) {
				if (row[i].equals("term1")) 
					term1Index = i;
				else if (row[i].equals("term2")) 
					term2Index = i;
				else if (row[i].equals("sentence")) 
					sentenceIndex = i;
				if (row[i].equals("relation-type")) 
					relationIndex = i;
			}
			
			while((row = csvReader.readNext()) != null) {	
				
				if (row.length != sentenceIndex + 1) {
					for (int i = sentenceIndex + 1; i < row.length; i ++) {
						if (!row[i].isEmpty()) {
							row[sentenceIndex] = row[sentenceIndex].concat(";");
							row[sentenceIndex] = row[sentenceIndex].concat(row[i]);
						}
					}
				}

				String sent = removeNewLines(row[sentenceIndex]);
				if (!sent.endsWith(".")) {
					System.out.println("mda");
					sent = sent.concat(".");
				}
				
				String sentence = modifySentence(sent, row[term1Index], row[term2Index]);
				
				String term1 = removeSquareBrackets(row[term1Index]).toLowerCase();
				String term2 = removeSquareBrackets(row[term2Index]).toLowerCase();
				ArrayList<Pair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>>> list = extractOccurrences(sentence, term1, term2);

				String relationType = row[relationIndex];
				createSentences(list, sentence, relationType);
	
			}
			csvReader.close();
			writer.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	public String createSentences(ArrayList<Pair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>>> list, String sentence, String relation) {
		Iterator < Pair < Pair<String, Pair <Integer, Integer>>, Pair<String, Pair <Integer, Integer>> >> it = list.iterator();
		StringBuffer modifiedSentence = null;
		String fileRow = null;
		while(it.hasNext())
		{
			modifiedSentence = new StringBuffer(sentence);
			Pair< Pair<String, Pair <Integer, Integer>>, Pair<String, Pair <Integer, Integer>>> pair = it.next();
			Pair<String, Pair <Integer, Integer>> left = pair.getLeft();
			Pair<String, Pair <Integer, Integer>> right = pair.getRight();

			String term1 = left.getLeft();
			Pair<Integer, Integer> coordinatesT1 = left.getRight();
			Integer b1 = coordinatesT1.getLeft();
			Integer e1 = coordinatesT1.getRight();

			String term2 = right.getLeft();
			Pair<Integer, Integer> coordinatesT2 = right.getRight();
			Integer b2 = coordinatesT2.getLeft();
			Integer e2 = coordinatesT2.getRight();

			if (b1 < b2) {
				modifiedSentence.replace(b1, e1, upperCaseTerm(term1));
				modifiedSentence.replace(b2+2, e2+2, upperCaseTerm(term2));
				fileRow = index + "###" + relation + "###" + upperCaseTerm(term1) + "###" + b1 + "###" + (e1 + 2) + "###" + upperCaseTerm(term2) + "###" + (b2 + 2) + "###" + (e2 + 4) + "###" + modifiedSentence.toString();
				String[] fileRowArray = fileRow.split("###");
				writer.writeNext(fileRowArray);
			}
			else {
				modifiedSentence.replace(b2, e2, upperCaseTerm(term2));
				modifiedSentence.replace(b1+2, e1+2, upperCaseTerm(term1));
				fileRow = index + "###" + relation + "###" + upperCaseTerm(term1) + "###" + (b1 + 2) + "###" + (e1 + 4) + "###" + upperCaseTerm(term2) + "###" + b2 + "###" + (e2 + 2) + "###" + modifiedSentence.toString();
				String[] fileRowArray = fileRow.split("###");
				writer.writeNext(fileRowArray);
			}
			index ++;

		}
		return fileRow;
	}

	public String removeSquareBrackets(String word) {
		String value = null;
		if (word.substring(0, 1).equals("[")) {
			value = word.replace("[", "");
			value = value.replace("]", "");
		//	value = word.substring(1, word.length() - 1);
		}
		else {
			value = word;
		}
		return value;
	}

	public String upperCaseTerm(String lowerCase) {
		String value = lowerCase.toUpperCase();
		value = "[" + value + "]";
		return value;
	}
	
	public String removeNewLines(String sentence) {
		System.out.println("intra");
		String value = sentence.replace("\n", "");
		value = value.replace("\r\n", "");
		return value;
	}

	public String modifySentence(String sentence, String term1, String term2) {
		String value = sentence.replace(term2, removeSquareBrackets(term2));
		value = value.replace(term1, removeSquareBrackets(term1));
		value = value.replace(removeSquareBrackets(term1), removeSquareBrackets(term1).toLowerCase());
		value = value.replace(removeSquareBrackets(term2), removeSquareBrackets(term2).toLowerCase());
		value = value.replace("[", "");
		value = value.replace("]", "");
		return value;
	}

	public ArrayList<Pair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>>> extractOccurrences(String sentence, String term1, String term2) {
		ArrayList<Pair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>>> list = new ArrayList<Pair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>>>();
		Pattern p1 = Pattern.compile(Pattern.quote(term1));
		Pattern p2 = Pattern.compile(Pattern.quote(term2));
		Matcher m1 = p1.matcher(Matcher.quoteReplacement(sentence.toLowerCase()));
		Matcher m2 = p2.matcher(Matcher.quoteReplacement(sentence.toLowerCase()));
		Pair<Integer, Integer> coordinatesTerm1 = null;
		Pair<Integer, Integer> coordinatesTerm2 = null;
		Pair<String, Pair<Integer, Integer>> termCoord1 = null;
		Pair<String, Pair<Integer, Integer>> termCoord2 = null;
		ImmutablePair<Pair<String, Pair<Integer, Integer>>, Pair<String, Pair<Integer, Integer>>> pair = null;
		boolean equals = false;

		if (term1.equals(term2)) {
			equals = true;
		}

		while (m1.find()) {
			coordinatesTerm1 = new ImmutablePair<Integer, Integer>(m1.start(), m1.end());
			termCoord1 = new ImmutablePair<String, Pair<Integer,Integer>>(m1.group(), coordinatesTerm1);
			if (m2.find(0) == true) {
				if (m2.start() == m1.start()) {
					continue;
				}
				coordinatesTerm2 = new ImmutablePair<Integer, Integer>(m2.start(), m2.end());
				termCoord2 = new ImmutablePair<String, Pair<Integer,Integer>>(m2.group(), coordinatesTerm2);
				pair = new ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>>(termCoord1, termCoord2);
				if (!listContainsPair(pair, equals)) {
					if (m1.start() > m2.end() || m2.start() > m1.end()) {
						list.add(new ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>>(termCoord1, termCoord2));
						globalList.add(new ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>>(termCoord1, termCoord2));
					}
				}

				while (m2.find()) {
					if (m2.start() == m1.start()) {
						continue;
					}
					coordinatesTerm2 = new ImmutablePair<Integer, Integer>(m2.start(), m2.end());
					termCoord2 = new ImmutablePair<String, Pair<Integer,Integer>>(m2.group(), coordinatesTerm2);
					pair = new ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>>(termCoord1, termCoord2);
					if (!listContainsPair(pair, equals)) {
						if (m1.start() > m2.end() || m2.start() > m1.end()) {
							list.add(new ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>>(termCoord1, termCoord2));
							globalList.add(new ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>>(termCoord1, termCoord2));
						}
					}
				}
			}
		}
		return list;
	}


	private boolean listContainsPair(ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>> pair, boolean equals) {

		for (int i = 0; i < globalList.size(); ++ i) {
			ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>> crntPair = (ImmutablePair<Pair<String,Pair<Integer,Integer>>, Pair<String,Pair<Integer,Integer>>>)globalList.get(i);
			if (crntPair.left.equals(pair.left) && crntPair.right.equals(pair.right)) {
				return true;
			}
			if (equals == true) {
				if (crntPair.right.equals(pair.left) && crntPair.left.equals(pair.right)) {
					return true;
				}
			}
		}
		return false;
	}
}
