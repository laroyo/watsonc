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
public interface RelationMeasure extends Measure {
	public void init(Map<String,Integer> vectorIndex);
//	public Double call(Dataset sentCluster, Instance sumVector);
//	Double call(Map<String, Instance> sentSumVectors);
	public Double call(Integer relId, Map<String, Instance> sentSumVectors);
}
