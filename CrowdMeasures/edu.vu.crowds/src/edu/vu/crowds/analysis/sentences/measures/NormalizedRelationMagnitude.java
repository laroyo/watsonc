/**
 * 
 */
package edu.vu.crowds.analysis.sentences.measures;

import java.util.Map;

import edu.vu.crowds.analysis.sentences.SentenceMeasure;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


/**
 * Compute the magnitude of the sum vector for just the relations
 * (exclude OTHER and NONE)
 * 
 * @author welty
 *
 */
public class NormalizedRelationMagnitude implements SentenceMeasure {
	int othIndex=-1;
	int nonIndex=-1;
	String OTHER = "other";
	String NONE = "none";
	
	@Override
	public void init(Map<String, Integer> vectorIndex) { 
		for (String rel : vectorIndex.keySet()) {
			if (rel.equalsIgnoreCase(OTHER)) othIndex = vectorIndex.get(rel);
			if (rel.equalsIgnoreCase(NONE)) nonIndex = vectorIndex.get(rel);
		}
	}

	@Override
	public Double call(Dataset sentCluster, Instance sumVector) {
		Double mag = 0.0;
		Double sum = 0.0;
		for (int i=0; i<sumVector.keySet().size(); i++) {
			if (i != othIndex && i != nonIndex) {
				Double comp = sumVector.get(i);
				mag += comp * comp;
				sum += comp;
			}
		}
		mag = Math.sqrt(mag) / sum;
		return mag;
	}

	@Override
	public String label() {
		return "norm |R|";
	}
	
}
