package edu.vu.crowds.analysis.relation;

import java.util.Map;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;

import edu.vu.crowds.AggregateMeasure;

public interface AggregateRelationMeasure extends AggregateMeasure {
	void init(Map<String,Integer> vectorIndex, Map<RelationMeasure,Integer> measureIndex);
	public void next(Dataset sentCluster, Instance sentSum,	Instance sentMeasures);
}
