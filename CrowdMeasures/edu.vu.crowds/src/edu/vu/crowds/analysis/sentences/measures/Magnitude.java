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
public class Magnitude implements SentenceMeasure {
	@Override
	public Double call(Dataset sentCluster, Instance sumVector) {
		Double mag = 0.0;
		for (Double comp : sumVector.values()) {
			mag += comp * comp;
		}
		mag = Math.sqrt(mag);
		return mag;
	}

	@Override
	public void init(Map<String, Integer> vectorIndex) { }

	@Override
	public String label() {
		return "|V|";
	}
	
}
