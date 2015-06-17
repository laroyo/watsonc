/**
 * 
 */
package edu.vu.crowds.analysis.sentences.measures;

import java.util.Map;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.DefaultDataset;
import net.sf.javaml.core.DenseInstance;
import net.sf.javaml.core.Instance;
import net.sf.javaml.distance.CosineDistance;
import edu.vu.crowds.analysis.sentences.SentenceMeasure;


/**
 * Compute the magnitude of the sum vector
 * 
 * @author welty
 *
 */
public class MaxRelationCosine implements SentenceMeasure {
	Dataset relationUnitVectors = new DefaultDataset();
	Map<String,Integer> vectorIndex;
	private int size;
	private DenseInstance zeroVec;
	private CosineDistance cosineMeasure;
	
	@Override
	public void init(Map<String, Integer> vectorIndex) { 
		this.vectorIndex = vectorIndex;
		size = vectorIndex.keySet().size();
		zeroVec = new DenseInstance(size);
		for (int i=0; i<size; i++) zeroVec.put(i, 0.0);
		cosineMeasure = new CosineDistance();
	}

	@Override
	public Double call(Dataset sentCluster, Instance sumVector) {
		Double maxCos = 0d;
		for (int i=0; i<sumVector.keySet().size(); i++) {
			Double cos = relationCosine(sumVector, i);
			if (cos > maxCos) maxCos = cos;
		}
		return maxCos;
	}
	
	@Override
	public String label() {
		return "Max Rel Cos";
	}
	
	public Double relationCosine(Instance sumVector,int rel) {
		Instance relUnitVec = zeroVec.copy();
		relUnitVec.put(rel,1.0);
		return 1-cosineMeasure.measure(sumVector, relUnitVec);		
	}
	
	public Double factorCosine(Instance sumVector, String fact) {
		Instance factVec = zeroVec.copy();
		for (int i = 0; i < fact.length(); i++)
			factVec.put(Character.getNumericValue(fact.charAt(i)), 1.0);
		return 1-cosineMeasure.measure(sumVector, factVec);		
	}
}
