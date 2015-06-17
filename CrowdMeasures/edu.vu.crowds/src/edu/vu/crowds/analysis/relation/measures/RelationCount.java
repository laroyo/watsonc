/**
 * 
 */
package edu.vu.crowds.analysis.relation.measures;

import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.relation.RelationMeasure;


/**
 * Count the appearances of Rel in all sents
 * 
 * @author welty
 *
 */
public class RelationCount implements RelationMeasure {
	@Override
	public Double call(Integer relIndex, Map<String,Instance> sentSumVectors) {
		Integer count = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIndex).intValue() > 0) count++;
		}
		return new Double(count);
	}

	@Override
	public void init(Map<String, Integer> vectorIndex) { }

	@Override
	public String label() {
		return "|S:R|";
	}
	
}
