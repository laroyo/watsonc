/**
 * 
 */
package edu.vu.crowds.analysis.sentences.aggregates;

import java.util.Map;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


import edu.vu.crowds.analysis.aggregates.StdDevMeasure;
import edu.vu.crowds.analysis.sentences.AggregateSentenceMeasure;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.measures.NormalizedRelationMagnitude;

/**
 * @author welty
 *
 */
public class StdDevNormalizedRelationMagnitude extends StdDevMeasure implements AggregateSentenceMeasure {

	/**
	 * 
	 */
	public StdDevNormalizedRelationMagnitude() { super(); }

	public void init (Map<String,Integer> vectorIndex, Map<SentenceMeasure,Integer> measureIndex) {
		super.init(measureIndex);
		for (SentenceMeasure m : measureIndex.keySet()) {
			if (m instanceof NormalizedRelationMagnitude) mIndex = measureIndex.get(m);
		}
	}

	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#label()
	 */
	@Override
	public String label() {
		return "norm |R| Stdev";
	}

	@Override
	public void next(Dataset sentCluster, Instance sentSum, Instance sentMeasures) {
		super.next(sentMeasures);
	}

}
