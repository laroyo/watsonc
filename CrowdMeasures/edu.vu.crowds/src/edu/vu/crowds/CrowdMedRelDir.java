/**
 * 
 */
package edu.vu.crowds;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintStream;
import java.util.ArrayList;
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
import edu.vu.crowds.analysis.workers.NumberOfSents;
import edu.vu.crowds.analysis.workers.WorkerCosine;
import edu.vu.crowds.analysis.workers.WorkerMeasure;

/**
 * Inputs annotation data from workers.  Takes sentence id, workerid, annotations, seed-type
 * 
 * Worker matrix computes for each pair of workers for the sentences they've mutually annotated, how often then agree
 * 	
 * @author welty
 *
 */
public class CrowdMedRelDir extends CrowdTruth {
	/**
	 * The split char
	 */
	final static String SPLIT_CHAR = ",";
	/**
	 * Annots are ARG1 (ARG1 is first), ARG2 (ARG2 is first), no_relation. 
	 */
	final static String ARG1 = "ARG1";
	final static String ARG2 = "ARG2";
	final static String NIL = "no_relation";
	static final String GS_FAIL = "GS-FAIL";
	
	static Integer gsFailIndex = null;

	CrowdMedRelDir(String filename) throws IOException {
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
		};
		
		vectorIndex.put(ARG1, 0);
		vectorIndex.put(ARG2, 1);
		vectorIndex.put(NIL, 2);
		vectorIndex.put(GS_FAIL, 3);
		init(new File(filename));
	}
	
	public void init(File f) throws IOException {
		super.init(f);
	}
	
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
		out.println();
		
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
			out.println();
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

		int[] origCols = {17,18,22,23,35,15,29};
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

	@Override
	protected Integer getNumCols() {
		return 36;
	}
	
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		Set<String> annots = new HashSet<String>();
		try {
		String dir = lineArray.get(15);//.replaceAll("[\\[\\]]", "");
		if (NIL.equalsIgnoreCase(dir)) annots.add(NIL);
		else {
			String dirArg1 = processQuotes(dir.substring(dir.indexOf('[')+1,dir.indexOf(']')));
			String dirArg2 = processQuotes(dir.substring(dir.lastIndexOf('[')+1,dir.lastIndexOf(']')));
			String sent = lineArray.get(29).replaceAll("[\\[\\]]", "");
			sent = processQuotes(sent);

			int arg1Beg = Integer.decode(lineArray.get(17));
			int arg1End = Integer.decode(lineArray.get(22));
			String arg1 = sent.substring(arg1Beg, arg1End);
//			int arg2Beg = Integer.decode(lineArray.get(18));
//			int arg2End = Integer.decode(lineArray.get(23));
//			String arg2 = sent.substring(arg2Beg, arg2End);
			
			if (arg1.equalsIgnoreCase(dirArg1)) annots.add(ARG1);
			else if (arg1.equalsIgnoreCase(dirArg2)) annots.add(ARG2);
			else { 
				System.err.println("Args don't match sent: " + dir);
				return null;
			}

			//			int loc = lineArray.get(29).indexOf(dirArg1);
//			System.out.println(dirArg1 + "," + dirArg2 + "," + arg1 + "," + arg2);
			// The offsets are a bit off, pick the one that is closest...
//			System.out.print("Selected arg offset: "+loc+", ");
//			if (Math.abs(loc-arg1Beg) < Math.abs(loc-arg2Beg)) {
//				annots.add(ARG1);
//				System.out.println(arg1Beg);
//			} else {
//				annots.add(ARG2);
//				System.out.println(arg2Beg);
//			}
		}

		if ("TRUE".equalsIgnoreCase(lineArray.get(5)) || "1".equalsIgnoreCase(lineArray.get(5)))
			annots.add(GS_FAIL); // failed GS test
		
		// annots will have 1-2 members, the direction and GS_FAIL if failed
		return annots;
		} catch (StringIndexOutOfBoundsException e) { 
			return null; 
		}
	}

	@Override
	protected String getWorkId(ArrayList<String> lineArray) {
		return lineArray.get(10);
	}

	@Override
	protected String getSentId(ArrayList<String> lineArray) {
		return lineArray.get(19);
	}
	/**
	 * input-file workers-file sentence-file
	 */
	public static void main(String[] args) {
	
		try {
			CrowdMedRelDir c = new CrowdMedRelDir(args[0]);
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
		Double annotsPerSent = measures.get(workerMeasureIndex.get(measureList.get(3)));
		if (annotsPerSent > 1) return true; // this guy failed a GS test
		// System.err.println("PASSED GS");
		
		Double agree = measures.get(workerMeasureIndex.get(measureList.get(2)));
		if (agree < .6f) return true; //very disagreeable worker
		// System.err.println("PASSED AGREEMENT");

		Double cos = measures.get(workerMeasureIndex.get(measureList.get(1)));  
		if (cos > .4) return true; // does not appear to have signal for this task
		// System.err.println("PASSED TASK SIGNAL");
		
		return false;
	}
}
