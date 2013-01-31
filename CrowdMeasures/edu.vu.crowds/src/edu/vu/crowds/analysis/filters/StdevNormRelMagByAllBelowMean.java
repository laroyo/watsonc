/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import java.util.Map;


import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanNormalizedRelationMagnitudeByAll;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevNormalizedRelationMagnitudeByAll;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitudeByAll;

/**
 * @author welty
 *
 */
public class StdevNormRelMagByAllBelowMean extends BelowDiffFilter {
	public StdevNormRelMagByAllBelowMean() {}
	
	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<SentenceMeasure, Integer> measureIndex,
			Map<AggregateMeasure, Integer> aggIndex) {
		for (AggregateMeasure m : aggIndex.keySet()) {
			if (m instanceof MeanNormalizedRelationMagnitudeByAll) a1Index = aggIndex.get(m);
			if (m instanceof StdDevNormalizedRelationMagnitudeByAll) a2Index = aggIndex.get(m);
		}
		for (SentenceMeasure m : measureIndex.keySet()) {
			if (m instanceof NormalizedRelationMagnitudeByAll) mIndex = measureIndex.get(m);
		}
	}

	@Override
	public String label() {
		return "norm-all |R| < STDEV";
	}
}
