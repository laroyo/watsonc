/**
 * 
 */
package edu.vu.crowds.analysis.workers;

import java.util.Map;
import java.util.Set;

import net.sf.javaml.core.Instance;
import net.sf.javaml.distance.CosineDistance;

/**
 * @author welty
 *
 */
public class WorkerCosine implements WorkerMeasure {

	private CosineDistance cos;
	private Integer filterIndex;

	/**
	 * 
	 */
	public WorkerCosine() {	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#init(java.util.Map)
	 */
	@Override
	public void init(Integer filterIndex) {
		 cos = new CosineDistance();
		 this.filterIndex = filterIndex;
	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#call(net.sf.javaml.core.Dataset, net.sf.javaml.core.Instance)
	 */
	@Override
	public Double call(Map<String, Instance> workerSents,Map<Integer,Map<String,Set<String>>> workerAgreement,
			Map<String,Instance> sentSumVectors,Map<String, Instance> sentFilters) {
		Double sumCos = 0.0;
		Double count = 0.0;
		for (String sentid : workerSents.keySet()) {
			if (sentFilters.get(sentid).get(filterIndex) < 1) {
				Instance sentSumVec = sentSumVectors.get(sentid);
				Instance workSent = workerSents.get(sentid);
				sentSumVec = sentSumVec.minus(workSent);
				sumCos += cos.measure(sentSumVec,workSent);
				count++;
			}
		}
		return sumCos/count;
	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#label()
	 */
	@Override
	public String label() {
		return "Cos";
	}

}
