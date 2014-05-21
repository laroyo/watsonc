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
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;

import net.sf.javaml.core.Instance;
import net.sf.javaml.core.SparseInstance;
import net.sf.javaml.distance.CosineDistance;
import net.sf.javaml.distance.CosineSimilarity;

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
		AnnFile relDir = buildMapFromFile(relDirFile,0);
		AnnFile relEx = buildMapFromFile(relExFile,0);
		

		List<String> relDirLabels = relDir.getHeader();
		Map<String,List<String>> relDirMap = relDir.getRelMap();
		Map<String,List<String>> relExMap = relEx.getRelMap();
		
		ArrayList<String> bidirRels = new ArrayList<String>();
		bidirRels.add("none");
		bidirRels.add("other");
		bidirRels.add("associated_with");

		List<String> relExLabels = relEx.getHeader();
		
		try {
			CosineSimilarity cosineMeasure = new CosineSimilarity();
			
			char sep = CrowdTruth.getSplitCharFromFilename(args[2], ',');
			PrintStream o = new PrintStream(args[2]);
			
			o.print("Sent_id" + sep);
			for (int i = 1; i < 15; i++) {
				o.print(relExLabels.get(i) + "1" + sep + relExLabels.get(i) + "2" + sep);
			}
			o.print("\n");

			for (String sentid : relExMap.keySet()) {
				
				//List<String> relDirList = relDirMap.get(sentid);
				List<String> relExList = relExMap.get(sentid);
				double[] relVals = new double[28];
				for (int i = 0; i < relVals.length; i++) relVals[i] = 0;
				
				boolean found = false;
				for (Entry<String, List<String>> e : relDirMap.entrySet()) {
					String sentidRD = e.getKey().replaceAll("\"", "");
					
					if (sentidRD.startsWith(sentid)) {
						found = true;
						List<String> relDirList = e.getValue();
						
						String rel = relDirList.get(16).replaceAll("\"", "");
						Integer relIdx = relExLabels.indexOf(rel);
						
						System.out.println(rel + " " + relIdx);
						
						Double relScoreRE = Double.parseDouble(relExList.get(relIdx));
						
						relVals[2 * relIdx - 2] += Double.parseDouble(relDirList.get(2));
						relVals[2 * relIdx - 1] += Double.parseDouble(relDirList.get(4));						
							
						/*
						Integer noRelScoreRE = 
								Integer.parseInt(relExList.get(relExLabels.indexOf("NumAnnots"))) 
								- relScoreRE;
							
							
						Integer noRelScoreRD = Integer.parseInt(relDirList.get(5));
						Integer relScoreRD = Integer.parseInt(relDirList.get(1)) + 
								Integer.parseInt (relDirList.get(3));
						
						Instance relExVec = new SparseInstance(2);
						relExVec.put(0, (double)relScoreRE);
						relExVec.put(1, (double)noRelScoreRE);
						Instance relDirVec = new SparseInstance(2);
						relDirVec.put(0, (double)relScoreRD);
						relDirVec.put(1, (double)noRelScoreRD);
						Double cosDis = cosineMeasure.measure(relExVec, relDirVec);
						
						Instance relVec = new SparseInstance(2);
						relVec.put(0, 1.0);
						
						o.println(sentidRD + sep + relScoreRE + sep + noRelScoreRE + sep +
								relScoreRD + sep + noRelScoreRD + sep + cosDis + sep +
								relExList.get(relExLabels.indexOf(rel + "_relscore")) + sep + 
								cosineMeasure.measure(relVec, relDirVec) + sep + 
								rel + sep +
								relDirList.get(relDirLabels.indexOf("term1")) + sep +
								relDirList.get(relDirLabels.indexOf("term2")) + sep +
								relDirList.get(relDirLabels.indexOf("sent")) + sep);*/
					}
				}
				
				if (found == true) {
					o.print(sentid + sep);
					for (int i = 0; i < relVals.length; i++) {
						o.print("\"" + relVals[i] + "\"" + sep);
					}
					o.print("\n");
				}
				
				else 
					System.err.println("Unable to find matching relex sent: " + sentid);
			}
			
			//writeMapToFile(o, (Map<String,List<String>>)Collections.singletonMap("head", relExLabels),splitChar);
			//writeMapToFile(o,relExNewMap,splitChar);
			//writeMapToFile(o, relExInvMap, splitChar);
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

	private static AnnFile buildMapFromFile(File f, int keyidx) {
		char splitChar = CrowdTruth.getSplitCharFromFilename(f.getName(), ',');
		
		Map<String, List<String>> relMap =  new HashMap<String,List<String>>();
		List<String> header = new ArrayList<String>();

		try {
			BufferedReader r = new BufferedReader(new FileReader(f));
			header = CrowdTruth.parseCsvLine(r, r.readLine(), splitChar);
			for (String l = r.readLine(); l != null; l=r.readLine()) {
				ArrayList<String> lineArray = CrowdTruth.parseCsvLine(r,l,splitChar);
				String sentId = lineArray.get(keyidx);
				relMap.put(sentId, lineArray);
			}
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		//System.out.println(header.toString());
		return new AnnFile(header, relMap);
	}
}
