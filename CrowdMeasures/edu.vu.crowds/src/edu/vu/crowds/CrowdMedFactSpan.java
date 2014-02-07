package edu.vu.crowds;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.filters.PassAll;
import edu.vu.crowds.analysis.filters.StdevMRCBelowMean;
import edu.vu.crowds.analysis.filters.StdevMagBelowMean;
import edu.vu.crowds.analysis.filters.StdevNormMagBelowMean;
import edu.vu.crowds.analysis.filters.StdevNormRelMagBelowMean;
import edu.vu.crowds.analysis.filters.StdevNormRelMagByAllBelowMean;
import edu.vu.crowds.analysis.sentences.AggregateSentenceMeasure;
import edu.vu.crowds.analysis.sentences.SentenceFilter;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.MeanMaxRelationCosine;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedRelationMagnitudeByAll;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevMaxRelationCosine;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedRelationMagnitudeByAll;
import edu.vu.crowds.analysis.sentences.measures.Magnitude;
import edu.vu.crowds.analysis.sentences.measures.MaxRelationCosine;
import edu.vu.crowds.analysis.sentences.measures.NormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitudeByAll;
import edu.vu.crowds.analysis.sentences.measures.NumAnnotators;
import edu.vu.crowds.analysis.workers.AnnotsPerSent;
import edu.vu.crowds.analysis.workers.AvgWorkerAgreement;
import edu.vu.crowds.analysis.workers.FactorSelectionCheck;
import edu.vu.crowds.analysis.workers.NumberOfSents;
import edu.vu.crowds.analysis.workers.WorkerCosine;
import edu.vu.crowds.analysis.workers.WorkerMeasure;

public class CrowdMedFactSpan extends CrowdTruth {
	
	protected int argNo;
	
	final static int B1 = 12;
	final static int B2 = 13;
	final static int E1 = 18;
	final static int E2 = 19;
	final static int SENTENCE = 28;
	final static int TERM1 = 20;
	final static int TERM2 = 21;
	final static int USER_TERM1 = 22;
	final static int USER_TERM2 = 27;
	final static int USER_CHECK_TERM1 = 14;
	final static int USER_CHECK_TERM2 = 17;
	final static int USER_DECISION1 = 23;
	final static int USER_DECISION2 = 24;
	

	public CrowdMedFactSpan(String filename) {
		measures = new SentenceMeasure[] {
				new Magnitude(),
				new NormalizedMagnitude(),
				new NormalizedRelationMagnitude(),
				new NormalizedRelationMagnitudeByAll(),
				new MaxRelationCosine(),
				new NumAnnotators(),
		};
		aggregates = new AggregateSentenceMeasure[] {
				new MeanMagnitude(), 
				new StdDevMagnitude(),
				new MeanNormalizedMagnitude(),
				new StdDevNormalizedMagnitude(),
				new MeanNormalizedRelationMagnitude(),
				new StdDevNormalizedRelationMagnitude(),
				new MeanNormalizedRelationMagnitudeByAll(),
				new StdDevNormalizedRelationMagnitudeByAll(),
				new MeanMaxRelationCosine(),
				new StdDevMaxRelationCosine(),
		};
		filters = new SentenceFilter[] {
				new PassAll(),
				new StdevMagBelowMean(),
				new StdevNormMagBelowMean(),
				new StdevNormRelMagBelowMean(),
				new StdevNormRelMagByAllBelowMean(),
				new StdevMRCBelowMean(),
				//			new BelowMean()
		};
		workMeasures = new WorkerMeasure[] {
				new NumberOfSents(),
				new WorkerCosine(),
				new AvgWorkerAgreement(),
				new AnnotsPerSent(),
				new FactorSelectionCheck(),
		};
		
		vectorIndex.put("WORD_-3", 0);
		vectorIndex.put("WORD_-2", 1);
		vectorIndex.put("WORD_-1", 2);
		vectorIndex.put("WORD_+1", 3);
		vectorIndex.put("WORD_+2", 4);
		vectorIndex.put("WORD_+3", 5);
		vectorIndex.put("WORD_OTHER", 6);
		vectorIndex.put("NIL", 7);
		vectorIndex.put("CHECK_FAILED", 8);
		
		try {
			init(new File(filename));
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	@Override
	public void init(File f) throws IOException {
		MakeIndexMap(measures,measureIndex);
		MakeIndexMap(filters,filterIndex);
		MakeIndexMap(aggregates,aggIndex);
		MakeIndexMap(workMeasures,workerMeasureIndex);		
		char splitChar = getSplitCharFromFilename(f.getName(), SPLIT_CHAR.charAt(0));
	
		BufferedReader r= new BufferedReader(new FileReader(f));
		this.header = parseCsvLine(r, r.readLine(), splitChar);
		if (this.header.size() != this.getNumCols()) {
			//System.err.println("Columns in header: " + this.header.size());
			//System.err.println("Columns in function: " + this.getNumCols());
			System.err.println("Number of columns ("+ this.header.size() + ") doesn't match expected");
		}
		
		for (String l = r.readLine(); l != null; l=r.readLine()) {
			ArrayList<String> lineArray = parseCsvLine(r,l,splitChar);
			String workId = this.getWorkId(lineArray);
//			System.out.println(lineArray);
			
			argNo = 1;
			String sentId1 = this.getSentId(lineArray);
			Set<String> annotSet = this.getAnnots(lineArray);
			if (annotSet == null) {
				System.err.println("Unable to process: " + lineArray);
				continue; // don't process lines with bad data
			}
			
			argNo = 2;
			String sentId2 = this.getSentId(lineArray);
			Set<String> annotSet2 = this.getAnnots(lineArray);
			if (annotSet2 == null) {
				System.err.println("Unable to process: " + lineArray);
				continue; // don't process lines with bad data
			}
			
//			System.out.print(sentId+","+workId);
//			for (String s : annotSet) System.out.print("," + s);
//			System.out.println();

			if (!sentsMap.containsKey(sentId1)) {
				sentsMap.put(sentId1, lineArray);	
			}
			if (!sentsMap.containsKey(sentId2)) {
				sentsMap.put(sentId2, lineArray);	
			}
			Map<String,Set<String>> workerSents = workerSentAnnot.get(workId);
			if (workerSents == null) {
				workerSents = new HashMap<String,Set<String>>();
				workerSentAnnot.put(workId, workerSents);
			}
			
			if (workerSents.containsKey(sentId1)) {
//				System.err.println("Worker " + workId + " annotated sentence " + sentId + " more than once");
			} else {
				workerSents.put(sentId1, annotSet);
			}
			if (workerSents.containsKey(sentId2)) {
//				System.err.println("Worker " + workId + " annotated sentence " + sentId + " more than once");
			} else {
				workerSents.put(sentId2, annotSet2);
			}
		}
		r.close();
	}

	@Override
	protected void printWorkerMeasures(File f) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);
		
		out.print("Worker ID");
		List<SentenceFilter> filterList = getMeasuresByIndex(filterIndex);
		for (int i=0; i<filters.length; i++) {
			out.print("," + filterList.get(i).label());
			for (int j=1; j<workMeasures.length; j++) {
				out.print(",");
			}
		}
		out.println();
		List<WorkerMeasure> measureList = getMeasuresByIndex(workerMeasureIndex);
		for (int i=0; i<filters.length; i++) {
			for (int j=0; j<workMeasures.length; j++) {
				out.print("," + measureList.get(j).label());
			}
		}
		out.println(",Filtered");
		
		for (String workid : workerMeasures.keySet()) {
			out.print(workid);
			for (int i=0; i<filters.length; i++) {
				for (int j=0; j<workMeasures.length; j++) {
					Map<Integer,Instance> w = workerMeasures.get(workid);
					int index = filterIndex.get(filterList.get(i));
					Instance in = w.get(index);
					index = workerMeasureIndex.get(measureList.get(j));
					Double val = in.get(index);	
					
					out.print(",");
					if (!val.isNaN()) out.print(val);
				}
			}
			out.println( (this.isFilteredWorker(workid) ? ",1" : ",0") );
		}
		out.println();
	}
	
	@Override	
	protected void printSentenceMeasures(File f, boolean printVectors) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);
		
		String sep = ",";
		if (f.getName().endsWith(".tsv")) sep="\t";
	
		int[] origCols =      { B1,  B2,  E1,  E2,  SENTENCE,  TERM1,  TERM2};
		String[] origLabels = {"b1","b2","e1","e2","sentence","term1","term2"};
		
		out.print("Sent id");
		if (printVectors) {
			for (int i=0; i<vectorIndex.size();i++) {
				for (String label : vectorIndex.keySet()) {
					if (vectorIndex.get(label) == i) out.print(sep+label);
				}
			}
		}
		out.print(sep+"MaxRelCos" + sep + "NumAnnots");
		for (int i=0;i<origCols.length;i++) out.print(sep+origLabels[i]);
		out.println();
		
		for (String sentid : sentSumVectors.keySet()) {
			out.print(sentid+sep);
			if (printVectors) {
				Instance sumVector = sentSumVectors.get(sentid);
				MaxRelationCosine relCos = (MaxRelationCosine) measures[4];
				for (int rel = 0; rel<sumVector.keySet().size(); rel++) {
					out.print(relCos.relationCosine(sumVector, rel)+sep);
				}
			}
			
			Instance meas = sentMeasures.get(sentid);
			out.print(meas.get(4)+sep+meas.get(5));
			
			ArrayList<String> sentInput = sentsMap.get(sentid);
			for (int i=0;i<origCols.length;i++) {
				String content = sentInput.get(origCols[i]);
				try {
					Integer.decode(content);
					out.print(sep + content);
				} catch (NumberFormatException e) {
					out.print(sep + "\"" + content.replaceAll("\"", "") + "\""); // quote strings in case they contain commas
				}
			}
			out.println();
		}
	}
	
	protected static List<String> addSeparators(List<String> terms, boolean isLeft) {
		String[] separators = {" ", "-"};
		List<String> newV = new ArrayList<String>(terms);
		//newV.add("");
		for (String tl : terms) {
			if (tl.compareTo("") != 0) {
				for (int i = 0; i < separators.length; i++) {
					if (isLeft) tl += " ";
					else tl = " " + tl;
					newV.add(tl);
				}
			}
		}
		return newV;
	}
	
	protected void printRelEx(File f) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);
		
		String sep = ",";
		if (f.getName().endsWith(".tsv")) sep="\t";
		
		//int[] origCols = {12,13,18,19,28,39,40};
		String[] origLabels = {"Sent_id","term1","term2","b1","b2","e1","e2","sentence"};
		
		for (int i=0;i<origLabels.length - 1;i++) out.print(origLabels[i] + sep);
		out.print(origLabels[origLabels.length - 1]);
		out.println();
		
		Map<String, ArrayList<String> > sentTerm1Set = new HashMap<String, ArrayList<String> >();
		Map<String, ArrayList<String> > sentTerm2Set = new HashMap<String, ArrayList<String> >();
		
		for (String sentid : sentSumVectors.keySet()) {
			ArrayList<String> sentInput = sentsMap.get(sentid);
			String sentence = sentInput.get(SENTENCE);
			String[] argCompVec = new String[7];
			String term;
			if (sentid.endsWith("1")) {
				int arg1Beg = Integer.decode(sentInput.get(B1));
				int arg1End = Integer.decode(sentInput.get(E1));
				term = sentInput.get(TERM1);
				//String term1 = sentence.substring(arg1Beg, arg1End+1);
				argCompVec = getCompVector(1, sentence, arg1Beg, arg1End, term, false);
			}
			else {
				int arg2Beg = Integer.decode(sentInput.get(B2));
				int arg2End = Integer.decode(sentInput.get(E2));
				term = sentInput.get(TERM2);
				argCompVec = getCompVector(1, sentence, arg2Beg, arg2End, term, false);
			}
			
			//out.print(sentid+sep);
			
			Instance sumVector = sentSumVectors.get(sentid);
			MaxRelationCosine relCos = (MaxRelationCosine) measures[4];
			List<String> leftVersions = new ArrayList<String>();
			leftVersions.add("");
			for (int rel = 0; rel < 3; rel++) {
				//out.print(relCos.relationCosine(sumVector, rel)+sep);
				String termLeft = "";
				if (relCos.relationCosine(sumVector, rel) >= .4f) {
					// System.out.println(sentid + ": " + rel + " = " + relCos.relationCosine(sumVector, rel));
					for (int i = rel; i < 2; i++) {
						termLeft += argCompVec[i] + " ";
					}
					termLeft += argCompVec[2];
				}
				if (termLeft.compareTo("") != 0) {
					// System.out.println(termLeft);
					leftVersions.add(termLeft);
				}
			}
			
			List<String> rightVersions = new ArrayList<String>();
			rightVersions.add("");
			for (int rel = 5; rel > 2; rel--) {
				//out.print(relCos.relationCosine(sumVector, rel)+sep);
				String termRight = "";
				if (relCos.relationCosine(sumVector, rel) >= .4f) {
					// System.out.println(sentid + ": " + rel + " = " + relCos.relationCosine(sumVector, rel));
					for (int i = 3; i < rel; i++) {
						termRight += argCompVec[i + 1] + " ";
					}
					termRight += argCompVec[rel + 1];
				}
				if (termRight.compareTo("") != 0) {
					//System.out.println(termRight);
					rightVersions.add(termRight);
				}
			}
			
			leftVersions = addSeparators(leftVersions, true);
			rightVersions = addSeparators(rightVersions, false);
			
			for (String tl : leftVersions) {
				for (String tr : rightVersions) {
					String newTerm = "";
					if (tl.compareTo("") != 0) newTerm += tl;
					newTerm += argCompVec[3];
					if (tr.compareTo("") != 0) newTerm += tr;
					
					if (sentence.indexOf(newTerm) != -1) {
						// System.out.println(newTerm.toUpperCase() + ": " + sentence.indexOf(newTerm));
						String trimSentId = sentid.substring(0, sentid.length() - 3);
						
						if (sentid.endsWith("1")) { 
							if (sentTerm1Set.containsKey(trimSentId)) {
								sentTerm1Set.get(trimSentId).add(newTerm);
							}
							else {
								List<String> aux = new ArrayList<String>();
								aux.add(newTerm);
								sentTerm1Set.put(trimSentId, (ArrayList<String>) aux);
							}
						}
						else {
							if (sentTerm2Set.containsKey(trimSentId)) {
								sentTerm2Set.get(trimSentId).add(newTerm);
							}
							else {
								List<String> aux = new ArrayList<String>();
								aux.add(newTerm);
								sentTerm2Set.put(trimSentId, (ArrayList<String>) aux);
							}
						}
					}
				}
			}
		}
		
		for (String sen : sentTerm1Set.keySet()) {
			int newSenID = 1;
			for (String t1 : sentTerm1Set.get(sen)) {
				for (String t2 : sentTerm2Set.get(sen)) {
					//System.out.println(sen + ": [" + t1.toUpperCase() + "] --- [" + t2.toUpperCase() + "]");
					
					String sentence = sentsMap.get(sen+"-T1").get(SENTENCE);
					Integer oldB1 = Integer.decode(sentsMap.get(sen+"-T1").get(B1));
					Integer oldB2 = Integer.decode(sentsMap.get(sen+"-T1").get(B2));
					Integer oldE1 = Integer.decode(sentsMap.get(sen+"-T1").get(E1));
					Integer oldE2 = Integer.decode(sentsMap.get(sen+"-T1").get(E2));
					
					int b1 = 0;
					int e1 = 0;
					while (b1 > oldB1 || e1 < oldE1 ) {
							b1 = sentence.indexOf(t1, b1);
							e1 = b1 + t1.length();
					}
					
					int b2 = 0;
					int e2 = 0;
					while (b2 > oldB2 || e2 < oldE2 ) {
							b2 = sentence.indexOf(t2, b2);
							e2 = b2 + t2.length();
					}
					
					if ((b1 >= b2 && b1 <= e2) || (b2 >= b1 && b2 <= e1)) {
						System.err.println("Overlapping Terms (ID = " + sen 
								+ "): [" + t1.toUpperCase() + "] --- [" + t2.toUpperCase() + "]");
					}
					else {
						String capT1 = "[" + t1.toUpperCase() + "]";
						String capT2 = "[" + t2.toUpperCase() + "]";
						
						if (b1 < b2)
							sentence = sentence.substring(0, b1) + capT1 + sentence.substring(e1, b2) +
							capT2 + sentence.substring(e2, sentence.length());
						else
							sentence = sentence.substring(0, b2) + capT2 + sentence.substring(e2, b1) +
							capT1 + sentence.substring(e1, sentence.length());
						
						out.print(sen + "-FS" + newSenID + sep + "\"" + capT1.replaceAll("\"", "") + "\"" +
								sep + "\"" + capT2.replaceAll("\"", "") + "\"" +
								sep + b1 + sep + b2 + sep 
								+ e1 + sep + e2 +
								sep + "\"" + sentence.replaceAll("\"", "") + "\"");
						out.println();
						
						newSenID++;				
					}
				}
			}
		}
	}

	
	protected static String[] getCompVector(int an, String sentence, int argBeg, int argEnd, String term, boolean noPunct) {
		String[] argCompVec = new String[7];
		for (int i = 0; i < 7; i++) {
			argCompVec[i] = "";
		}
		
		String argLeft = sentence.substring(0, argBeg);
		if (noPunct) argLeft = argLeft.replaceAll("[^a-zA-Z ]", "").toLowerCase();
		String[] argLeftWords = argLeft.split(" ");
		
		List<String> list = new ArrayList<String>(Arrays.asList(argLeftWords));
		list.removeAll(Arrays.asList(""));
		argLeftWords = list.toArray(new String[0]);
		
		for (int i = 0; i < 3 && argLeftWords.length - i - 1 >= 0; i++) {
			argCompVec[2 - i] = argLeftWords[argLeftWords.length - i - 1];
		}
		
		argCompVec[3] = term;
		
		if (argEnd+1 < sentence.length() - 1) {

			String argRight = sentence.substring(argEnd+1, sentence.length() - 1);
			if (noPunct) argRight = argRight.replaceAll("[^a-zA-Z ]", "").toLowerCase();
			String[] argRightWords = argRight.split(" ");
			
			// change term to plural form for comparison purposes
			if (argRightWords[0].compareTo("s") == 0) {
				argCompVec[3] += "s";
			}
			if (argRightWords[0].compareTo("es") == 0) {
				argCompVec[3] += "es";
			}
			
			list = new ArrayList<String>(Arrays.asList(argRightWords));
			list.removeAll(Arrays.asList(""));
			// remove leftover plural forms
			list.removeAll(Arrays.asList("s"));
			list.removeAll(Arrays.asList("es"));
			argRightWords = list.toArray(new String[0]);
			
			for (int i = 0; i < 3 && i < argRightWords.length; i++) {
				argCompVec[i + 4] = argRightWords[i];
			}
		}
		
		/* if (noPunct == true) {
		 System.err.println(an + ": " + argCompVec[0] + " + " + argCompVec[1] + " + " + argCompVec[2] + " + " 
		 + argCompVec[3] + " + " + argCompVec[4] + " + " + argCompVec[5] + " + " + argCompVec[6]);
		}*/
		
		return argCompVec;
	}
	
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		String sentence = lineArray.get(SENTENCE);
		Set<String> annots = new HashSet<String>();
		String[] argCompVec = new String[7];
		String argDecision;
		String userAnswer = "";
		String term = "";
		//System.err.println(sentence);
		
		if (argNo == 1) {
			argDecision = lineArray.get(USER_DECISION1);
			if (argDecision.compareTo("no") == 0) {
				userAnswer = lineArray.get(USER_TERM1).replaceAll("[^a-zA-Z ]", "").toLowerCase();
			}
			else {
				userAnswer = lineArray.get(USER_CHECK_TERM1).replaceAll("[^a-zA-Z ]", "").toLowerCase();
			}
				
			int arg1Beg = Integer.decode(lineArray.get(B1));
			int arg1End = Integer.decode(lineArray.get(E1));
			term = lineArray.get(TERM1);
			//String term1 = sentence.substring(arg1Beg, arg1End+1);
			argCompVec = getCompVector(1, sentence, arg1Beg, arg1End, term, true);
		}
		else {
			argDecision = lineArray.get(USER_DECISION2);
			if (argDecision.compareTo("no") == 0) {
				userAnswer = lineArray.get(USER_TERM2).replaceAll("[^a-zA-Z ]", "").toLowerCase();
			}
			else {
				userAnswer = lineArray.get(USER_CHECK_TERM2).replaceAll("[^a-zA-Z ]", "").toLowerCase();
			}
				
			int arg2Beg = Integer.decode(lineArray.get(B2));
			int arg2End = Integer.decode(lineArray.get(E2));
			term = lineArray.get(TERM2);
			//String term2 = sentence.substring(arg2Beg, arg2End);
			argCompVec = getCompVector(2, sentence, arg2Beg, arg2End, term, true);
		}
		
		
		if (argDecision.compareTo("yes") == 0) {
			argCompVec[3] = argCompVec[3].replaceAll("[^a-zA-Z ]", "").toLowerCase().trim();
			userAnswer = userAnswer.replaceAll("[^a-zA-Z ]", "").toLowerCase();
			if (userAnswer.compareTo(argCompVec[3]) == 0) {
				annots.add("NIL");
			}
			else {
				annots.add("CHECK_FAILED");
				// System.out.println(argDecision + " : " + userAnswer + " - " + argCompVec[3] + " / " + term );
				 //System.out.println(userAnswer + " - " + term);
			}
		}
		else {
			int[] vec = new int[7];
			int wordsInRange = 0;
			for (int i = 0; i < 7; i++) vec[i] = 0;
			String[] userWords = userAnswer.split(" ");
			
			// remove main term
			List<String> list = new ArrayList<String>(Arrays.asList(userWords));
			String[] factorWords = argCompVec[3].replaceAll("[^a-zA-Z ]", "").split(" ");
			//System.out.println("BEFORE:" + argCompVec[3] + " "  + list.toString());
			for (int i = 0; i < factorWords.length; i++) 
				list.remove(factorWords[i]);
			//System.out.println("AFTER:" + argCompVec[3] + " "  +list.toString());
			userWords = list.toArray(new String[0]);
			
			
			for (int i = 0; i < 3; i++) {
				boolean found = false;
				for (int j = 0; j < userWords.length && found != true; j++) {
					if (userWords[j].compareTo(argCompVec[i]) == 0) found = true;
				}
				if (found == true) {
					int index = 3 - i;
					//annots.add(argCompVec[i]);
					annots.add("WORD_-" + index);
					// vectorIndex.put("WORD_-" + index,vectorIndex.size());
					
					wordsInRange++;
				}
				else {
					// annots.add("");
				}
			}

			for (int i = 4; i < 7; i++) {
				boolean found = false;
				for (int j = 0; j < userWords.length && found != true; j++) {
					if (userWords[j].compareTo(argCompVec[i]) == 0) found = true;
				}
				if (found == true) {
					//annots.add(argCompVec[i]);
					
					int index = i - 3;
					annots.add("WORD_+" + index);
					// vectorIndex.put("WORD_+" + index,vectorIndex.size());
					
					wordsInRange++;
				}
				else {
					// annots.add("");
				}
			}
			
			String otherWords = "";
			if (wordsInRange < userWords.length) {
				for (int i = 0; i < userWords.length; i++) {
					if (!argCompVec[3].contains(userWords[i]) && 
							!Arrays.asList(argCompVec).contains(userWords[i]))
						otherWords += userWords[i] + " ";
				}
			}
			
			//System.err.println(otherWords);
			
			if (otherWords.compareTo("") != 0) {
				//annots.add(otherWords);
				annots.add("WORD_OTHER");
				// vectorIndex.put("WORD_OTHER",vectorIndex.size());
			}
			
			
			if (annots.size() == 0) {
				//System.out.println("FAILED: " + userAnswer + " :-: " + argCompVec[3] + " -> " + sentence);
				annots.add("CHECK_FAILED");
			}
		}
		
		//System.err.println(userAnswer);
		//System.err.println(annots.toString());
		
		return annots;
	}

	@Override
	protected String getWorkId(ArrayList<String> lineArray) {
		// TODO Auto-generated method stub
		//System.out.println("worker: " + lineArray.get(7));
		return lineArray.get(7);
	}

	@Override
	protected String getSentId(ArrayList<String> lineArray) {
		// TODO Auto-generated method stub
		return lineArray.get(35) + "-T" + argNo;
	}

	@Override
	protected Integer getNumCols() {
		return 43;
	}

	@Override
	protected boolean isFilteredWorker(String workid) {
		List<SentenceFilter> filterList = getMeasuresByIndex(filterIndex);
		List<WorkerMeasure> measureList = getMeasuresByIndex(workerMeasureIndex);
		int findex = filterIndex.get(filterList.get(5)); // the MRC<STDEV sentence filter
		Map<Integer,Instance> w = workerMeasures.get(workid);
		Instance measures = w.get(findex);
		Double numSents = measures.get(workerMeasureIndex.get(measureList.get(0)));
		
		// System.err.println("WORKER: " + workid + ", JUDGEMENTS: " + numSents);
		if (numSents < 2) return true; // too few annots
//		Integer idx = vectorIndex.get(GS_FAIL);
//		if (idx != null) { // if at least one person failed a GS test
//			Map<String, Instance> workerSents = workers.get(workid);
//			for (Instance annots : workerSents.values()) {
//				if(annots.get(idx) > 0) return true; // this guy failed a GS test
//			}	
//		}		
		Double checkFailed = measures.get(workerMeasureIndex.get(measureList.get(4)));
		if (checkFailed > .3f) {
			//System.out.println( workid + " failed");
			return true; // this guy failed a GS test
		}
		// System.err.println("PASSED GS");
		
		Double agree = measures.get(workerMeasureIndex.get(measureList.get(2)));
		//if (agree < .3f) return true; //very disagreeable worker
		// System.err.println("PASSED AGREEMENT");

		Double cos = measures.get(workerMeasureIndex.get(measureList.get(1)));  
		if (cos > .4) return true; // does not appear to have signal for this task
		// System.err.println("PASSED TASK SIGNAL");
		
		return false;
	}
	
	public static void main(String[] args) {
		// Process first factor
		CrowdMedFactSpan c1 = new CrowdMedFactSpan(args[0]);
		c1.buildConfusionMatrix();
		c1.buildSentenceClusters();
		c1.computeSentenceMeasures();
		c1.computeAggregateSentenceMeasures();
		c1.computeSentenceFilters();
		c1.computeWorkerMeasures();
		try {
			c1.printWorkerMeasures(new File(args[1]));
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		c1.filterWorkers();
		c1.buildConfusionMatrix();
		c1.buildSentenceClusters();
		c1.computeSentenceMeasures();
		c1.computeAggregateSentenceMeasures();
		try {
			c1.printSentenceMeasures(new File(args[2]),true);
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		try {
			c1.printRelEx(new File(args[3]));
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

}
