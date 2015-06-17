package edu.vu.crowds;

import java.util.List;
import java.util.Map;



public class AnnFile {
	List<String> header;
	Map<String,List<String>> relMap;
	
	public AnnFile(List<String> h, Map<String,List<String>> rm) {
		header = h;
		relMap = rm;
	}
	
	Map<String,List<String>> getRelMap() {
		return relMap;
	}
	
	List<String> getHeader() {
		return header;
	}
}
