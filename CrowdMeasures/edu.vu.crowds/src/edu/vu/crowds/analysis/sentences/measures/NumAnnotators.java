/**
 * 
 */
package edu.vu.crowds.analysis.sentences.measures;

import java.util.Map;

import edu.vu.crowds.analysis.sentences.SentenceMeasure;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


/**
 * Return the number of annotators for this sentence
 * 
 * @author welty
 *
 */
public class NumAnnotators implements SentenceMeasure {
	@Override
	public Double call(Dataset sentCluster, Instance sumVector) {
		return new Double(sentCluster.size());
	}

	@Override
	public void init(Map<String, Integer> vectorIndex) { }

	@Override
	public String label() {
		return "|# annots|";
	}
	
}
