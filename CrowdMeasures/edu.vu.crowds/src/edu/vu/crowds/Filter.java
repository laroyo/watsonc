package edu.vu.crowds;

import java.util.Map;

import net.sf.javaml.core.Instance;


public interface Filter extends Measure {
	void init(Map<? extends Measure, Integer> measureIndex, Map<? extends AggregateMeasure,Integer> aggIndex);
	Double call(Instance measures, Instance aggMeasures);
}
