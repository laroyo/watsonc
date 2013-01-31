/**
 * 
 */
package edu.vu.crowds.analysis.aggregates;

import java.util.ArrayList;
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
public abstract class StdDevMeasure implements AggregateMeasure {
	private ArrayList<Double> measures = null;
	protected int mIndex = -1;
	private Double sum;
	private Double count;

	/**
	 * 
	 */
	public StdDevMeasure() {	}

	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#init()
	 */
	@Override
	public void init(Map<? extends Measure, Integer> measureIndex) {
		measures = new ArrayList<Double>();
		count = 0.0;
		sum = 0.0;
	}

	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#next(net.sf.javaml.core.Dataset, net.sf.javaml.core.Instance, net.sf.javaml.core.Instance)
	 */
	@Override
	public void next(Instance iMeasures) {
		count++;
		sum += iMeasures.get(mIndex);
		measures.add(iMeasures.get(mIndex));
	}

	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#value()
	 */
	@Override
	public Double value() {
		Double mean = sum/count;
		Double sumsqdiff = 0.0;
		for (Double val : measures) {
			Double diff = val - mean;
			sumsqdiff += diff * diff;
		}
		return Math.sqrt(sumsqdiff/count);
	}
}
