#!/usr/bin/Rscript
#source('/var/www/html/wcs/dataproc/envars.R')
source('/home/gsc/watson/dataproc/envars.R')

#Parse a results file from Crowdflower, and generates contingency tables for relations/workers and sentences as an output. 
library(XLConnect)


source(paste(libpath,'/','simplify.R',sep=''))
source(paste(libpath,'/','measures.R',sep=''))
source(paste(libpath,'/','db.R',sep=''))
source(paste(libpath,'/','fileStorage.R',sep=''))

#source(paste(libpath,'/','output.R',sep=''))
#To calculate the permutations for the relation similarity. 
library(gtools)
#For calculating the cosine. 
library(lsa)

args <- commandArgs(trailingOnly = TRUE)

if(length(args) > 0){
  job_id <- args[1]
} else {
  if(!exists('job_id')){
    stop('Error: you should provide a Job id (parameter)')
  }
}

outputfile <- paste(filespath,'/','AnalysisFiles','/',job_id,'_sentenceMetrics.xlsx',sep='')

query <- sprintf('select unit_id,worker_id,worker_trust,external_type,relation,explanation,selected_words,started_at,created_at,term1,term2,sentence from cflower_results where job_id = %s', job_id)
raw_data <- dbGetQuery(con, query)

if(dim(raw_data)[1] == 0){
  cat('JOB_NOT_FOUND')
} else {

  #Shorten the names of some fields. 
  names(raw_data)[names(raw_data)=="step_1_select_the_valid_relations"] <- "relation"
  names(raw_data)[names(raw_data)=="step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1"] <- "selected_words"
  names(raw_data)[names(raw_data)=="step_2b_if_you_selected_none_in_step_1_explain_why"] <- "explanation"

  sentenceTable <- pivot(raw_data,'unit_id','relation')
  sentenceDf <- getDf(sentenceTable)

  measuresDf <- calc_measures(sentenceDf,list('SumSQ','SQRT','Difference','NormSQRT','SumSQRel','SQRTRel','DiffRel'))
  
  #Merge the data and the measures data frames into a single df, to export it. 
  combinedDf <- merge(sentenceDf,measuresDf,by=0)

  fname <- getFileName(job_id,fileTypes[['sentenceMetrics']])
  path <- getFilePath(job_id, folderTypes[['analysisFiles']])
  
  wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)
  print('full path')
  print(paste(path,fname,sep='/'))
  
  createSheet(wb.new, name = "pivot-sentence")
  writeWorksheet(wb.new,data=format(combinedDf,digits=2),sheet=1,startRow=1,startCol=1,rownames=NULL)
   
  workerTable <- pivot(raw_data,'worker_id','relation')
  workerDf <- as.data.frame(rbind(sentenceTable))
  workerDf <- labeldf(workerDf)
  
  wmeasuresDf <- calc_measures(workerDf,list('RelCount'))
  combinedWorkersDf <- merge(workerDf,wmeasuresDf,by=0)
  
  ## createSheet(wb.new, name = "pivot-worker")
  ## writeWorksheet(wb.new,data=combinedWorkersDf,sheet=2,startRow=1,startCol=1,rownames=NULL)

  sentRelDf <- sentRelScoreMeasure(raw_data)
  sentClarity <- sentenceClarity(sentRelDf)

  createSheet(wb.new, name = "sentence-metrics")
  saveSentenceMetrics(wb.new,"sentence-metrics",sentRelDf,sentClarity)
  
  relClarity <- relationClarity(sentRelDf)

  createSheet(wb.new, name = "relation-metrics")
  relSimilarity <- relationSimilarity(raw_data)
  relAmbiguity <- relationAmbiguity(relSimilarity)

  saveRelationSimilarity(wb.new,"relation-metrics",relSimilarity,relAmbiguity,relClarity)

  saveWorkbook(wb.new)
  genHeatMap(sentenceDf,job_id,dir=path)

  #FIXME: get the adecuate value for the creator
  creator = 'script'
  saveFileMetadata(fname,path,mimeTypes[['excel']],-1,creator)
  dbDisconnect(con)
  cat('OK')

}


