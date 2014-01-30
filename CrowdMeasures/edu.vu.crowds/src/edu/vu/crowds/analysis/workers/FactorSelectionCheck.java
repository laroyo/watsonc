package edu.vu.crowds.analysis.workers;

import java.util.Map;
import java.util.Set;

import net.sf.javaml.core.Instance;

public class FactorSelectionCheck implements WorkerMeasure {
	
	private Integer filterIndex;

	@Override
	public String label() {
		// TODO Auto-generated method stub
		return "# wrong selections";
	}

	@Override
	public void init(Integer filterIndex) {
		// TODO Auto-generated method stub
		this.filterIndex = filterIndex;
	}

	@Override
	public Double call(Map<String, Instance> workerSents,Map<String,Map<String,Set<String>>> workerAgreement,
			Map<String,Instance> sentSumVectors,Map<String, Instance> sentFilters) {
		// TODO Auto-generated method stub
		Double selCheck = 0.0;
		Double count = 0.0;
		for (String sentid : workerSents.keySet()) {
			if (sentFilters.get(sentid).get(filterIndex) < 1) {
				Instance workSent = workerSents.get(sentid);
				if (workSent.containsKey(8)) selCheck += 1.0;
				// System.out.println(workSent.keySet().toString());
				count += 1.0;
			}
		}
		//System.out.println(selCheck + " / " + count);
		
		return selCheck / count;
	}

}
