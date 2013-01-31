/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import java.util.Map;


import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.analysis.sentences.SentenceFilter;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanMagnitude;
import edu.vu.crowds.analysis.sentences.aggregates.StdDevMagnitude;
import edu.vu.crowds.analysis.sentences.measures.Magnitude;

/**
 * @author welty
 *
 */
public class StdevMagBelowMean extends BelowDiffFilter implements SentenceFilter {
	public StdevMagBelowMean() {}
	
	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<SentenceMeasure, Integer> measureIndex,
			Map<AggregateMeasure, Integer> aggIndex) {
		for (AggregateMeasure m : aggIndex.keySet()) {
			if (m instanceof MeanMagnitude) a1Index = aggIndex.get(m);
			if (m instanceof StdDevMagnitude) a2Index = aggIndex.get(m);
		}
		for (SentenceMeasure m : measureIndex.keySet()) {
			if (m instanceof Magnitude) mIndex = measureIndex.get(m);
		}
	}

	@Override
	public String label() {
		return "|V| < STDEV";
	}
}
