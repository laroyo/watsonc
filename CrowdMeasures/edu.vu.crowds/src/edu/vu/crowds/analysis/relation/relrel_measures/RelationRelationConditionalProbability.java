/**
 * 
 */
package edu.vu.crowds.analysis.relation.relrel_measures;

import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.relation.RelationRelationMeasure;


/**
 * Compute the probability of r2 given r1 
 * 
 * @author welty
 *
 */
public class RelationRelationConditionalProbability implements RelationRelationMeasure {
	@Override
	public Double call(Integer relIdx1, Integer relIdx2, Map<String, Instance> sentSumVectors, 
			Map<String,Instance> relationScores) {
		double countRel1 = 0;
		double countRelPair = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIdx1).intValue() > 0) {
				countRel1++;
				if (sentSumEntry.getValue().get(relIdx2).intValue() > 0) {
					countRelPair++;
				}
			}			
		}
		return countRelPair / countRel1;
	}

	@Override
	public String label() {
		return "P(Rc|Rr)";
	}

	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<String, Instance> sentSumVectors) {	}
	
}
