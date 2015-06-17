/**
 * 
 */
package edu.vu.crowds.analysis.relation.relrel_measures;

import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.analysis.relation.RelationRelationMeasure;


/**
 * Compute P(R2 | R1) - P(R2)
 * zero out r2==r1
 * @author welty
 *
 */
public class RelRelCondProbMinusRelProb implements RelationRelationMeasure {
	@Override
	public Double call(Integer relIdx1, Integer relIdx2, Map<String, Instance> sentSumVectors, 
			Map<String,Instance> relationScores) {
		if (relIdx1 == relIdx2) return 0.0;
		
		double count = 0;
		for (Entry<String, Instance> sentSumEntry : sentSumVectors.entrySet()) {
			if (sentSumEntry.getValue().get(relIdx2).intValue() > 0) count++;
		}
		double relProb =  count / (new Double(sentSumVectors.size()));

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
		return (countRelPair / countRel1) - relProb;
	}

	@Override
	public String label() {
		return "P(Rc|Rr)-P(Rc)";
	}

	@Override
	public void init(Map<String, Integer> vectorIndex,
			Map<String, Instance> sentSumVectors) {	}
	
}
