import java.io.FileNotFoundException;
import java.io.FileReader;

public class Main {

//	files/w21-contra-0.1.txt.filterbykb.sample.csv files/w21-cause-0.1.txt.filterbykb.sample.csv files/w21-symptom-0.1.txt.filterbykb.sample.csv files/w21-cause-0.1.txt.filterbykb.sample.csv files/w21-prevent-0.1.txt.filterbykb.sample.csv files/w21-location-0.1.txt.filterbykb.sample.csv files/w21-diagnose-0.1.txt.filterbykb.sample.csv 
	public static void main(String[] args) {
		FormatInputFile inputFile = new FormatInputFile();
		ExtractGoodSentences goodBadSent= new ExtractGoodSentences();
		ClusterOnRelations relations = new ClusterOnRelations();
		SentencesLengthSelection length = new SentencesLengthSelection();
		FileReader fileReader = null;
		String fileName = args[0];
		
		try {
			fileReader = new FileReader(fileName);
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		}
		inputFile.readRowFile(fileReader, fileName);
	
		try {
			fileReader = new FileReader(inputFile.outputFile);
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		}
		goodBadSent.readRowFile(fileReader, fileName);
					
		try {
			fileReader = new FileReader(inputFile.outputFile);
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		}
		relations.readRowFile(fileReader, fileName);
		
		try {
			fileReader = new FileReader(inputFile.outputFile);
		} catch (FileNotFoundException e) {
			e.printStackTrace();
		}
		length.readRowFile(fileReader, fileName);

	}
}
