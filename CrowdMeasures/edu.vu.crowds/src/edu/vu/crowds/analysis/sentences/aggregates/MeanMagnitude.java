/**
 * 
 */
package edu.vu.crowds.analysis.sentences.aggregates;

import java.util.Map;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;


import edu.vu.crowds.Measure;
import edu.vu.crowds.analysis.aggregates.MeanMeasure;
import edu.vu.crowds.analysis.sentences.AggregateSentenceMeasure;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;
import edu.vu.crowds.analysis.sentences.measures.Magnitude;

/**
 * @author welty
 *
 */
public class MeanMagnitude extends MeanMeasure implements AggregateSentenceMeasure {

	/**
	 * 
	 */
	public MeanMagnitude() { super(); }
	/* (non-Javadoc)
	 * @see com.ibm.racr.crowd.analysis.AggregateMeasure#init()
	 */
	@Override
	public void init(Map<? extends Measure, Integer> measureIndex) {
	}
	public void init(Map<String,Integer> vectorIndex, Map<SentenceMeasure,Integer> measureIndex) {
		super.init(measureIndex);
		for (Measure m : measureIndex.keySet()) {
			if (m instanceof Magnitude) index = measureIndex.get(m);
		}
	}
	@Override
	public String label() {
		return "|V| Mean";
	}
	@Override
	public void next(Dataset sentCluster, Instance sentSum,	Instance sentMeasures) {
		super.next(sentMeasures);		
	}
}
