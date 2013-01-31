/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import java.util.Map;


import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedMagnitude;
import edu.vu.crowds.analysis.sentences.measures.NormalizedMagnitude;

/**
 * @author welty
 *
 */
public class StdevNormMagBelowMean extends BelowDiffFilter {
	public StdevNormMagBelowMean() {}
	
	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<SentenceMeasure, Integer> measureIndex,
			Map<AggregateMeasure, Integer> aggIndex) {
		for (AggregateMeasure m : aggIndex.keySet()) {
			if (m instanceof MeanNormalizedMagnitude) a1Index = aggIndex.get(m);
			if (m instanceof StdDevNormalizedMagnitude) a2Index = aggIndex.get(m);
		}
		for (SentenceMeasure m : measureIndex.keySet()) {
			if (m instanceof NormalizedMagnitude) mIndex = measureIndex.get(m);
		}
	}

	@Override
	public String label() {
		return "norm |V| < STDEV";
	}
}
