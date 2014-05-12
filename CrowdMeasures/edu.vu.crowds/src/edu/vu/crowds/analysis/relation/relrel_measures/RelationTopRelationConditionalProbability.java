/**
 * 
 */
package edu.vu.crowds.analysis.relation.relrel_measures;

import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.JavaMlUtils;
import edu.vu.crowds.analysis.relation.RelationRelationMeasure;


/**
 * Compute the probability of P(R2|R1 is the top)
 * 
 * @author welty
 *
 */
public class RelationTopRelationConditionalProbability implements RelationRelationMeasure {
	@Override
	public Double call(Integer relIdx1, Integer relIdx2, Map<String, Instance> sentSumVectors, 
			Map<String,Instance> relationScores) {
		double countRel1Top = 0;
		double countRelPair = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIdx1).intValue() == JavaMlUtils.max(sentSumEntry.getValue())) {
				countRel1Top++;
				if (sentSumEntry.getValue().get(relIdx2).intValue() > 0) {
					countRelPair++;
				}
			}			
		}
		return countRelPair / countRel1Top;
	}

	@Override
	public String label() {
		return "P(Rc|Rr-Top)";
	}

	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<String, Instance> sentSumVectors) {	}
	
}
