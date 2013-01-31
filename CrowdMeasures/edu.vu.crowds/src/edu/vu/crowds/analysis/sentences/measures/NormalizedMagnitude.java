/**
 * 
 */
package edu.vu.crowds.analysis.sentences.measures;

import java.util.Map;

import edu.vu.crowds.analysis.sentences.SentenceMeasure;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


/**
 * Compute the magnitude of the sum vector
 * 
 * @author welty
 *
 */
public class NormalizedMagnitude implements SentenceMeasure {
	@Override
	public void init(Map<String, Integer> vectorIndex) { }

	@Override
	public Double call(Dataset sentCluster, Instance sumVector) {
		Double mag = 0.0;
		Double sum = 0.0;
		for (Double comp : sumVector.values()) {
			mag += comp * comp;
			sum += comp;
		}
		mag = Math.sqrt(mag) / sum;
		return mag;
	}

	@Override
	public String label() {
		return "norm |V|";
	}
	
}
