/**
 * 
 */
package edu.vu.crowds.analysis.relation.measures;

import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.relation.RelationMeasure;


/**
 * Compute the maximum clarity score of a relation across all sents
 * 
 * @author welty
 *
 */
public class RelationMaxClarity implements RelationMeasure {
	@Override
	public Double call(Integer relIndex, Map<String,Instance> sentSumVectors) {
		double max = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIndex).compareTo(max) > 0) 
				max = sentSumEntry.getValue().get(relIndex);
		}
		return max;
	}

	@Override
	public void init(Map<String, Integer> vectorIndex) { }

	@Override
	public String label() {
		return "RClar";
	}
	
}
