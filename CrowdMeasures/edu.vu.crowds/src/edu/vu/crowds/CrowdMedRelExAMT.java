package edu.vu.crowds;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;
import java.util.Set;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.sentences.measures.MaxRelationCosine;

public class CrowdMedRelExAMT extends CrowdTruthRelEx {

	CrowdMedRelExAMT(String filename) throws IOException {
		super(filename);
	}
	
	@Override	
	protected void printSentenceMeasures(File f, boolean printVectors) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);
		
		String sep = ",";
		if (f.getName().endsWith(".tsv")) sep="\t";
	
		int[] origCols =      { 32 , 35 , 0 , 33 , 36 , 38  , 30    , 37   , 31   , 34};
		String[] origLabels = {"b1","b2","id","e1","e2","rel","twrel","sent","arg1","arg2"};
		
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
	
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		String choices = lineArray.get(40).replace("\"", "");
		String annots[] = choices.split("\\]\\s*\\[");
		for (int i=0; i<annots.length; i++) {
			annots[i]=annots[i].replaceAll("\\]|\\[", "").toLowerCase();
			if (!vectorIndex.containsKey(annots[i])) {
				vectorIndex.put(annots[i],vectorIndex.size());
			}
		}
		return new HashSet<String>(Arrays.asList(annots));
	}
	
	@Override
	protected String getWorkId(ArrayList<String> lineArray) {
		return lineArray.get(15);
	}

	@Override
	protected String getSentId(ArrayList<String> lineArray) {
		return lineArray.get(28);
	}
	
	@Override
	protected Integer getNumCols() { return 44; }
	
	public static void main(String[] args) {
		try {
			CrowdMedRelExAMT c = new CrowdMedRelExAMT(args[0]);
			c.buildConfusionMatrix();
			c.buildSentenceClusters();
			c.computeSentenceMeasures();
			c.computeAggregateSentenceMeasures();
			c.computeSentenceFilters();
			c.computeWorkerMeasures();
			c.printWorkerMeasures(new File(args[1]));
//			c.printSentenceMeasures(new File(args[2]),true);
			c.filterWorkers();
			c.buildConfusionMatrix();
			c.buildSentenceClusters();
			c.computeSentenceMeasures();
			c.computeAggregateSentenceMeasures();
			c.printSentenceMeasures(new File(args[2]),true);
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

}
