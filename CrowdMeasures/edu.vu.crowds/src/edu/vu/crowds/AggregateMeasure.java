package edu.vu.crowds;

import java.util.Map;

import net.sf.javaml.core.Instance;

public interface AggregateMeasure extends Measure {
	public void next(Instance measures);
	public Double value();
	void init(Map<? extends Measure, Integer> measureIndex);
}
