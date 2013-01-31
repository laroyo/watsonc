/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import java.util.Map;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.analysis.sentences.SentenceFilter;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;

/**
 * @author welty
 *
 */
public class PassAll implements SentenceFilter {

	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<SentenceMeasure, Integer> measureIndex,
			Map<AggregateMeasure, Integer> aggIndex) {
	}

	@Override
	public Double call(Dataset sentCluster, Instance sentSum,
			Instance sentMeasures, Instance aggMeasures) {
		return 0.0;
	}

	@Override
	public String label() {
		return "No Filter";
	}
}
