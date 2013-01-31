/**
 * 
 */
package edu.vu.crowds.analysis.sentences.aggregates;

import java.util.Map;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


import edu.vu.crowds.analysis.aggregates.MeanMeasure;
import edu.vu.crowds.analysis.sentences.AggregateSentenceMeasure;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitude;

/**
 * @author welty
 *
 */
public class MeanNormalizedRelationMagnitude extends MeanMeasure implements AggregateSentenceMeasure {

	/**
	 * 
	 */
	public MeanNormalizedRelationMagnitude() { super(); }
	
	public void init(Map<String,Integer> vectorIndex, Map<SentenceMeasure,Integer> measureIndex) {
		super.init(measureIndex);
		for (SentenceMeasure m : measureIndex.keySet()) {
			if (m instanceof NormalizedRelationMagnitude) index = measureIndex.get(m);
		}
	}
	
	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#init()
	 */
	@Override
	public String label() {
		return "norm |R| Mean";
	}

	@Override
	public void next(Dataset sentCluster, Instance sentSum,	Instance sentMeasures) {
		super.next(sentMeasures);
	}
}
