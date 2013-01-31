/**
 * 
 */
package edu.vu.crowds.analysis.filters;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;

import edu.vu.crowds.analysis.sentences.SentenceFilter;

/**
 * @author welty
 *
 */
public abstract class BelowDiffFilter implements SentenceFilter {
	protected Integer a1Index=-1;
	protected Integer a2Index=-1;
	protected Integer mIndex=-1;
	protected Double factor = 1.0;

	/**
	 * 
	 */
	public BelowDiffFilter() {}
	
	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.SentenceFilter#call(net.sf.javaml.core.Dataset, net.sf.javaml.core.Instance, net.sf.javaml.core.Instance, net.sf.javaml.core.Instance)
	 */
	@Override
	public Double call(Dataset sentCluster, Instance sentSum, Instance sentMeasures, Instance aggMeasures) {
		if (sentMeasures.get(mIndex) < factor * (aggMeasures.get(a1Index) - aggMeasures.get(a2Index))) return 1.0;
		else return 0.0;
	}
}
