/**
 * 
 */
package edu.vu.crowds.analysis.relation.relrel_measures;

import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.relation.RelationRelationMeasure;


/**
 * Compute the causal power of R1 -> R2 
 * [P(R2 | R1) - P(R2 | ~R1)] / [1 - P(R2 | ~R1)] 
 * 
 * @author welty
 *
 */
public class RelationRelationCausalPower implements RelationRelationMeasure {
	@Override
	public Double call(Integer relIdx1, Integer relIdx2, Map<String, Instance> sentSumVectors, 
			Map<String,Instance> relationScores) {
		if (relIdx1 == relIdx2) return 0.0;

		double countRel1 = 0;
		double countNotRel1 = 0;
		double countPosRelPair = 0;
		double countNegRelPair = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIdx1).intValue() > 0) {
				countRel1++;
				if (sentSumEntry.getValue().get(relIdx2).intValue() > 0) 
					countPosRelPair++;
			} else {
				countNotRel1++;
				if (sentSumEntry.getValue().get(relIdx2).intValue() > 0)
					countNegRelPair++;				
			}
		}
		double probPos = countPosRelPair / countRel1;
		double probNeg = countNegRelPair / countNotRel1;
		return (probPos - probNeg) / (1.0 - probNeg);
	}

	@Override
	public String label() {
		return "Rr->Rc";
	}

	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<String, Instance> sentSumVectors) {	}
	
}
