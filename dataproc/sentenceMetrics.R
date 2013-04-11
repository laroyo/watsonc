#!/usr/bin/Rscript

#Parse a results file from Crowdflower, and generates contingency tables for relations/workers and sentences as an output. 
library(XLConnect)
library(RMySQL)

con <- dbConnect(MySQL(), user="watsoncs",password="Tre2akEf",dbname='watsoncs',host='localhost')

source('lib/simplify.R')
source('lib/measures.R')
source('lib/output.R')

args <- commandArgs(trailingOnly = TRUE)

#The script accepts parameters. If none passed, the following will be used as an examaple. 
if(length(args) == 0){
#FIXME: a job_id should always be passed. 
} else {
  job_id <- args[1]
}


query <- sprintf('select unit_id,worker_id,worker_trust,external_type,relation,explanation,selected_words,started_at,created_at,term1,term2,sentence from cflower_results where job_id = %s', job_id)
raw_data <- dbGetQuery(con, query)
#Shorten the names of some fields. 
names(raw_data)[names(raw_data)=="step_1_select_the_valid_relations"] <- "relation"
names(raw_data)[names(raw_data)=="step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1"] <- "selected_words"
names(raw_data)[names(raw_data)=="step_2b_if_you_selected_none_in_step_1_explain_why"] <- "explanation"

sentenceTable <- pivot(raw_data,'unit_id','relation')
sentenceDf <- getDf(sentenceTable)

measuresDf <- calc_measures(sentenceDf,list('SumSQ','SQRT','Difference','NormSQRT','SumSQRel','SQRTRel','DiffRel'))

#Merge the data and the measures data frames into a single df, to export it. 
combinedDf <- merge(sentenceDf,measuresDf,by=0)

wb.new <- loadWorkbook(outputfile, create = TRUE)

createSheet(wb.new, name = "pivot-sentence")
writeWorksheet(wb.new,data=combinedDf,sheet=1,startRow=1,startCol=1,rownames=NULL)


workerTable <- pivot(raw_data,'worker_id','relation')
workerDf <- as.data.frame(rbind(sentenceTable))
workerDf <- labeldf(workerDf)

wmeasuresDf <- calc_measures(workerDf,list('RelCount'))
combinedWorkersDf <- merge(workerDf,wmeasuresDf,by=0)

## createSheet(wb.new, name = "pivot-worker")
## writeWorksheet(wb.new,data=combinedWorkersDf,sheet=2,startRow=1,startCol=1,rownames=NULL)

saveWorkbook(wb.new)
genHeatMap(sentenceDf,job_id)


