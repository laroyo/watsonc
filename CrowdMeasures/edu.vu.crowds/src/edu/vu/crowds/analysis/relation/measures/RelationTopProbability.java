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
 * Compute the probability that a relation is the top rel
 * 
 * @author welty
 *
 */
public class RelationTopProbability implements RelationMeasure {
	@Override
	public Double call(Integer relIndex, Map<String,Instance> sentSumVectors) {
		double count = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIndex).equals(JavaMlUtils.max(sentSumEntry.getValue()))) count++;
		}
		return count / (new Double(sentSumVectors.size()));
	}

	@Override
	public void init(Map<String, Integer> vectorIndex) { }

	@Override
	public String label() {
		return "P(R-Top)";
	}
	
}
