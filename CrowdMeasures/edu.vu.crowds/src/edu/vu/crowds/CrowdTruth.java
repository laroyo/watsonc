/**
 * 
 */
package edu.vu.crowds;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Set;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.DefaultDataset;
import net.sf.javaml.core.DenseInstance;
import net.sf.javaml.core.Instance;
import net.sf.javaml.core.SparseInstance;
import edu.vu.crowds.analysis.sentences.AggregateSentenceMeasure;
import edu.vu.crowds.analysis.sentences.SentenceFilter;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
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
 *
 */
public abstract class CrowdTruth {
	
	/**
	 * The split char
	 */
	protected static String SPLIT_CHAR = ",";
	/**
	 * Map from workerid -> Map from sent id -> worker annotation vector
	 */
	protected Map<Integer,Map<String,Instance>> workers;
	/**
	 * Map from worker id -> Map from sent id -> set of annotations
	 * This is for reading in the csv data
	 * TODO probably this can be removed and read data directly into workers field above
	 */
	protected Map<Integer,Map<String,Set<String>>> workerSentAnnot = new HashMap<Integer,Map<String,Set<String>>>();
	/**
	 * Map from sentid -> Dataset of worker annotation vectors
	 */
	protected Map<String,Dataset> sentClusters;
	/**
	 * Map from worker id -> Map from worker id -> Map from sentid -> Set of annots in common
	 * This is a 3-d confusion matrix.
	 * workerid1 -> workerid2 = empty Map if they annotated no sentences in common
	 * workerid1 -> workerid2 -> sentid = empty Set if they did not agree on that sent
	 */
	protected Map<Integer,Map<Integer,Map<String,Set<String>>>> confusionMatrix;
	/**
	 * Map from relation names to vector index
	 */
	protected Map<String,Integer> vectorIndex = new HashMap<String,Integer>();
	/**
	 * Map from sentid -> full parsed sentence input array
	 */
	protected Map<String,ArrayList<String>> sentsMap = new HashMap<String,ArrayList<String>>();
	/**
	 * Map from sent id -> vector sum of annots on sent
	 */
	protected Map<String,Instance> sentSumVectors = new HashMap<String,Instance>();
	/**
	 * The sentence metrics, these are the indexes of sentMeasures instances. This should be done with an enum!
	 * "|S|","|S|-norm","Max","|Rels|","|Rels|-norm","|Rels|-normByAll","|Rels|-normByAllSmooth"
	 */
	protected SentenceMeasure[] measures = {	};
	/**
	 * Map from sent id -> instance containing metrics for the sent. 
	 */
	protected Map<String,Instance> sentMeasures = new HashMap<String,Instance>();
	/**
	 * Map from measure -> sentence measure index 
	 */
	protected Map<SentenceMeasure,Integer> measureIndex = new HashMap<SentenceMeasure,Integer>();
	/**
	 * The aggregate metrics, these are the indexes of the aggMeasures instance. This should be done with an enum!
	 * "|S|","|S|-norm","Max","|Rels|","|Rels|-norm","|Rels|-normByAll","|Rels|-normByAllSmooth"
	 */
	protected AggregateSentenceMeasure[] aggregates = {	};
	/**
	 * instance containing aggregate metrics for the collection. 
	 */
	protected Instance aggMeasures = null;
	/**
	 * Map from aggmeasure -> measure index 
	 */
	protected Map<AggregateMeasure,Integer> aggIndex = new HashMap<AggregateMeasure,Integer>();
	/**
		 * The sentence filters, these are the indexes of sentFilters instances. This should be done with an enum!
		 * "|S|-norm<1std","|Rels|-norm<1std","|Rels|-normByAll<1std","|Rels|-normByAllSmooth<1std"};
		 */
	protected SentenceFilter[] filters = { };
	/**
	 * Map from sent id -> instance containing filtered flags for the sent according to filter metrics
	 */
	protected Map<String,Instance> sentFilters = null;
	/**
	 * Map from filter to filter index
	 */
	protected Map<SentenceFilter,Integer> filterIndex = new HashMap<SentenceFilter,Integer>();
	/**
	 * The worker measures
	 */
	protected WorkerMeasure[] workMeasures = {	};
	/**
	 * Map from workerid -> filter index -> worker measure with this sentence filter
	 */
	protected Map<Integer,Map<Integer,Instance>> workerMeasures = new HashMap<Integer,Map<Integer,Instance>>();
	/**
	 * Map from measure to measure index
	 */
	protected Map<WorkerMeasure,Integer> workerMeasureIndex = new HashMap<WorkerMeasure, Integer>();

	public void init(File f) throws IOException {
		MakeIndexMap(measures,measureIndex);
		MakeIndexMap(filters,filterIndex);
		MakeIndexMap(aggregates,aggIndex);
		MakeIndexMap(workMeasures,workerMeasureIndex);		
	
		BufferedReader r= new BufferedReader(new FileReader(f));
		r.readLine(); // skip header
		char splitChar = SPLIT_CHAR.charAt(0);
		if (f.getName().endsWith(".tsv")) splitChar = '\t';
		for (String l = r.readLine(); l != null; l=r.readLine()) {
			ArrayList<String> lineArray = new ArrayList<String>();
			boolean inQuote = false;
			for (int start=0,end=0; start<l.length(); start=end+1) {
				for (end=start; end<l.length() && (inQuote || l.charAt(end) != splitChar); end++) {
					if (l.charAt(end) == '"') inQuote = !inQuote;
					if (inQuote && end == l.length()-1) l += r.readLine(); //line break in the middle of a quote
				}
				lineArray.add(l.substring(start,end));
			}
//			System.out.println(lineArray);
			
			String sentId = this.getSentId(lineArray);
			Integer workId = this.getWorkId(lineArray);
			Set<String> annotSet = this.getAnnots(lineArray);
			
//			System.out.print(sentId+","+workId);
//			for (String s : annotSet) System.out.print("," + s);
//			System.out.println();

			if (!sentsMap.containsKey(sentId)) {
				sentsMap.put(sentId, lineArray);	
			}
			Map<String,Set<String>> workerSents = workerSentAnnot.get(workId);
			if (workerSents == null) {
				workerSents = new HashMap<String,Set<String>>();
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
	
	protected abstract void printWorkerMeasures(File f) throws FileNotFoundException;
	protected abstract void printSentenceMeasures(File f, boolean printVectors) throws FileNotFoundException;
	protected abstract Set<String> getAnnots(ArrayList<String> lineArray);
	protected abstract Integer getWorkId(ArrayList<String> lineArray);
	protected abstract String getSentId(ArrayList<String> lineArray);
	protected abstract Integer getNumCols();
	protected abstract boolean isFilteredWorker(Integer workid);

	public void buildConfusionMatrix() {
		confusionMatrix  = new HashMap<Integer,Map<Integer,Map<String,Set<String>>>>();
		for (int workid1 : workerSentAnnot.keySet()) {
			Map<String,Set<String>> work1Sents = workerSentAnnot.get(workid1);
			Map<Integer,Map<String,Set<String>>> work1row = new HashMap<Integer,Map<String,Set<String>>>();
			confusionMatrix.put(workid1, work1row);
			for (int workid2 : workerSentAnnot.keySet()) {
				if (workid1 == workid2) continue;
				Map<String,Set<String>> work2Sents = workerSentAnnot.get(workid2);
				Map<String,Set<String>> work1work2row = new HashMap<String, Set<String>>();
				work1row.put(workid2, work1work2row);
				for (String sentid : work1Sents.keySet()) {
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
		sentClusters = new HashMap<String,Dataset>();
		workers = new HashMap<Integer,Map<String,Instance>>();
		for (int workid1 : workerSentAnnot.keySet()) {
			Map<String,Set<String>> work1Sents = workerSentAnnot.get(workid1);
			Map<String,Instance> workerInstancesBySent = workers.get(workid1);
			if (workerInstancesBySent == null) {
				workerInstancesBySent = new HashMap<String,Instance>();
				workers.put(workid1, workerInstancesBySent);
			}
			for (String sentid : work1Sents.keySet()) {
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
		sentSumVectors = new HashMap<String,Instance>();
		sentMeasures = new HashMap<String,Instance>();
		for (SentenceMeasure m : measures) m.init(vectorIndex);
		for (String sentid : sentClusters.keySet()) {
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
		
		for (String sentid : sentClusters.keySet()) {
			for (AggregateSentenceMeasure agg : aggregates) {
				agg.next(sentClusters.get(sentid),sentSumVectors.get(sentid),sentMeasures.get(sentid));
			}
		}
		for (AggregateSentenceMeasure agg : aggregates) aggMeasures.put(aggIndex.get(agg),agg.value());
	}
	
	public void computeSentenceFilters() {
		sentFilters = new HashMap<String,Instance>();
		for (SentenceFilter f : filters) f.init(vectorIndex,measureIndex,aggIndex);
		for (String sentid : sentClusters.keySet()) {
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
					workerMeasuresForFilt.put(workerMeasureIndex.get(measure), 
							measure.call(workers.get(workid), confusionMatrix.get(workid), sentSumVectors, sentFilters));
				}
			}
		}
	}
	
	/**
	 * TODO major refactoring needed to do this correctly, for now this is quick&dirty
	 * Filter out workers from the workerSentAnnot that are identified as low quality
	 */
	public void filterWorkers() {
		int count = 0;
		for (Iterator<Entry<Integer, Map<String, Set<String>>>> workSentItor = workerSentAnnot.entrySet().iterator(); 
				workSentItor.hasNext();) {
			int workid = workSentItor.next().getKey();
			if (this.isFilteredWorker(workid)) {
				workSentItor.remove(); 
				count++;
			}			
		}
		if (count > 0)
			System.err.println("Removed " + count + " low quality workers.");
	}
	
	/**
	 * return a list of Measures ordered by the Instance index.  The index maps are indexed by 
	 * measure, so there is no way to iterate through them by number, which is needed to 
	 * control the order in printing only.  Inefficient, but only used in printing.
	 * 
	 */
	protected <T extends Measure> List<T> getMeasuresByIndex(Map<T, Integer> measureIndex) {
		List<T> rtn = new ArrayList<T>();
		for (int i=0; i<measureIndex.size();i++) {
			for (T m : measureIndex.keySet()) {
				if (measureIndex.get(m) == i) rtn.add(m);
			}
		}
		return rtn;
	}


	public Instance sentenceVector(Set<String> annots) {
		Instance v = new SparseInstance(vectorIndex.size());
		for (String annot : annots) v.put(vectorIndex.get(annot), 1.0);
		return v;
	}

	protected <T> void MakeIndexMap(T[] indices,	Map<T, Integer> map) {
		for (int i=0; i<indices.length; i++) map.put(indices[i], i);
	}

}