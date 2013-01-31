/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import net.sf.javaml.core.Instance;

import edu.vu.crowds.Filter;

/**
 * @author welty
 *
 */
public abstract class BelowMean implements Filter {
	protected Integer meanIndex=-1;
	protected Integer magIndex=-1;

	/**
	 * extend to specify the measures to test
	 */
	public BelowMean() {}
	
	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.SentenceFilter#call(net.sf.javaml.core.Dataset, net.sf.javaml.core.Instance, net.sf.javaml.core.Instance, net.sf.javaml.core.Instance)
	 */
	@Override
	public Double call(Instance measures, Instance aggMeasures) {
		if (measures.get(magIndex) < aggMeasures.get(meanIndex)) return 1.0;
		else return 0.0;
	}
}
