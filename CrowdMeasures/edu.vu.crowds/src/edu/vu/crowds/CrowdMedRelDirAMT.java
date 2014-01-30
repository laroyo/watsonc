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
import java.util.HashSet;
import java.util.Set;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.sentences.measures.MaxRelationCosine;

public class CrowdMedRelDirAMT extends CrowdMedRelDir {

	CrowdMedRelDirAMT(String filename) throws IOException {
		super(filename);
		// TODO Auto-generated constructor stub
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
	protected Integer getNumCols() {
		return 40;
	}
	
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		Set<String> annots = new HashSet<String>();
		try {
		String sen = lineArray.get(37);//.replaceAll("[\\[\\]]", "");
		String dir = lineArray.get(39);
		
		// System.err.println("SENTENCE: " + sen);
		// System.err.println("CHOICE: " + dir);
		
		if (dir.equals("Choice3")) annots.add(NIL);
		else {
			if (dir.equals("Choice1")) {
				// System.err.println("CHOICE: " + dir);
				annots.add(ARG1);
			}
			else annots.add(ARG2);
		}

		// if ("TRUE".equalsIgnoreCase(lineArray.get(5)) || "1".equalsIgnoreCase(lineArray.get(5)))
		//  	annots.add(GS_FAIL); // failed GS test
		
		// annots will have 1-2 members, the direction and GS_FAIL if failed
		return annots;
		
		} catch (StringIndexOutOfBoundsException e) { 
			return null; 
		}
	}
	
	@Override	
	protected void printSentenceMeasures(File f, boolean printVectors) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);

		String sep = ",";
		if (f.getName().endsWith(".tsv")) sep="\t";

		int[] origCols = {     32,   35, 33,  36,  38,   39,   37};
		String[] origLabels = {"b1","b2","e1","e2","rel","dir","sent"};

		out.print("Sent id");
		if (printVectors) {
			for (int i=0; i<vectorIndex.size();i++) {
				for (String label : vectorIndex.keySet()) {
					if (vectorIndex.get(label) == i) out.print(sep+label+sep+label+"-cos");
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
					out.print(sumVector.get(rel) + sep+relCos.relationCosine(sumVector, rel)+sep);
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
	
	/**
	 * input-file workers-file sentence-file
	 */
	public static void main(String[] args) {
	
		try {
			CrowdMedRelDirAMT c = new CrowdMedRelDirAMT(args[0]);
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
