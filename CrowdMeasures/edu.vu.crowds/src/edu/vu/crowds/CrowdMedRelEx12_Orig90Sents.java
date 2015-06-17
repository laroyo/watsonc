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
import java.util.Map.Entry;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.filters.PassAll;
import edu.vu.crowds.analysis.filters.StdevMRCBelowMean;
import edu.vu.crowds.analysis.relation.RelationMeasure;
import edu.vu.crowds.analysis.relation.RelationRelationMeasure;
import edu.vu.crowds.analysis.relation.measures.RelationCount;
import edu.vu.crowds.analysis.relation.measures.RelationMaxClarity;
import edu.vu.crowds.analysis.relation.measures.RelationProbability;
import edu.vu.crowds.analysis.relation.measures.RelationTopProbability;
import edu.vu.crowds.analysis.relation.relrel_measures.RelRelCondProbMinusRelProb;
import edu.vu.crowds.analysis.relation.relrel_measures.RelationRelationCausalPower;
import edu.vu.crowds.analysis.relation.relrel_measures.RelationRelationConditionalProbability;
import edu.vu.crowds.analysis.relation.relrel_measures.RelationRelationNegConditionalProbability;
import edu.vu.crowds.analysis.relation.relrel_measures.RelationTopRelationConditionalProbability;
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
public class CrowdMedRelEx12_Orig90Sents extends CrowdTruthRelEx {
	/**
	 * The split char
	 */
	final static String SPLIT_CHAR = ",";

	CrowdMedRelEx12_Orig90Sents(String filename) throws IOException {
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
		relMeasures = new RelationMeasure[] {
				new RelationCount(),
				new RelationProbability(),
				new RelationTopProbability(),
				new RelationMaxClarity(),
		};
		relRelMeasures = new RelationRelationMeasure[] {
				new RelationRelationConditionalProbability(),
				new RelationTopRelationConditionalProbability(),
				new RelRelCondProbMinusRelProb(),
				new RelationRelationNegConditionalProbability(),
				new RelationRelationCausalPower(),
		};
		workMeasures = new WorkerMeasure[] {
				new NumberOfSents(),
				new WorkerCosine(),
				new AvgWorkerAgreement(),
				new AnnotsPerSent(),
		};
		filters = new SentenceFilter[] {
				new PassAll(),
				new StdevMRCBelowMean(),
		};

		init(new File(filename));

	}
	
	public void init(File f) throws IOException {
		super.init(f);
	}
	
//	protected static Pattern annotsPattern = Pattern.compile("(\\[[^:]*:\\])|(\\[[^\\]]*\\]:)");
	protected static Pattern annotsPattern = Pattern.compile("\\[[^\\]]*\\]");
	@Override
	protected Set<String> getAnnots(ArrayList<String> lineArray) {
		HashSet<String> annots = new HashSet<String>();
		String choices = lineArray.get(2).replace("\"", "");
		Matcher m = annotsPattern.matcher(choices);
		while (m.find()) {
			String annot = m.group().replaceAll(":|\\]|\\[|\\s", "").toLowerCase();
			annots.add(annot);
			if (!vectorIndex.containsKey(annot))
				vectorIndex.put(annot,vectorIndex.size());
		}
		return annots;
	}

	@Override
	protected String getWorkId(ArrayList<String> lineArray) {
		return lineArray.get(1);
	}

	@Override
	protected String getSentId(ArrayList<String> lineArray) {
		return lineArray.get(0);
	}
	
	@Override
	protected Integer getNumCols() { return 23; }

	@Override
	protected boolean isFilteredWorker(String workid) {
		List<SentenceFilter> filterList = getMeasuresByIndex(filterIndex);
		List<WorkerMeasure> measureList = getMeasuresByIndex(workerMeasureIndex);
		int findex = filterIndex.get(filterList.get(0)); // the passAll sentence filter
		Map<Integer,Instance> w = workerMeasures.get(workid);
		Instance measures = w.get(findex);
		Double numSents = measures.get(workerMeasureIndex.get(measureList.get(0)));
		if (numSents < 3) return true; 
		else return false;
	}

	// Headroom experiment to try: merge useless relations (too common, too infrequent) into other
	// 	merging similar relations
	protected void filterSentencesFromRelations() {
		for (Entry<String, Map<String, Set<String>>> workSentEnt : workerSentAnnot.entrySet()) {
			for (Entry<String, Set<String>> sentEnt : workSentEnt.getValue().entrySet()) {
				if (sentEnt.getValue().remove("contraindicates"))
					sentEnt.getValue().add("other");
				if (sentEnt.getValue().remove("part_of"))
					sentEnt.getValue().add("is_a");
				if (sentEnt.getValue().remove("associated_with"))
					sentEnt.getValue().add("other");
				if (sentEnt.getValue().remove("prevents"))
					sentEnt.getValue().add("treats");
				if (sentEnt.getValue().remove("manifestation"))
					sentEnt.getValue().add("side_effect");
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
	
		int[] origCols = {14,15,0,16,17,19,19,20,21,22};
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


	/**
	 * input-file workers-file sentence-file
	 */
	public static void main(String[] args) {
		try {
			CrowdMedRelEx12_Orig90Sents c = new CrowdMedRelEx12_Orig90Sents(args[0]);
			c.buildConfusionMatrix();
			c.buildSentenceClusters();
			c.computeSentenceMeasures();
			c.computeAggregateSentenceMeasures();
			c.computeSentenceFilters();
			c.computeWorkerMeasures();
			c.computeRelationMeasures();
			c.computeRelationRelationMeasures();
//			c.filterWorkers();
//			c.filterSentencesFromRelations();
//			c.buildConfusionMatrix();
//			c.buildSentenceClusters();
//			c.computeSentenceMeasures();
//			c.computeAggregateSentenceMeasures();
//			c.computeSentenceFilters();
//			c.computeWorkerMeasures();
//			c.computeRelationMeasures();
//			c.computeRelationRelationMeasures();
//			c.printWorkerMeasures(new File(args[1]));
//			c.printSentenceMeasures(new File(args[1]),true);
//			c.filterWorkers();
//			c.buildConfusionMatrix();
//			c.buildSentenceClusters();
//			c.computeSentenceMeasures();
//			c.computeAggregateSentenceMeasures();
//			c.computeRelationMeasures();
//			c.computeRelationRelationMeasures();
			c.printRelationMeasures(new File(args[1]));
//			c.printSentenceMeasures(new File(args[2]),true);
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

}
