/**
 * 
 */
package edu.vu.crowds.analysis.sentences.filters;

import java.util.Map;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.Measure;
import edu.vu.crowds.analysis.filters.BelowMean;
import edu.vu.crowds.analysis.sentences.SentenceFilter;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.aggregates.MeanMagnitude;
import edu.vu.crowds.analysis.sentences.measures.Magnitude;

/**
 * @author welty
 *
 */
public class BelowMeanSentence extends BelowMean implements SentenceFilter {
	/**
	 * 
	 */
	public BelowMeanSentence() {}
	
	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<SentenceMeasure, Integer> measureIndex,
			Map<AggregateMeasure, Integer> aggIndex) {
		init(measureIndex,aggIndex);
	}

	@Override
	public void init(Map<? extends Measure, Integer> measureIndex,
			Map<? extends AggregateMeasure, Integer> aggIndex) {
		for (AggregateMeasure m : aggIndex.keySet()) {
			if (m instanceof MeanMagnitude) meanIndex = aggIndex.get(m);
		}
		for (Measure m : measureIndex.keySet()) {
			if (m instanceof Magnitude) magIndex = measureIndex.get(m);
		}
	}

	@Override
	public String label() {
		return "|V| < Mean";
	}

	@Override
	public Double call(Dataset sentCluster, Instance sentSum,
			Instance sentMeasures, Instance aggMeasures) {
		return super.call(sentMeasures, aggMeasures);
	}
}
