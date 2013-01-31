/**
 * 
 */
package edu.vu.crowds.analysis.workers;

import java.util.Map;
import java.util.Set;

import net.sf.javaml.core.Instance;

/**
 * @author welty
 *
 */
public class NumberOfSents implements WorkerMeasure {

	private Integer filterIndex;

	/**
	 * 
	 */
	public NumberOfSents() {	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#init(java.util.Map)
	 */
	@Override
	public void init(Integer filterIndex) {
		 this.filterIndex = filterIndex;
	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#call(net.sf.javaml.core.Dataset, net.sf.javaml.core.Instance)
	 */
	@Override
	public Double call(Map<Integer,Instance> workerSents,Map<Integer,Map<Integer,Set<String>>> workerAgreement,
			Map<Integer,Instance> sentSumVectors,Map<Integer, Instance> sentFilters) {
		Double count = 0.0;
		for (int sentid : workerSents.keySet()) {
			if (sentFilters.get(sentid).get(filterIndex) < 1) {
				count++;
			}
		}
		return count;
	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#label()
	 */
	@Override
	public String label() {
		return "# Sents";
	}
}
