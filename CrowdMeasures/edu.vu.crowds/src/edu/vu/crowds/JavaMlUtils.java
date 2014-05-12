package edu.vu.crowds;

import java.util.Iterator;

import net.sf.javaml.core.Dataset;
import net.sf.javaml.core.Instance;
import net.sf.javaml.core.SparseInstance;

public class JavaMlUtils {
	
	public static String instanceString(Instance instance) {
		String rtn = "";
		int i=0;
		while (instance.containsKey(i)) {
			rtn += instance.get(i) + ",";
			i++;
		}
		return rtn.substring(0,rtn.length()-1);
	}

	public static Instance sumVector(Dataset dataset, int size) {
		Instance sum = new SparseInstance(size);
		for (Instance i : dataset) sum = sum.add(i);
		return sum;
	}

	public static Double componentSum(Instance inst) {
		Double sum = 0.0;
		for (Iterator<Double> i = inst.iterator(); i.hasNext();) {
			sum += i.next();
		}
		return sum;
	}
	
	public static Double max(Instance inst) {
		Double max = 0.0;
		for (Iterator<Double> i = inst.iterator(); i.hasNext();) {
			Double val = i.next();
			if (val > max) max = val;
		}
		return max;
	}
}
