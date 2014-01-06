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
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.DefaultDataset;
import net.sf.javaml.core.DenseInstance;
import net.sf.javaml.core.Instance;
import net.sf.javaml.core.SparseInstance;
import net.sf.javaml.distance.AbstractDistance;
import net.sf.javaml.distance.AngularDistance;
import net.sf.javaml.distance.CosineDistance;


import edu.vu.crowds.analysis.filters.StdevMagBelowMean;
import edu.vu.crowds.analysis.filters.StdevNormMagBelowMean;
import edu.vu.crowds.analysis.filters.StdevNormRelMagBelowMean;
import edu.vu.crowds.analysis.filters.StdevNormRelMagByAllBelowMean;
import edu.vu.crowds.analysis.sentences.SentenceFilter;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedRelationMagnitudeByAll;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedRelationMagnitudeByAll;
import edu.vu.crowds.analysis.sentences.measures.Magnitude;
import edu.vu.crowds.analysis.sentences.measures.NormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitudeByAll;

/**
 * 
 * Inputs annotation data from workers.  Takes sentence id, workerid, annotations, seed-type
 * 
 * Worker matrix computes for each pair of workers for the sentences they've mutually annotated, how often then agree
 * 
 * _unit_id	_worker_id	step_1_select_the_valid_relations	step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1	step_2b_if_you_selected_none_in_step_1_explain_why	b1	b2	e1	e2	id	relation-type	sentence	term1	term2
	*	
 * @author welty
 * @deprecated
 */
@SuppressWarnings(value = { "all" })
public class Cluster {

	/**
	 * Map from workerid -> Map from sent id -> worker annotation vector
	 */
	Map<Integer,Map<Integer,Instance>> workers;
	/**
	 * Map from sentid -> Dataset of worker annotation vectors
	 */
	Map<Integer,Dataset> sentClusters;
	/**
	 * Map from workerid to their <avg. cosine, avg angle> distance from each sentence vector sum
	 */
//	Map<Integer,Pair<Float,Float>> workerAvgs = new HashMap<Integer,Pair<Float,Float>>();
	/**
	 * Map from relation names to vector index
	 */
	Map<String,Integer> vectorIndex = new HashMap<String,Integer>();
	/**
	 * Map from sentid -> seed relation name
	 */
	Map<Integer,String> sentSeedMap = new HashMap<Integer,String>();
	/**
	 * Map from worker id -> Map from sent id -> set of annotations
	 * This is for reading in the csv data
	 * TODO probably this can be removed and read data directly into workers field above
	 */
	Map<Integer,Map<Integer,Set<String>>> workerSentAnnot = new HashMap<Integer,Map<Integer,Set<String>>>();
	/**
	 * Map from worker id -> Map from worker id -> [# sents in common,# annots,# agree]
	 */
	Map<Integer,Map<Integer,Integer[]>> confusionMatrix = new HashMap<Integer,Map<Integer,Integer[]>>();
	/**
	 * Map from sent id -> vector sum of annots on sent
	 */
	Map<Integer,Instance> sentSumVectors = new HashMap<Integer,Instance>();
	/**
	 * Map from sent id -> instance containing metrics for the sent. 
	 */
	Map<Integer,Instance> sentMeasures = new HashMap<Integer,Instance>();
	/**
	 * The sentence metrics, these are the indexes of sentMeasures instances. This should be done with an enum!
	 * "|S|","|S|-norm","Max","|Rels|","|Rels|-norm","|Rels|-normByAll","|Rels|-normByAllSmooth"
	 */
	SentenceMeasure[] measures = {
			new Magnitude(),
			new NormalizedMagnitude(),
			new NormalizedRelationMagnitude(),
			new NormalizedRelationMagnitudeByAll(),
	};
	/**
	 * Map from measure -> sentence measure index 
	 */
	Map<SentenceMeasure,Integer> measureIndex = new HashMap<SentenceMeasure,Integer>();
	/**
	 * The aggregate metrics, these are the indexes of the aggMeasures instance. This should be done with an enum!
	 * "|S|","|S|-norm","Max","|Rels|","|Rels|-norm","|Rels|-normByAll","|Rels|-normByAllSmooth"
	 */
	AggregateMeasure[] aggregates = {
			new MeanMagnitude(), 
			new StdDevMagnitude(),
			new MeanNormalizedMagnitude(),
			new StdDevNormalizedMagnitude(),
			new MeanNormalizedRelationMagnitude(),
			new StdDevNormalizedRelationMagnitude(),
			new MeanNormalizedRelationMagnitudeByAll(),
			new StdDevNormalizedRelationMagnitudeByAll(),
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
	 * Map from sent id -> instance containing filtered flags for the sent according to filter metrics
	 */
	Map<Integer,Instance> sentFilters = null;
	/**
	 * The sentence filters, these are the indexes of sentFilters instances. This should be done with an enum!
	 * "|S|-norm<1std","|Rels|-norm<1std","|Rels|-normByAll<1std","|Rels|-normByAllSmooth<1std"};
	 */
	SentenceFilter[] filters = {
			new StdevMagBelowMean(),
			new StdevNormMagBelowMean(),
			new StdevNormRelMagBelowMean(),
			new StdevNormRelMagByAllBelowMean(),
//			new BelowMean()
	};
	/**
	 * Map from filter to filter index
	 */
	Map<SentenceFilter,Integer> filterIndex = new HashMap<SentenceFilter,Integer>();
	
	Cluster(String filename) throws IOException {
		init(new File(filename));
	}
	
	public void init(File f) throws IOException {
		MakeIndexMap(measures,measureIndex);
		MakeIndexMap(filters,filterIndex);
		MakeIndexMap(aggregates,aggIndex);
		
	
		BufferedReader r= new BufferedReader(new FileReader(f));
		r.readLine(); // skip header
		int index=0;
		for (String l = r.readLine(); l != null; l=r.readLine()) {
			String[] lineArray = l.split("\t");
			while (lineArray.length < 14) {
				l += r.readLine();
				lineArray = l.split("\t");
			}
			// for (int i=0; i<lineArray.length; i++) System.out.println(lineArray[i]);
			Integer sentId = Integer.decode(lineArray[0]);
			Integer workId = Integer.decode(lineArray[1]);
			String choices = lineArray[2].replace("\"", "");
			String seedRel = lineArray[10];
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
			Map<Integer,Integer[]> work1row = new HashMap<Integer,Integer[]>();
			confusionMatrix.put(workid1, work1row);
			for (int workid2 : workerSentAnnot.keySet()) {
				if (workid1 == workid2) {
					Integer[] vals = {1,1,1};
					work1row.put(workid2, vals);
				} else {
					Map<Integer,Set<String>> work2Sents = workerSentAnnot.get(workid2);
					int sentsInCommon = 0;
					int hitCount = 0;
					int annotCount = 0;
					for (int sentid : work1Sents.keySet()) {
						if (work2Sents.containsKey(sentid)) {
							Set<String> work1Annots = work1Sents.get(sentid);
							Set<String> work2Annots = work2Sents.get(sentid);
							sentsInCommon++;
							for (String annot : work1Annots) {
								annotCount++;
								if (work2Annots.contains(annot)) hitCount++;
							}
						}
					}
					Integer[] vals = {sentsInCommon,annotCount,hitCount};
					work1row.put(workid2, vals);
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
			Instance sumVec = sumVector(sentClusters.get(sentid));
			sentSumVectors.put(sentid,sumVec);
			Instance sentMeasure = new DenseInstance(measures.length);
			sentMeasures.put(sentid,sentMeasure);
			for (SentenceMeasure measure : measures) {
				sentMeasure.put(measureIndex.get(measure), measure.call(sentClusters.get(sentid),sumVec));
			}
		}
	}
	
	public void computeAggregateMeasures() {
		aggMeasures = new DenseInstance(aggregates.length);
//		for (AggregateMeasure agg : aggregates) agg.init(vectorIndex,measureIndex);
		
		for (int sentid : sentClusters.keySet()) {
			for (AggregateMeasure agg : aggregates) {
//				agg.next(sentClusters.get(sentid),sentSumVectors.get(sentid),sentMeasures.get(sentid));
			}
		}
		for (AggregateMeasure agg : aggregates) aggMeasures.put(aggIndex.get(agg),agg.value());
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
	
	public void computeWorkerAverages() {

		AbstractDistance cos = new CosineDistance();
		AbstractDistance ang = new AngularDistance(); 
		for (int workid : workers.keySet()) {
			Map<Integer,Instance> workerSents = workers.get(workid);
			float sumCos = 0;
			float sumAng = 0;
			float count = 0;
			for (int sentid : workerSents.keySet()) {
				Instance sentSumVec = sentSumVectors.get(sentid);
				Instance workSent = workerSents.get(sentid);
				sentSumVec = sentSumVec.minus(workSent);
				sumCos += cos.measure(sentSumVec,workSent);
				sumAng += ang.measure(sentSumVec, workSent);
				count++;
			}
//			workerAvgs.put(workid,new Pair<Float,Float>((float) sumCos/count, (float) sumAng/count));
		}
	}
	
	public void printConfusionMatrix(File f) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);
	
		out.print("id");
		for (int work1id : confusionMatrix.keySet()) {
			out.print("," + work1id);
		}
		out.println(",Weighted Avg,Avg. Cos.,Avg. Ang.");
		
		for (int work1id : confusionMatrix.keySet()) {
			out.print(work1id);
			int weightedSum = 0;
			int weightedCount = 0;
			Map<Integer,Integer[]> work1row = confusionMatrix.get(work1id);
			for (int work2id : confusionMatrix.keySet()) {
				Integer[] work2cell = work1row.get(work2id);
				if (work2cell[0] > 0) {
					out.print(","+(float) work2cell[2]/work2cell[1] + " (" + work2cell[0] + ")");
					weightedSum += work2cell[0] * work2cell[2]/work2cell[1];
					weightedCount += work2cell[0];
				} else {
					out.print(", ");
				}
			}
			//out.println(", " + (float) weightedSum/weightedCount + "," + workerAvgs.get(work1id));
		}	
	}

	public void printConfusionMatrix() throws FileNotFoundException {
		printConfusionMatrix(null);
	}

	private void printSentenceVectors(File f) throws FileNotFoundException {
		PrintStream out;
		if (f == null) out = System.out;
		else out = new PrintStream(f);
	
		out.print("Sent id");
		for (int i=0; i<vectorIndex.size();i++) {
			for (String label : vectorIndex.keySet()) {
				if (vectorIndex.get(label) == i) out.print(","+label);
			}
		}
		for (int i=0; i<measureIndex.size();i++) out.print(","+measures[i].label());
		for (int i=0; i<filterIndex.size();i++) out.print(","+filters[i].label());
		out.println();
		
		for (int sentid : sentSumVectors.keySet()) {
			out.println(sentid+","+instanceString(sentSumVectors.get(sentid))+","+instanceString(sentMeasures.get(sentid))+","+
					instanceString(sentFilters.get(sentid)));
		}
		
		out.println();
		for (int i=0; i<aggIndex.size();i++) out.print(","+aggregates[i].label());
		out.println();
		for (int i=0; i<aggIndex.size();i++) out.print(","+aggMeasures.get(i));
		out.println();
	}

	private String instanceString(Instance instance) {
		String rtn = "";
		int i=0;
		while (instance.containsKey(i)) {
			rtn += instance.get(i) + ",";
			i++;
		}
		return rtn.substring(0,rtn.length()-1);
	}

	public Instance sentenceVector(Set<String> annots) {
		Instance v = new SparseInstance(vectorIndex.size());
		for (String annot : annots) v.put(vectorIndex.get(annot), 1.0);
		return v;
	}

	private Instance sumVector(Dataset dataset) {
		Instance sum = new SparseInstance(vectorIndex.size());
		for (Instance i : dataset) sum = sum.add(i);
		return sum;
	}

	private <T> void MakeIndexMap(T[] indices,	Map<T, Integer> map) {
		for (int i=0; i<indices.length; i++) map.put(indices[i], i);
	}

	/**
	 * @param args
	 */
	public static void main(String[] args) {
	
		try {
			Cluster c = new Cluster(args[0]);
			c.buildConfusionMatrix();
			c.buildSentenceClusters();
			c.computeSentenceMeasures();
			c.computeAggregateMeasures();
			c.computeSentenceFilters();
			if (args.length == 1)
				c.printConfusionMatrix();
			else 
				c.printConfusionMatrix(new File(args[1]));
			if (args.length == 3)
				c.printSentenceVectors(new File(args[2]));
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

}
