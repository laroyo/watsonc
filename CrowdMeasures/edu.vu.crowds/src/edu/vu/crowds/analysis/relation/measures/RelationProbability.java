/**
 * 
 */
package edu.vu.crowds.analysis.relation.measures;

import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.JavaMlUtils;
import edu.vu.crowds.analysis.relation.RelationMeasure;


/**
 * Compute the probability that a relation appears at least once in a sent
 * 
 * @author welty
 *
 */
public class RelationProbability implements RelationMeasure {
	@Override
	public Double call(Integer relIndex, Map<String,Instance> sentSumVectors) {
		double count = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIndex).intValue() > 0) count++;
		}
		return count / (new Double(sentSumVectors.size()));
	}

	@Override
	public void init(Map<String, Integer> vectorIndex) { }

	@Override
	public String label() {
		return "P(R)";
	}
	
}
