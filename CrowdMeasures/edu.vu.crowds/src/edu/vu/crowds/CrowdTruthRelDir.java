/**
 * 
 * @author anca
 * 
 */

package edu.vu.crowds;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintStream;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.sentences.measures.MaxRelationCosine;

public class CrowdTruthRelDir extends CrowdMedRelDir {
	
	// AMT
	/*final static int B1 = 30;
	final static int B2 = 31;
	final static int E1 = 32;
	final static int E2 = 33;
	final static int SENTENCE = 35;
	final static int TERM1 = 36;
	final static int TERM2 = 37;
	final static int REL = 29;
	final static int TWREX = 34;
	final static int SOURCE = -1;
	final static int DIR = 38;
	final static int SENID = 27;
	final static int COS = 28;
	final static int NCOL = 39;
	final static int WID = 15;*/
	
	// CF
	final static int B1 = 13;
	final static int B2 = 14;
	final static int E1 = 16;
	final static int E2 = 17;
	final static int SENTENCE = 21;
	final static int TERM1 = 22;
	final static int TERM2 = 23;
	final static int REL = 18;
	final static int TWREX = 24;
	final static int SOURCE = -1;
	final static int DIR = 12;
	final static int SENID = 20;
	final static int COS = 19;
	final static int NCOL = 25;
	final static int WID = 7;
	
	HashMap<String, Integer> mapSentidToRel = new HashMap<String, Integer>();
	

	CrowdTruthRelDir(String filename) throws IOException {
		super(filename);

		mapSentidToRel.put("associated", 1);
		mapSentidToRel.put("treat", 2);
		mapSentidToRel.put("other", 3);
		mapSentidToRel.put("cause", 4);
		mapSentidToRel.put("prevent", 5);
		mapSentidToRel.put("symptom", 6);
		mapSentidToRel.put("side", 7);
		mapSentidToRel.put("manifestation", 8);
		mapSentidToRel.put("location", 9);
		mapSentidToRel.put("contraindicates", 10);
		mapSentidToRel.put("is a", 11);
		mapSentidToRel.put("is_a", 11);
		mapSentidToRel.put("part", 12);
		mapSentidToRel.put("diagnose", 13);
		mapSentidToRel.put("none", 0);
	}
	
	protected String correctSentId(String sentid, String relation) {
		if (sentid.contains("FS") == false) {
			sentid = sentid.substring(0, 6) + "-FS1";
		}
		else {
			sentid = sentid.substring(0, 10);
		}
		
		for (String rel : mapSentidToRel.keySet()) {
			if (relation.contains(rel) == true) {
				int relid = mapSentidToRel.get(rel);
				sentid += "-" + relid;
			}
		}
		
		return sentid;
	}
	
	@Override
	protected String getWorkId(ArrayList<String> lineArray) {
		return lineArray.get(WID);
	}

	@Override
	protected String getSentId(ArrayList<String> lineArray) {
		return lineArray.get(SENID);
	}
	
	@Override
	protected Integer getNumCols() {
		return NCOL;
	}
	
	
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		Set<String> annots = new HashSet<String>();
		try {
		String sen = lineArray.get(SENTENCE);//.replaceAll("[\\[\\]]", "");
		String dir = lineArray.get(DIR);
		
		 //System.err.println("SENTENCE: " + sen);
		// System.err.println("CHOICE: " + dir);
		
		// AMT
		/*if (dir.equals("Choice3")) annots.add(NIL);
		else {
			if (dir.equals("Choice1")) {
				// System.err.println("CHOICE: " + dir);
				annots.add(ARG1);
			}
			else annots.add(ARG2);
		}*/
		
		// CF
		if (dir.startsWith(lineArray.get(TERM1)) && dir.endsWith(lineArray.get(TERM2))) {
			annots.add(ARG1);
			//System.err.println(lineArray.get(SENID) + ": choice1");
		}
		else if (dir.startsWith(lineArray.get(TERM2)) && dir.endsWith(lineArray.get(TERM1))) {
			annots.add(ARG2);
			//System.err.println(lineArray.get(SENID) + ": choice2");
		}
		else {
			annots.add(NIL);
			//System.err.println(lineArray.get(SENID) + ": choice3");
		}

		// if ("TRUE".equalsIgnoreCase(lineArray.get(5)) || "1".equalsIgnoreCase(lineArray.get(5)))
		//  	annots.add(GS_FAIL); // failed GS test
		
		// annots will have 1-2 members, the direction and GS_FAIL if failed
		return annots;
		
		} catch (StringIndexOutOfBoundsException e) { 
			return null; 
		}
	}
	
	
	
	protected void printTrainFile(File f) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);
		
		String sep = "\t";
		
		// int[] origCols =      {REL, TERM1, B1, E1, TERM2, B2, E2, SENTENCE};
		String[] origLabels = {"confidence", "crowdRel", "term1", "b1", "e1", "term2", "b2","e2","sent"};
		
		out.print("Sent_id");
		for (int i=0;i<origLabels.length;i++) out.print(sep+origLabels[i]);
		out.println();
		
		for (String sentid : sentSumVectors.keySet()) {
			String term1 = "";
			String term2 = "";
			String b1 = "";
			String e1 = "";
			String b2 = "";
			String e2 = "";
			
			Instance sumVector = sentSumVectors.get(sentid);
			MaxRelationCosine relCos = (MaxRelationCosine) measures[4];
			ArrayList<String> sentInput = sentsMap.get(sentid);
			
		//	if (sentMeasures.get(sentid).get(4) >= 0.8) {
				if (relCos.relationCosine(sumVector, 0) > relCos.relationCosine(sumVector, 1)) {
					term1 = sentInput.get(TERM1);
					b1 = sentInput.get(B1);
					e1 = sentInput.get(E1);
					
					term2 = sentInput.get(TERM2);
					b2 = sentInput.get(B2);
					e2 = sentInput.get(E2);
				}
				else {
					term2 = sentInput.get(TERM1);
					b2 = sentInput.get(B1);
					e2 = sentInput.get(E1);
					
					term1 = sentInput.get(TERM2);
					b1 = sentInput.get(B2);
					e1 = sentInput.get(E2);
				}
			//}
			
			if (term1.compareTo("") != 0) {
				String relation = sentInput.get(REL);
				
				if (relation.contains("part of") == false &&
						relation.contains("part_of") == false &&
						relation.contains("is a") == false &&
						relation.contains("is_a") == false) {
				
					if (relation.contains("prevent"))
						relation = "treats";
					
					out.print(correctSentId(sentid, relation) + sep + sentInput.get(COS) + sep + relation + sep +
							term1 + sep + b1 + sep + e1 + sep +
							term2 + sep + b2 + sep + e2 + sep +
							sentInput.get(SENTENCE));
					out.println();
				}
			}
		}
		
	}
	
	@Override	
	protected void printSentenceMeasures(File f, boolean printVectors) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);

		String sep = ",";
		if (f.getName().endsWith(".tsv")) sep="\t";

		int[] origCols = {TERM1, TERM2, B1, B2, E1, E2, TWREX, SENTENCE};
		String[] origLabels = {"term1", "term2", "b1","b2","e1","e2","TWrex","sent"};

		out.print("Sent id");
		if (printVectors) {
			for (int i=0; i<vectorIndex.size();i++) {
				for (String label : vectorIndex.keySet()) {
					if (vectorIndex.get(label) == i) out.print(sep+label+sep+label+"-cos");
				}
			}
		}
		out.print(sep+"MaxRelCos" + sep + "NumAnnots" + sep + "crowdRel");
		for (int i=0;i<origCols.length;i++) out.print(sep+origLabels[i]);
		out.println();

		for (String sentid : sentSumVectors.keySet()) {
			ArrayList<String> sentInput = sentsMap.get(sentid);
			String relation = sentInput.get(REL);
			
			if (relation.contains("part of") == false &&
				relation.contains("part_of") == false &&
				relation.contains("is a") == false &&
				relation.contains("is_a") == false) {
			
				if (relation.contains("prevent"))
					relation = "treats";
				
				out.print(correctSentId(sentid, relation) +sep);
				if (printVectors) {
					Instance sumVector = sentSumVectors.get(sentid);
					MaxRelationCosine relCos = (MaxRelationCosine) measures[4];
					for (int rel = 0; rel<sumVector.keySet().size(); rel++) {
						out.print(sumVector.get(rel) + sep+relCos.relationCosine(sumVector, rel)+sep);
					}
				}
	
				Instance meas = sentMeasures.get(sentid);
				out.print(meas.get(4) + sep + meas.get(5) + sep + relation);
	
				
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
	}
	
	/**
	 * input-file workers-file sentence-file
	 */
	public static void main(String[] args) {
		
		String in = args[0];
		String out = args[1];
		int num = Integer.parseInt(args[2]);
		
		for (int i = 1; i <= num; i++) {
			//String inputFile = in + i + "/RelDir_batch_" + i + "_";
			String inputFile = in + i + "/RelDir_" + i;
			
			/*if (i < 7) { 
				inputFile += "noFactSpan";
			}
			else {
				inputFile += "withFactSpan";
			}*/
			
			//inputFile += "_cause";
			
			System.err.println("Processing " + inputFile + "...");
	
			try {
				CrowdTruthRelDir c = new CrowdTruthRelDir(inputFile + ".csv");
				c.buildConfusionMatrix();
				c.buildSentenceClusters();
				c.computeSentenceMeasures();
				c.computeAggregateSentenceMeasures();
				c.computeSentenceFilters();
				c.computeWorkerMeasures();
				c.printWorkerMeasures(new File(inputFile + "-workers.csv"));
			//	c.printSentenceMeasures(new File(inputFile + "-sents-short.csv"),true);
				c.filterWorkers();
				c.buildConfusionMatrix();
				c.buildSentenceClusters();
				c.computeSentenceMeasures();
				c.computeAggregateSentenceMeasures();
				c.printSentenceMeasures(new File(inputFile + "-sents.csv"),true);
				c.printTrainFile(new File(out + "crowdtruth-pos_" + i + ".csv"));
	
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
	}
	
}
