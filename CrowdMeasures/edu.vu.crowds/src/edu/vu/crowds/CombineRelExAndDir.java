/**
 * 
 */
package edu.vu.crowds;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintStream;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * @author welty
 *
 */
public class CombineRelExAndDir {

	/**
	 * 
	 */
	public CombineRelExAndDir() {
		// TODO Auto-generated constructor stub
	}

	/**
	 * Creates a new RelEx file. Zeros out all sentence relation scores unless there has been a 
	 * RelDir task for that relation on that sentence. All SR scores are weighted by their 
	 * RelDir score.  A "no relation" from reldir will also reduce the score of the normal
	 * direction (the normal direction is ARG2 rel ARG1).  This means that a relation with 
	 * some votes for the normal direction (which is labelled ARG2 in RelDir) *and* some votes
	 * for no_relation will get reduced twice.
	 * 
	 * @param args RelDir.csv RelEx.csv NewRelEx.csv
	 */
	public static void main(String[] args) {
		File relDirFile = new File(args[0]);
		File relExFile = new File(args[1]);
		int[] relExRelFields = {1,2,3,4,5,6,7,8,9,10,11,12,13,14};
		List<String> relDirLabels = new ArrayList<String>();
		List<String> relExLabels = new ArrayList<String>();
		Map<String,List<String>> relDirMap = new HashMap<String,List<String>>();
		Map<String,List<String>> relExMap = new HashMap<String,List<String>>();
		Map<String,List<String>> relExInvMap = new HashMap<String,List<String>>();
		Map<String,List<String>> relExNewMap = new HashMap<String,List<String>>();
		buildMapFromFile(relDirMap,relDirFile,0,relDirLabels);
		buildMapFromFile(relExMap,relExFile,0,relExLabels);
		for (String sentid : relDirMap.keySet()) {
			List<String> relDirList = relDirMap.get(sentid);
			List<String> relExList = relExMap.get(sentid);
			if (relExList == null) System.err.println("Unable to find matching relex sent: " + sentid);
			else {
				String rel = relDirList.get(15);
				Integer relIdx = relExLabels.indexOf(rel);
				Float relScore = Float.parseFloat(relExList.get(relIdx));
				if (!"0".equals(relDirList.get(2))) { // ARG1 is support for the inverted order
					List<String> relExInvList = relExInvMap.get(sentid);
					if (relExInvList == null) {
						relExInvList = copyRow(relExList,relExRelFields);
						relExInvMap.put(sentid, relExInvList);
						Integer b1Idx = relExLabels.indexOf("b1");
						Integer b2Idx = relExLabels.indexOf("b2");
						Integer e1Idx = relExLabels.indexOf("e1");
						Integer e2Idx = relExLabels.indexOf("e2");
						relExInvList.add(b1Idx,relExList.get(b2Idx));
						relExInvList.add(b2Idx,relExList.get(b1Idx));
						relExInvList.add(e1Idx,relExList.get(e2Idx));
						relExInvList.add(e2Idx,relExList.get(e1Idx));
						relExInvList.add(0,relExList.get(0)+"-inv");
					}
					float argScore = Float.parseFloat(relDirList.get(2));
					relExInvList.add(relIdx,(new Float(argScore*relScore)).toString());
				}
				boolean relScoreChanged = false;
				if (!"0".equals(relDirList.get(4))) { // ARG2 is support for the orig. order
					float argScore = Float.parseFloat(relDirList.get(4));
					relScoreChanged = true;
					relScore = argScore*relScore;
				}
				if (!"0".equals(relDirList.get(6))) { // noRel is support for no relation
					float argScore = Float.parseFloat(relDirList.get(6));
					relScoreChanged = true;
					relScore = argScore*relScore;
					// TODO maybe give the lost amount to NONE & OTHER?
				}
				if (relScoreChanged) {
					List<String> relExNewList = relExNewMap.get(sentid);
					if (relExNewList == null) {
						relExNewList = copyRow(relExList,relExRelFields);
						relExNewMap.put(sentid, relExNewList);
					}
					relExNewList.add(relIdx,relScore.toString());
				}
			}
		}
		
		try {
			char splitChar = CrowdTruth.getSplitCharFromFilename(args[2], ',');
			PrintStream o = new PrintStream(args[2]);
			writeMapToFile(o, (Map<String,List<String>>)Collections.singletonMap("head", relExLabels),splitChar);
			writeMapToFile(o,relExNewMap,splitChar);
			writeMapToFile(o, relExInvMap, splitChar);
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	private static void writeMapToFile(PrintStream o, Map<String, List<String>> map, char splitChar) {
		for (List<String> row : map.values()) {
			for (int i=0;i<row.size()-1;i++) o.print(row.get(i)+splitChar);
			o.println(row.get(row.size()-1));
		}
	}

	private static ArrayList<String> copyRow(List<String> relExList,int[] fields) {
		//TODO keep none & other???
		ArrayList<String> newList = new ArrayList<String>(relExList);
		for (int i=0;i<fields.length;i++) 
			newList.add(fields[i],"0");
		return newList;
	}

	private static void buildMapFromFile(Map<String, List<String>> relDirMap,
			File f, int keyidx, List<String> header) {
		char splitChar = CrowdTruth.getSplitCharFromFilename(f.getName(), ',');

		try {
			BufferedReader r = new BufferedReader(new FileReader(f));
			header = CrowdTruth.parseCsvLine(r, r.readLine(), splitChar);
			for (String l = r.readLine(); l != null; l=r.readLine()) {
				ArrayList<String> lineArray = CrowdTruth.parseCsvLine(r,l,splitChar);
				String sentId = lineArray.get(keyidx);
				relDirMap.put(sentId, lineArray);
			}
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
}
