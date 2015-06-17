package edu.vu.crowds.analysis.sentences;

import java.util.HashMap;
import java.util.HashSet;

public class TermModel {
	int b;
	int e;
	String term;
	String[] argCompVec;
	String[] argCompVecWithPunct;
	
	HashMap<String, String> variants;
	
	TermModel(int b, int e, String term) {
		this.b = b;
		this.e = e;
		this.term = term.replace('-', ' ');
		this.variants = new HashMap<String, String>();
	}
	
	int getB() {
		return b;
	}
	
	int getE() {
		return e;
	}
	
	String getTerm() {
		return term;
	}
	
	public void setArgCompVec(String[] argCompVec) {
		this.argCompVec = argCompVec;
	}
	
	public void setArgCompVecWithPunct(String[] argCompVec) {
		this.argCompVecWithPunct = argCompVec;
	}
	
	public String[] getArgCompVec() {
		return argCompVec;
	}
	

	public String[] getArgCompVecWithPunct() {
		return argCompVecWithPunct;
	}
	
	public void addTermVariant(String wid, String v) {
		variants.put(wid, v);
	}
	
	public HashMap<String, String> getVariants() {
		return variants;
	}
}
