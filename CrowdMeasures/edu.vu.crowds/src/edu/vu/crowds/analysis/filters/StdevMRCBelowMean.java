/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import java.util.Map;

import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.analysis.sentences.SentenceFilter;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanMaxRelationCosine;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevMaxRelationCosine;
import edu.vu.crowds.analysis.sentences.measures.MaxRelationCosine;

/**
 * @author welty
 *
 */
public class StdevMRCBelowMean extends BelowDiffFilter implements SentenceFilter {
	public StdevMRCBelowMean() {}
	
	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<SentenceMeasure, Integer> measureIndex,
			Map<AggregateMeasure, Integer> aggIndex) {
		for (AggregateMeasure m : aggIndex.keySet()) {
			if (m instanceof MeanMaxRelationCosine) a1Index = aggIndex.get(m);
			if (m instanceof StdDevMaxRelationCosine) a2Index = aggIndex.get(m);
		}
		for (SentenceMeasure m : measureIndex.keySet()) {
			if (m instanceof MaxRelationCosine) mIndex = measureIndex.get(m);
		}
	}

	@Override
	public String label() {
		return "MRC < STDEV";
	}
}
