/**
 * 
 */
package edu.vu.crowds.analysis.sentences;

import java.util.Map;

import edu.vu.crowds.Measure;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;

/**
 * @author welty
 *
 */
public interface SentenceMeasure extends Measure {
	public void init(Map<String,Integer> vectorIndex);
	public Double call(Dataset sentCluster, Instance sumVector);
}
