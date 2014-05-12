/**
 * 
 */
package edu.vu.crowds.analysis.relation;

import java.util.Map;

import net.sf.javaml.core.Instance;
import edu.vu.crowds.Measure;

/**
 * @author welty
 *
 */
public interface RelationRelationMeasure extends Measure {
	public void init(Map<String,Integer> vectorIndex, Map<String,Instance> sentSumVectors);
	public Double call(Integer relIdx1, Integer relIdx2, Map<String, Instance> sentSumVectors,
			Map<String,Instance> relationScores);
}
