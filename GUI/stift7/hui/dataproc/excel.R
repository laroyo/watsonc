#!/usr/bin/Rscript

#Parse a results Excel file (raw data), and generates contingency tables for relations/workers and sentences as an output.

library(XLConnect)
source('simplify.R')
source('measures.R')

args <- commandArgs(trailingOnly = TRUE)

if(length(args) == 0){
  ## Default options
  inputfile <- "90-sents-WebSci13-all-batches-with-manual-annotations.xlsx"
  outputfile  <- "output-excel.xlsx"
} else {
  inputfile <- args[1]
  outputfile  <- args[2]
}

raw_data <- readWorksheet(loadWorkbook(inputfile),sheet=1)

#For excel spreadsheets, discard empty columns
nullCols <- colSums(is.na(raw_data))
raw_data = raw_data[,nullCols!=dim(raw_data)[1]]

names(raw_data)[names(raw_data)=="STEP1..Chosen.relation"] <- "relation"
names(raw_data)[names(raw_data)=="STEP2A..Words.from.the.sentence"] <- "selected_words"
names(raw_data)[names(raw_data)=="STEP2B..Explain.why"] <- "explanation"

sentenceTable <- pivot(raw_data,'UID','relation')
#Transform the contingency table into a data frame. 
sentenceDf <- getDf(sentenceTable)

measuresDf <- calc_measures(sentenceDf,list('SumSQ','SQRT','Difference','NormSQRT','SumSQRel','SQRTRel','DiffRel'))


#Merge the data and the measures data frames into a single df, to export it. 
combinedDf <- merge(sentenceDf,measuresDf,by=0)

wb.new <- loadWorkbook(outputfile, create = TRUE)
createSheet(wb.new, name = "pivot-sentence")
writeWorksheet(wb.new,data=combinedDf,sheet=1,startRow=1,startCol=1,rownames=NULL)


workerTable <- pivot(raw_data,'WID','relation')
workerDf <- getDf(workerTable)

wmeasuresDf <- calc_measures(workerDf,list('RelCount'))
combinedWorkersDf <- merge(workerDf,wmeasuresDf,by=0)

createSheet(wb.new, name = "pivot-worker")
writeWorksheet(wb.new,data=combinedWorkersDf,sheet=2,startRow=1,startCol=1,rownames=NULL)
saveWorkbook(wb.new)





