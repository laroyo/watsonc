/**
 * 
 */
package edu.vu.crowds.analysis.workers;

import java.util.Map;
import java.util.Set;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.Measure;

/**
 * @author welty
 *
 */
public interface WorkerMeasure extends Measure {
	
	public void init(Integer filterIndex);
	/**
	 * Return NaN if all the sentences are filtered.
	 */
	public Double call(Map<String, Instance> workerSents,
			Map<String, Map<String, Set<String>>> workerAgreement,
			Map<String, Instance> sentSumVectors, Map<String, Instance> sentFilters);
}
