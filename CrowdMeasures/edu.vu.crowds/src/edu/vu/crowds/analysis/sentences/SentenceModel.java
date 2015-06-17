package edu.vu.crowds.analysis.sentences;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class SentenceModel {
	String sentence;
	TermModel t1;
	TermModel t2;
	String senID;
	String rel;
	
	public String getSentence() {
		return sentence;
	}
	
	public TermModel getTerm(int tid) {
		if (tid == 1)
			return t1;
		else
			return t2;
	}
	
	public String getRel() {
		return rel;
	}
	
	public String getSenID() {
		return senID;
	}
	
	public SentenceModel(String senID, String sentence, String rel) {
		this.senID = senID;
		
		if (sentence.startsWith("\"")){
			sentence = sentence.substring(1);
		}
		if (sentence.endsWith("\"")) {
			sentence = sentence.substring(0, sentence.length() - 1);
		}
		this.sentence = sentence.replace('-', ' ');
		
		this.rel = rel;
		
		t1 = null;
		t2 = null;
	}
	
	public void setTerm(int b, int e, String term, int tid) {
		if (tid == 1 && t1 == null) {
			t1 = new TermModel(b, e, term);
			t1.setArgCompVecWithPunct(getArgCompVec(t1, false));
			t1.setArgCompVec(getArgCompVec(t1, true));
		}
		else if (t2 == null) {
			t2 = new TermModel(b, e, term);
			t2.setArgCompVecWithPunct(getArgCompVec(t2, false));
			t2.setArgCompVec(getArgCompVec(t2, true));
		}
	}
	
	String[] getArgCompVec(TermModel t, boolean noPunct) {
		String[] argCompVec = new String[7];
		for (int i = 0; i < 7; i++) {
			argCompVec[i] = "";
		}
		
		System.out.println(senID + ": " + t.getB() + ", " + t.getE() + " " + t.getTerm() + " -> " + sentence);
		argCompVec[3] = sentence.substring(t.getB(), t.getE() + 1).toLowerCase().trim();
		
		if (noPunct == true)
			argCompVec[3] = argCompVec[3].replaceAll("[^a-zA-Z0-9 ]", "").trim();
		
		/*if (t.getTerm().compareTo(argCompVec[3]) != 0) {
			System.out.println(senID + ": " + t.getB() + ", " + t.getE() + " " + t.getTerm() + " -> " + argCompVec[3]);
		}*/
		
		//sentence = sentence.replaceAll("[\\[\\]]", "");
		String argLeft = sentence.substring(0, t.getB()).toLowerCase().trim();
		if (noPunct == true) {
			argLeft = argLeft.replaceAll("[^a-zA-Z0-9 ]", "").trim();
		}
		String[] argLeftWords = argLeft.split(" ");
		
		List<String> list = new ArrayList<String>(Arrays.asList(argLeftWords));
		list.removeAll(Arrays.asList(""));
		argLeftWords = list.toArray(new String[0]);
		
		for (int i = 0; i < 3 && argLeftWords.length - i - 1 >= 0; i++) {
			argCompVec[2 - i] = argLeftWords[argLeftWords.length - i - 1];
		}
		
		if (t.getE()+1 < sentence.length() - 1) {

			String argRight = sentence.substring(t.getE()+1, sentence.length() - 1).toLowerCase();
			if (noPunct == true) {
				argRight = argRight.replaceAll("[^a-zA-Z0-9 ]", "").trim();
			}
			String[] argRightWords = argRight.split(" ");
			
			// change term to plural form for comparison purposes
			if (argRightWords[0].compareTo("s") == 0) {
				argCompVec[3] += "s";
			}
			if (argRightWords[0].compareTo("es") == 0) {
				argCompVec[3] += "es";
			}
			if (argRightWords[0].compareTo(",") == 0) {
				argCompVec[3] += ",";
			}
			
			list = new ArrayList<String>(Arrays.asList(argRightWords));
			list.removeAll(Arrays.asList(""));
			// remove leftover plural forms
			list.removeAll(Arrays.asList("s"));
			list.removeAll(Arrays.asList("es"));
			list.removeAll(Arrays.asList(","));
			argRightWords = list.toArray(new String[0]);
			
			for (int i = 0; i < 3 && i < argRightWords.length; i++) {
				argCompVec[i + 4] = argRightWords[i];
			}
		}
		
		/* if (noPunct == true) {
		 System.err.println(an + ": " + argCompVec[0] + " + " + argCompVec[1] + " + " + argCompVec[2] + " + " 
		 + argCompVec[3] + " + " + argCompVec[4] + " + " + argCompVec[5] + " + " + argCompVec[6]);
		}*/
		
		argCompVec[3] = argCompVec[3].toLowerCase().trim();
		if (noPunct == true)
			argCompVec[3] = argCompVec[3].replaceAll("[^a-zA-Z0-9 ]", "").trim();
		
		return argCompVec;
	}
	
	public String[] getArgCompVec(int tid) {
		if (tid == 1)
			return t1.getArgCompVec();
		else
			return t2.getArgCompVec();
	}
	
	public String[] getArgCompVecWithPunct(int tid) {
		if (tid == 1)
			return t1.getArgCompVecWithPunct();
		else
			return t2.getArgCompVecWithPunct();
	}
}
