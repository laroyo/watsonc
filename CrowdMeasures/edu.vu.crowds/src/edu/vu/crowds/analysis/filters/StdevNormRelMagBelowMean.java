/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import java.util.Map;


import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedRelationMagnitude;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitude;

/**
 * @author welty
 *
 */
public class StdevNormRelMagBelowMean extends BelowDiffFilter {
	public StdevNormRelMagBelowMean() {}
	
	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<SentenceMeasure, Integer> measureIndex,
			Map<AggregateMeasure, Integer> aggIndex) {
		for (AggregateMeasure m : aggIndex.keySet()) {
			if (m instanceof MeanNormalizedRelationMagnitude) a1Index = aggIndex.get(m);
			if (m instanceof StdDevNormalizedRelationMagnitude) a2Index = aggIndex.get(m);
		}
		for (SentenceMeasure m : measureIndex.keySet()) {
			if (m instanceof NormalizedRelationMagnitude) mIndex = measureIndex.get(m);
		}
	}

	@Override
	public String label() {
		return "norm |R| < STDEV";
	}
}
