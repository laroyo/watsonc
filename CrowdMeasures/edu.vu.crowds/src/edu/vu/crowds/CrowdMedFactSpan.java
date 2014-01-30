package edu.vu.crowds;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintStream;
import java.util.ArrayList;
import java.util.Arrays;
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

	public CrowdMedFactSpan(String filename, int an) {
		argNo = an;
		
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
	
		int[] origCols = {12,13,18,19,29,28,39,40};
		String[] origLabels = {"b1","b2","e1","e2","index", "sentence","term1","term2"};
		
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
//		for (int i=0; i<measureIndex.size();i++) out.print(sep+measures[i].label());
//		for (int i=0; i<filterIndex.size();i++) out.print(sep+filters[i].label());
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
//			out.println(JavaMlUtils.instanceString(sentMeasures.get(sentid))+sep+
//					JavaMlUtils.instanceString(sentFilters.get(sentid)));
		}
	
//		out.println();
//		for (int i=0; i<aggIndex.size();i++) out.print(sep+aggregates[i].label());
//		out.println();
//		for (int i=0; i<aggIndex.size();i++) out.print(sep+aggMeasures.get(i));
//		out.println();
	}

	
	protected static String[] getCompVector(int an, String sentence, int argBeg, int argEnd, String term) {
		String[] argCompVec = new String[7];
		for (int i = 0; i < 7; i++) {
			argCompVec[i] = "";
		}
		
		String argLeft = sentence.substring(0, argBeg).replaceAll("[^a-zA-Z ]", "").toLowerCase();
		String[] argLeftWords = argLeft.split(" ");
		
		List<String> list = new ArrayList<String>(Arrays.asList(argLeftWords));
		list.removeAll(Arrays.asList(""));
		argLeftWords = list.toArray(new String[0]);
		
		for (int i = 0; i < 3 && argLeftWords.length - i - 1 >= 0; i++) {
			argCompVec[2 - i] = argLeftWords[argLeftWords.length - i - 1];
		}
		
		argCompVec[3] = term;
		
		if (argEnd+1 < sentence.length() - 1) {
			String argRight = sentence.substring(argEnd+1, sentence.length() - 1).replaceAll("[^a-zA-Z ]", "").toLowerCase();
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
		
		// System.out.println(an + ": " + argCompVec[0] + " + " + argCompVec[1] + " + " + argCompVec[2] + " + " 
		// + argCompVec[3] + " + " + argCompVec[4] + " + " + argCompVec[5] + " + " + argCompVec[6]);
		
		return argCompVec;
	}
	
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		String sentence = lineArray.get(38);
		Set<String> annots = new HashSet<String>();
		String[] argCompVec = new String[7];
		String argDecision;
		String userAnswer = "";
		String term = "";
		//System.err.println(sentence);
		
		if (argNo == 1) {
			argDecision = lineArray.get(23);
			if (argDecision.compareTo("no") == 0) {
				//System.out.println(arg1Decision + " " + lineArray.get(7));
				userAnswer = lineArray.get(22).replaceAll("[^a-zA-Z ]", "").toLowerCase();
				
				int arg1Beg = Integer.decode(lineArray.get(12));
				int arg1End = Integer.decode(lineArray.get(18));
				term = lineArray.get(20);
				//String term1 = sentence.substring(arg1Beg, arg1End+1);
				argCompVec = getCompVector(1, sentence, arg1Beg, arg1End, term);
			}
			else {
				userAnswer = lineArray.get(14).replaceAll("[^a-zA-Z ]", "").toLowerCase();
				term = lineArray.get(20).replaceAll("[^a-zA-Z ]", "").toLowerCase();
				//System.out.println(userAnswer + " - " + term);
			}
		}
		else {
			argDecision = lineArray.get(24);
			if (argDecision.compareTo("no") == 0) {
				//System.out.println(arg2Decision + " " + lineArray.get(7));
				userAnswer = lineArray.get(27).replaceAll("[^a-zA-Z ]", "").toLowerCase();
				
				int arg2Beg = Integer.decode(lineArray.get(13));
				int arg2End = Integer.decode(lineArray.get(19));
				term = lineArray.get(21);
				//String term2 = sentence.substring(arg2Beg, arg2End);
				argCompVec = getCompVector(2, sentence, arg2Beg, arg2End, term);
			}
			else {
				userAnswer = lineArray.get(16).replaceAll("[^a-zA-Z ]", "").toLowerCase();
				term = lineArray.get(21).replaceAll("[^a-zA-Z ]", "").toLowerCase();
				//System.out.println(userAnswer + " - " + term);
			}
		}
		
		
		if (argDecision.compareTo("yes") == 0) {
			if (userAnswer.compareTo(term) == 0) {
				annots.add("NIL");
			}
			else {
				annots.add("CHECK_FAILED");
				//System.out.println(userAnswer + " - " + term);
			}
		}
		else {
			int[] vec = new int[7];
			int wordsInRange = 0;
			for (int i = 0; i < 7; i++) vec[i] = 0;
			String[] userWords = userAnswer.split(" ");
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
			
			//System.err.println(userAnswer);
			//System.err.println(annots.toString());
		}
		
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
		return lineArray.get(35);
	}

	@Override
	protected Integer getNumCols() {
		// TODO Auto-generated method stub
		return 41;
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
		// if (numSents < 2) return true; // too few annots
//		Integer idx = vectorIndex.get(GS_FAIL);
//		if (idx != null) { // if at least one person failed a GS test
//			Map<String, Instance> workerSents = workers.get(workid);
//			for (Instance annots : workerSents.values()) {
//				if(annots.get(idx) > 0) return true; // this guy failed a GS test
//			}	
//		}		
		Double checkFailed = measures.get(workerMeasureIndex.get(measureList.get(4)));
		if (checkFailed > .1f) {
			//System.out.println( workid + " failed");
			return true; // this guy failed a GS test
		}
		// System.err.println("PASSED GS");
		
		Double agree = measures.get(workerMeasureIndex.get(measureList.get(2)));
	//	if (agree < .6f) return true; //very disagreeable worker
		// System.err.println("PASSED AGREEMENT");

		Double cos = measures.get(workerMeasureIndex.get(measureList.get(1)));  
		if (cos > .4) return true; // does not appear to have signal for this task
		// System.err.println("PASSED TASK SIGNAL");
		
		return false;
	}
	
	public static void main(String[] args) {
		// Process first factor
		CrowdMedFactSpan c1 = new CrowdMedFactSpan(args[0], 1);
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
		

		// Process second factor
		/*CrowdMedFactSpan c2 = new CrowdMedFactSpan(args[0], 2);
		//c.getAnnots();
		c2.buildConfusionMatrix();
		c2.buildSentenceClusters();
		c2.computeSentenceMeasures();
		c2.computeAggregateSentenceMeasures();
		c2.computeSentenceFilters();
		c2.computeWorkerMeasures();
		// c2.printWorkerMeasures(new File(args[1]));
//		c2.printSentenceMeasures(new File(args[2]),true);
		c2.filterWorkers();
		c2.buildConfusionMatrix();
		c2.buildSentenceClusters();
		c2.computeSentenceMeasures();
		c2.computeAggregateSentenceMeasures();
		// c2.printSentenceMeasures(new File(args[2]),true);*/
	}

}
