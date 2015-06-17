package edu.vu.crowds;

import java.util.ArrayList;
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

	public static ArrayList<Integer> longestSubstr(String[] factSpan, String[] userSpan, 
			int factSpanStart, int userSpanStart,
			ArrayList<Integer> pos) {
		ArrayList<Integer> bestList = new ArrayList<Integer>(pos);
		//System.out.println(factSpanStart + ", " + userSpanStart + " - " + pos.toString());
		for (int i = factSpanStart; i < factSpan.length; i++) {
			for (int j = userSpanStart; j < userSpan.length; j++) {
				if (factSpan[i].compareTo(userSpan[j]) == 0) {
					pos.add(i);
					ArrayList<Integer> newPos = longestSubstr(factSpan, userSpan, i + 1, j + 1, pos);
					if (bestList.size() <= newPos.size()) {
						bestList = new ArrayList(newPos);
					}
					pos.remove(pos.size() - 1);
				}
			}
		}
		return bestList;
	}	
}
