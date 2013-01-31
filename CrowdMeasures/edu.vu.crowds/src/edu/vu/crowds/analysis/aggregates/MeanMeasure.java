/**
 * 
 */
package edu.vu.crowds.analysis.aggregates;

import java.util.Map;

import net.sf.javaml.core.Instance;

import edu.vu.crowds.AggregateMeasure;
import edu.vu.crowds.Measure;

/**
 * Compute the mean of a sentence Measure.  Override the init method 
 * to specify which.
 * @author welty
 *
 */
public abstract class MeanMeasure implements AggregateMeasure {
	private Double sum;
	private int count;
	protected int index; // The index for the next method to get the measure to find the mean

	/**
	 * 
	 */
	public MeanMeasure() {	}

	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#init()
	 */
	public void init(Map<? extends Measure,Integer> measures) {
		sum = 0.0;
		count = 0;
		index = -1;
	}

	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#next(net.sf.javaml.core.Dataset, net.sf.javaml.core.Instance, net.sf.javaml.core.Instance)
	 */
	public void next(Instance measures) {
		count++;
		sum += measures.get(index);
	}

	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#value()
	 */
	public Double value() {
		return sum / count;
	}

}
