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
public class AvgWorkerAgreement implements WorkerMeasure {
	private Integer filterIndex;

	/**
	 * 
	 */
	public AvgWorkerAgreement() {	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#init(java.util.Map)
	 */
	@Override
	public void init(Integer filterIndex) {
		 this.filterIndex = filterIndex;
	}

	/* # sents in common,# annots,# agree
	 * (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#call(net.sf.javaml.core.Dataset, net.sf.javaml.core.Instance)
	 */
	@Override
	public Double call(Map<String, Instance> workerSents,Map<Integer,Map<String,Set<String>>> workerAgreement,
			Map<String,Instance> sentSumVectors,Map<String, Instance> sentFilters) {
		Double weightedSum = 0.0;
		Double weightedCount = 0.0;
		/*
		 * Map from worker id -> Map from worker id -> Map from sentid -> Set of annots in common
		 * This is a 3-d confusion matrix.
		 * workerid1 -> workerid2 = empty Map if they annotated no sentences in common
		 * workerid1 -> workerid2 -> sentid = empty Set if they did not agree on that sent
		 */
		for (int workid2 : workerAgreement.keySet()) {
			Map<String,Set<String>> w2Agreement = workerAgreement.get(workid2);
			int sentsInCommon = 0;
			int hitCount = 0;
			Double annotCount = 0.0;
			for (String sentid : w2Agreement.keySet()) {
				if (sentFilters.get(sentid).get(filterIndex) < 1) {
					sentsInCommon++;
					Set<String> agreeForSent = w2Agreement.get(sentid);
					Instance work1Annots = workerSents.get(sentid);
					hitCount += agreeForSent.size();
					for (Double d : work1Annots.values()) annotCount += d;
				}
			}
			if (annotCount > 0.0) weightedSum += sentsInCommon * hitCount/annotCount ;
			weightedCount += sentsInCommon;
		}
		return weightedSum / weightedCount;
	}

	/* (non-Javadoc)
	 * @see edu.vu.crowds.analysis.workers.WorkerMeasure#label()
	 */
	@Override
	public String label() {
		return "Avg. Agreement";
	}
}
