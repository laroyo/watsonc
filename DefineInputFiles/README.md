Files Description


The file CreateCSVFile.java takes as argument the name of a folder. The folder should contain the text files that need 
to be processed and transformed into .csv files. The resulting .csv files can be used as input for the sentences 
filtering workflow.
- folder example: .../Dropbox/Watson-Crowdsourcing/data/CF-data-input/Dec 2012 sentences
- the resulting .csv file has the following header: index | relation-type | term1 | b1 | e1 | term2 | b2 | e2 | sentence 


The file Main.java takes as argument a .csv file. The sentence filtering process follows the following steps:

1.FormatInputFile.java creates the sentences:

  - search in each sentence for the occurrences of the two key words (term1 and term2) 
  - for each pair of different occurrences ((term1, (b1, e1)), (term2, (b2, e2))), add another entry in the file  
  - if the two terms are identical, the entries are not duplicated
  - resulting file: input_file_name + -all.csv


2.ExtractGoodSentences.java applies filters for ; (semicolon) between the two terms and then searches for sentences 
where one term is between brackets. The filtering for semicolon has two output files:

  - input_file_name + -noSemicolon.csv (all the sentences where the two key terms are not separated by a semicolon)
  - input_file_name + -withSemicolon.csv (all the senteces where the two terms are separated by a semicolon)

For identifying the senteces where one term is inside a brackets pair, we search for the following pattern: 
.. ( .. Term1 | Term2 .. ) .. . The output files are the following:
  - input_file_name + -withKeyWordBetweenBrackets.csv (all the sentences which don't contain a key term inside brackets)
  - input_file_name + -noKeyWordBetweenBrackets.csv (all the sentences which contain a key term inside brackets)


3.ClusterOnRelations.java searches for the 12 relations in each sentence (word searching). There are three output files 
generated:

- input_file_name + -noRelations.csv (there is no word that defines a relation in the sentence)
- input_file_name + -withRelationsOutside.csv (there is a word that defines a relation in the sentence, but it is not 
located between the key terms)
- input_file_name + -withRelationsBetween (there is a word that defines a relation in the sentence, right between the 
two terms) 
 
4.SentencesLengthSelection.java computes the average length of the sentences. The senteces are clustered in two output 
files. The sentences whose length is above the threshold are considered long sentences, while the sentences whose length 
is below the threshold are considered short or average. For computing the threshold, the following formula was used:

threshold = averageLength + (int)(maxLength - averageLength) / 2;

- input_file_name + -long.csv (the sentences with length above the threshold)
- input_file_name + -shortAndAverage.csv (the sentences with length below the threshold)


For creating the input file for a CrowdFlower job, the file JobFile.java should be run. The first argument for running 
it is an integer, which represents the number of senteces that will be randomly chosen from each file. The next 
arguments should be a list of files from which you want to create the job input. The output file is called job-sentences.csv.
