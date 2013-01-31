package edu.vu.crowds.analysis.sentences;

import java.util.Map;

import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.Measure;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


public interface SentenceFilter extends Measure {
	void init(Map<String,Integer> vectorIndex, Map<SentenceMeasure, Integer> measureIndex, Map<AggregateMeasure,Integer> aggIndex);
	Double call(Dataset sentCluster, Instance sentSum, Instance sentMeasures, Instance aggMeasures);
	public String label();
}
