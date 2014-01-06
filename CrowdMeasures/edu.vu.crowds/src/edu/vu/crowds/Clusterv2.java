/**
 * 
 */
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

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.DefaultDataset;
import net.sf.javaml.core.DenseInstance;
import net.sf.javaml.core.Instance;
import net.sf.javaml.core.SparseInstance;
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
 * TODO update for actual raw CF data format
 *  
 * Inputs annotation data from workers.  Takes sentence id, workerid, annotations, seed-type
 * 
 * Worker matrix computes for each pair of workers for the sentences they've mutually annotated, how often then agree
 * 
 * _unit_id	_worker_id	step_1_select_the_valid_relations	step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1	step_2b_if_you_selected_none_in_step_1_explain_why	b1	b2	e1	e2	id	relation-type	sentence	term1	term2
	*	
 * @author welty
 * @deprecated This has been rplaced by the CrowdTruth abstract class and is no longer working
 *
 */
public class Clusterv2 {
	
	/**
	 * The split char
	 */
	final static String SPLIT_CHAR = ",";
	/**
	 * Map from workerid -> Map from sent id -> worker annotation vector
	 */
	Map<Integer,Map<Integer,Instance>> workers;
	/**
	 * Map from worker id -> Map from sent id -> set of annotations
	 * This is for reading in the csv data
	 * TODO probably this can be removed and read data directly into workers field above
	 */
	Map<Integer,Map<Integer,Set<String>>> workerSentAnnot = new HashMap<Integer,Map<Integer,Set<String>>>();
	/**
	 * Map from sentid -> Dataset of worker annotation vectors
	 */
	Map<Integer,Dataset> sentClusters;
	/**
	 * Map from worker id -> Map from worker id -> Map from sentid -> Set of annots in common
	 * This is a 3-d confusion matrix.
	 * workerid1 -> workerid2 = empty Map if they annotated no sentences in common
	 * workerid1 -> workerid2 -> sentid = empty Set if they did not agree on that sent
	 */
	Map<Integer,Map<Integer,Map<Integer,Set<String>>>> confusionMatrix = new HashMap<Integer,Map<Integer,Map<Integer,Set<String>>>>();
	/**
	 * Map from relation names to vector index
	 */
	Map<String,Integer> vectorIndex = new HashMap<String,Integer>();
	/**
	 * Map from sentid -> seed relation name
	 */
	Map<Integer,String> sentSeedMap = new HashMap<Integer,String>();
	/**
	 * Map from sent id -> vector sum of annots on sent
	 */
	Map<Integer,Instance> sentSumVectors = new HashMap<Integer,Instance>();
	/**
	 * The sentence metrics, these are the indexes of sentMeasures instances. This should be done with an enum!
	 * "|S|","|S|-norm","Max","|Rels|","|Rels|-norm","|Rels|-normByAll","|Rels|-normByAllSmooth"
	 */
	SentenceMeasure[] measures = {
			new Magnitude(),
			new NormalizedMagnitude(),
			new NormalizedRelationMagnitude(),
			new NormalizedRelationMagnitudeByAll(),
			new MaxRelationCosine(),
	};
	/**
	 * Map from sent id -> instance containing metrics for the sent. 
	 */
	Map<Integer,Instance> sentMeasures = new HashMap<Integer,Instance>();
	/**
	 * Map from measure -> sentence measure index 
	 */
	Map<SentenceMeasure,Integer> measureIndex = new HashMap<SentenceMeasure,Integer>();
	/**
	 * The aggregate metrics, these are the indexes of the aggMeasures instance. This should be done with an enum!
	 * "|S|","|S|-norm","Max","|Rels|","|Rels|-norm","|Rels|-normByAll","|Rels|-normByAllSmooth"
	 */
	AggregateSentenceMeasure[] aggregates = {
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
	/**
	 * instance containing aggregate metrics for the collection. 
	 */
	Instance aggMeasures = null;
	/**
	 * Map from aggmeasure -> measure index 
	 */
	Map<AggregateMeasure,Integer> aggIndex = new HashMap<AggregateMeasure,Integer>();
	/**
		 * The sentence filters, these are the indexes of sentFilters instances. This should be done with an enum!
		 * "|S|-norm<1std","|Rels|-norm<1std","|Rels|-normByAll<1std","|Rels|-normByAllSmooth<1std"};
		 */
		SentenceFilter[] filters = {
				new PassAll(),
				new StdevMagBelowMean(),
				new StdevNormMagBelowMean(),
				new StdevNormRelMagBelowMean(),
				new StdevNormRelMagByAllBelowMean(),
				new StdevMRCBelowMean(),
	//			new BelowMean()
		};
	/**
	 * Map from sent id -> instance containing filtered flags for the sent according to filter metrics
	 */
	Map<Integer,Instance> sentFilters = null;
	/**
	 * Map from filter to filter index
	 */
	Map<SentenceFilter,Integer> filterIndex = new HashMap<SentenceFilter,Integer>();
	/**
	 * The worker measures
	 */
	WorkerMeasure[] workMeasures = {
			new NumberOfSents(),
			new WorkerCosine(),
			new AvgWorkerAgreement(),
			new AnnotsPerSent(),
	};
	/**
	 * Map from workerid -> filter index -> worker measure with this sentence filter
	 */
	Map<Integer,Map<Integer,Instance>> workerMeasures = new HashMap<Integer,Map<Integer,Instance>>();
	/**
	 * Map from measure to measure index
	 */
	Map<WorkerMeasure,Integer> workerMeasureIndex = new HashMap<WorkerMeasure, Integer>();

	Clusterv2(String filename) throws IOException {
		init(new File(filename));
	}
	
	public void init(File f) throws IOException {
		MakeIndexMap(measures,measureIndex);
		MakeIndexMap(filters,filterIndex);
		MakeIndexMap(aggregates,aggIndex);
		MakeIndexMap(workMeasures,workerMeasureIndex);		
	
		BufferedReader r= new BufferedReader(new FileReader(f));
		r.readLine(); // skip header
		int index=0;
		String splitChar = SPLIT_CHAR;
		if (f.getName().endsWith(".tsv")) splitChar = "\t";
		for (String l = r.readLine(); l != null; l=r.readLine()) {
			String[] lineArray = l.split(splitChar);
			while (lineArray.length < 32) {
				l += r.readLine();
				lineArray = l.split(splitChar);
			}
			// for (int i=0; i<lineArray.length; i++) System.out.println(lineArray[i]);
			Integer sentId = Integer.decode(lineArray[0]);
			Integer workId = Integer.decode(lineArray[7]);
			String choices = lineArray[13].replace("\"", "");
			String seedRel = lineArray[12];
			String annots[] = choices.split("\\]\\[");
			for (int i=0; i<annots.length; i++) {
				annots[i]=annots[i].replaceAll("\\]|\\[", "").toLowerCase();
				if (!vectorIndex.containsKey(annots[i])) {
					vectorIndex.put(annots[i],index);
					index++;
				}
			}
			Set<String> annotSet = new HashSet<String>(Arrays.asList(annots));
			
//			System.out.print(sentId+","+workId+","+choices);
//			for (int i=0; i<annots.length; i++) System.out.print(","+annots[i]);
//			System.out.println();

			if (!sentSeedMap.containsKey(sentId)) {
				sentSeedMap.put(sentId, seedRel);	
			}

			Map<Integer,Set<String>> workerSents = workerSentAnnot.get(workId);
			if (workerSents == null) {
				workerSents = new HashMap<Integer,Set<String>>();
				workerSentAnnot.put(workId, workerSents);
			}
			if (workerSents.containsKey(sentId)) {
				System.err.println("Worker " + workId + " annotated sentence " + sentId + " more than once");
			} else {
				workerSents.put(sentId, annotSet);
			}
		}
		r.close();
	}
	
	public void buildConfusionMatrix() {
		for (int workid1 : workerSentAnnot.keySet()) {
			Map<Integer,Set<String>> work1Sents = workerSentAnnot.get(workid1);
			Map<Integer,Map<Integer,Set<String>>> work1row = new HashMap<Integer,Map<Integer,Set<String>>>();
			confusionMatrix.put(workid1, work1row);
			for (int workid2 : workerSentAnnot.keySet()) {
				Map<Integer,Set<String>> work2Sents = workerSentAnnot.get(workid2);
				Map<Integer,Set<String>> work1work2row = new HashMap<Integer, Set<String>>();
				work1row.put(workid2, work1work2row);
				for (int sentid : work1Sents.keySet()) {
					if (work2Sents.containsKey(sentid)) {
						Set<String> common = new HashSet<String>(work1Sents.get(sentid));
						common.retainAll(work2Sents.get(sentid));
						work1work2row.put(sentid, common);
					} 
				}
			}
		}
	}
	
	public void buildSentenceClusters() {
		sentClusters = new HashMap<Integer,Dataset>();
		workers = new HashMap<Integer,Map<Integer,Instance>>();
		for (int workid1 : workerSentAnnot.keySet()) {
			Map<Integer,Set<String>> work1Sents = workerSentAnnot.get(workid1);
			Map<Integer,Instance> workerInstancesBySent = workers.get(workid1);
			if (workerInstancesBySent == null) {
				workerInstancesBySent = new HashMap<Integer,Instance>();
				workers.put(workid1, workerInstancesBySent);
			}
			for (int sentid : work1Sents.keySet()) {
				Instance sentVec = sentenceVector(work1Sents.get(sentid));
				Dataset sentSet = sentClusters.get(sentid);
				if (sentSet == null) {
					sentSet = new DefaultDataset();
					sentClusters.put(sentid, sentSet);
				}
				sentSet.add(sentVec);
				workerInstancesBySent.put(sentid,sentVec);
			}
		}
	}
	
	public void computeSentenceMeasures() {
		sentSumVectors = new HashMap<Integer,Instance>();
		sentMeasures = new HashMap<Integer,Instance>();
		for (SentenceMeasure m : measures) m.init(vectorIndex);
		for (int sentid : sentClusters.keySet()) {
			Instance sumVec = JavaMlUtils.sumVector(sentClusters.get(sentid),vectorIndex.size());
			sentSumVectors.put(sentid,sumVec);
			Instance sentMeasure = new DenseInstance(measures.length);
			sentMeasures.put(sentid,sentMeasure);
			for (SentenceMeasure measure : measures) {
				sentMeasure.put(measureIndex.get(measure), measure.call(sentClusters.get(sentid),sumVec));
			}
		}
	}
	
	public void computeAggregateSentenceMeasures() {
		aggMeasures = new DenseInstance(aggregates.length);
		for (AggregateSentenceMeasure agg : aggregates) agg.init(vectorIndex,measureIndex);
		
		for (int sentid : sentClusters.keySet()) {
			for (AggregateSentenceMeasure agg : aggregates) {
				agg.next(sentClusters.get(sentid),sentSumVectors.get(sentid),sentMeasures.get(sentid));
			}
		}
		for (AggregateSentenceMeasure agg : aggregates) aggMeasures.put(aggIndex.get(agg),agg.value());
	}
	
	public void computeSentenceFilters() {
		sentFilters = new HashMap<Integer,Instance>();
		for (SentenceFilter f : filters) f.init(vectorIndex,measureIndex,aggIndex);
		for (int sentid : sentClusters.keySet()) {
			Instance filts = new DenseInstance(filters.length);
			sentFilters.put(sentid,filts);
			for (SentenceFilter filt : filters) {
				filts.put(filterIndex.get(filt), 
						filt.call(sentClusters.get(sentid),sentSumVectors.get(sentid),sentMeasures.get(sentid),aggMeasures));
			}
		}
	}
	
	public void computeWorkerMeasures() {
		// Map from workerid -> filter index -> worker measure with this sentence filter
		workerMeasures = new HashMap<Integer,Map<Integer,Instance>>();
		for (int i=0; i<filters.length; i++) {
			for (WorkerMeasure m : workMeasures) m.init(i);
			for (int workid : workers.keySet()) {
				Map<Integer,Instance> measuresForAllFilts;
				if (workerMeasures.containsKey(workid)) {
					measuresForAllFilts = workerMeasures.get(workid);
				}
				else {
					measuresForAllFilts = new HashMap<Integer, Instance>();
					workerMeasures.put(workid,measuresForAllFilts);
				}
				Instance workerMeasuresForFilt = new DenseInstance(workMeasures.length);
				measuresForAllFilts.put(i, workerMeasuresForFilt);
				for (WorkerMeasure measure : workMeasures) {
//					workerMeasuresForFilt.put(workerMeasureIndex.get(measure), 
//							measure.call(workers.get(workid), confusionMatrix.get(workid), sentSumVectors, sentFilters));
				}
			}
		}
	}
	
	private void printWorkerMeasures(File f) throws FileNotFoundException {
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
	
		/**
		 * return a list of Measures ordered by the Instance index.  The index maps are indexed by 
		 * measure, so there is no way to iterate through them by number, which is needed to 
		 * control the order in printing only.  Inefficient, but only used in printing.
		 * 
		 */
	private <T extends Measure> List<T> getMeasuresByIndex(Map<T, Integer> measureIndex) {
		List<T> rtn = new ArrayList<T>();
		for (int i=0; i<measureIndex.size();i++) {
			for (T m : measureIndex.keySet()) {
				if (measureIndex.get(m) == i) rtn.add(m);
			}
		}
		return rtn;
	}

	private void printSentenceMeasures(File f, boolean printVectors) throws FileNotFoundException {
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
		
		for (int sentid : sentSumVectors.keySet()) {
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

	public Instance sentenceVector(Set<String> annots) {
		Instance v = new SparseInstance(vectorIndex.size());
		for (String annot : annots) v.put(vectorIndex.get(annot), 1.0);
		return v;
	}

	private <T> void MakeIndexMap(T[] indices,	Map<T, Integer> map) {
		for (int i=0; i<indices.length; i++) map.put(indices[i], i);
	}

	/**
	 * input-file workers-file sentence-file
	 */
	public static void main(String[] args) {
	
		try {
			Clusterv2 c = new Clusterv2(args[0]);
			c.buildConfusionMatrix();
			c.buildSentenceClusters();
			c.computeSentenceMeasures();
			c.computeAggregateSentenceMeasures();
			c.computeSentenceFilters();
			c.computeWorkerMeasures();
			c.printWorkerMeasures(new File(args[1]));
			c.printSentenceMeasures(new File(args[2]),false);
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

}
