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


	CrowdMedRelDir(String filename) throws IOException {
		measures = new SentenceMeasure[] {
				new Magnitude(),
				new NormalizedMagnitude(),
				new NormalizedRelationMagnitude(),
				new NormalizedRelationMagnitudeByAll(),
				new MaxRelationCosine(),
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
		
		for (int workid : workerMeasures.keySet()) {
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
	
		out.print("Sent id");
		if (printVectors) {
			for (int i=0; i<vectorIndex.size();i++) {
				for (String label : vectorIndex.keySet()) {
					if (vectorIndex.get(label) == i) out.print(","+label);
				}
			}
		}
		for (int i=0; i<measureIndex.size();i++) out.print(","+measures[i].label());
		for (int i=0; i<filterIndex.size();i++) out.print(","+filters[i].label());
		out.println();
		
		for (String sentid : sentSumVectors.keySet()) {
			out.print(sentid+",");
			if (printVectors) out.print(JavaMlUtils.instanceString(sentSumVectors.get(sentid))+",");
			out.println(JavaMlUtils.instanceString(sentMeasures.get(sentid))+","+
					JavaMlUtils.instanceString(sentFilters.get(sentid)));
		}
	
		out.println();
		for (int i=0; i<aggIndex.size();i++) out.print(","+aggregates[i].label());
		out.println();
		for (int i=0; i<aggIndex.size();i++) out.print(","+aggMeasures.get(i));
		out.println();
	}

	@Override
	protected Integer getNumCols() {
		return 32;
	}
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		Set<String> annots = new HashSet<String>();
		String dir = lineArray.get(15);
		if (NIL.equals(dir)) annots.add(NIL);
		else {
			int arg1Beg = Integer.decode(lineArray.get(17));
			int arg2Beg = Integer.decode(lineArray.get(18));
			if (arg1Beg < arg2Beg) annots.add(ARG1);
			else annots.add(ARG2);
		}
		return annots;
	}

	@Override
	protected Integer getWorkId(ArrayList<String> lineArray) {
		return Integer.decode(lineArray.get(10));
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
			c.printSentenceMeasures(new File(args[2]),true);
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	@Override
	protected boolean isFilteredWorker(Integer workid) {
		// TODO Auto-generated method stub
		return false;
	}
}
