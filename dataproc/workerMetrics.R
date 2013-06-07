#!/usr/bin/Rscript
## Read file 90-sents-all-batches-GS-sentsv3.csv and applies the filters. 
## The filter output is the same as 90-sents-all-batches-CS-sentsv3.csv (Dropbox/data/CF-Results-processed/)

source('envars.R')
library(XLConnect)

source(paste(libpath,'/db.R',sep=''),chdir=TRUE)

source(paste(libpath,'/measures.R',sep=''),chdir=TRUE)
source(paste(libpath,'/filters.R',sep=''),chdir=TRUE)
source(paste(libpath,'/simplify.R',sep=''),chdir=TRUE)
source(paste(libpath,'/fileStorage.R',sep=''),chdir=TRUE)

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

#FIXME: this be obtained when storing the file on the file storage. 
file_id <- -1

raw_data <- getJob(job_id)
if("" %in% unique(raw_data$relation)){
  raw_data <- raw_data[raw_data$relation!="",]
}

if(dim(raw_data)[1] == 0){
  cat('JOB_NOT_FOUND')
} else {
  
  sentenceTable <- pivot(raw_data,'unit_id','relation')

  sentenceDf <- getDf(sentenceTable)
  
  #Calculate the measures to apply the filters.
  filters <- list('SQRT','NormSQRT','NormR')

  #Calculate the measures to apply the filters.filters <- list('SQRT','NormSQRT')
  mdf <- calc_measures(sentenceDf,filters)

  discarded <- list()
  filtered <- list()
  
  for (f in filters){
    #Apply the filters: each one returns the discarded rows (those below the threshold)
    discarded[[f]] <- belowDiff(mdf,f)
    #The filtered *in* 
    filtered[[f]] <- setdiff(rownames(sentenceDf),discarded[[f]])
    insertFiltSentences(job_id, file_id, f, discarded[[f]])
  }

  #After applying the filters, add the "NULL" filter.
  filters <- append('NULL', filters)
  filtered[['NULL']] <- rownames(sentenceDf)
  discarded[['NULL']] <- NULL

  worker_ids <- sort(unique(raw_data$worker_id))

  numSent <- numSentences(raw_data)

  #Singletons: workers with less than 3 judgments (not sufficient to classify them as spammers). 
  #singletons <- belowFactor(data.frame(row.names = worker_ids, numSents=numSent), 'numSents',3)
  singletons <- belowFactor(numSent,'numSent',3)

  #Remove singletons
  raw_data <- raw_data[!(raw_data$worker_id %in% singletons),]

  out <- NULL
  spamCandidates <- list()
  
  for (f in filters){
    print(paste('computing metrics for filter ',f))
  
    filt <- raw_data[raw_data$unit_id %in% filtered[[f]],]

    filtWorkers <- sort(unique(filt$worker_id))

    numSent <- numSentences(filt)
    numAnnot <- numAnnotations(filt)

    annotSentence <- numAnnot / numSent
    colnames(annotSentence) <- 'annotSentence'
    
    #sentMat <- list()

    agrValues <- agreement(filt)
    cosValues <- cosMeasure(filt)
    #sentRelScoreValues <- sentRelScoreMeasure(filt)
    
    #df <- data.frame(row.names=filtWorkers,numSents=numSent, cos=cosValues, agr=agrValues, annotSentence=(numAnnot/numSent))
    df <- cbind(numSent,cosValues, agrValues,annotSentence)

    if(f == 'NULL'){
      saveWorkerMetrics(df,job_id)

	  sentRelDf <- sentRelScoreMeasure(raw_data)
	  sClarity <- sentenceClarity(sentRelDf)
	  rClarity <- relationClarity(sentRelDf)  
  
	  workerSentCos <- workerSentenceCosTable(raw_data)
	  workerSentScore <- workerSentenceScoreTable(raw_data, workerSentCos, sClarity)            
	  workerRelScore <- workerRelationScore(raw_data, rClarity, workerSentCos)

	  insertWorkerSentenceScore(workerSentScore, workerSentCos)
    }
    
    # Add empty values for filtered out workers
    # missingworkers <- setdiff(worker_ids,filtWorkers)
    # emptyCol <-  rep(0,length(missingworkers))

    ## filtrows <- data.frame(row.names=missingworkers,numSents=emptyCol,cos=emptyCol,agr=emptyCol,annotSentence=emptyCol)
    ## df <- rbind(df, filtrows)
    ## df <- df[order(as.numeric(row.names(df))),]

  #Empty dataframe
    spamFilters <- data.frame(row.names=worker_ids,cos=rep(0,length(worker_ids)),annotSentence=rep(0,length(worker_ids)),agr=rep(0,length(worker_ids)))

    candidateRows <- overDiff(df,'cos')
    if(length(candidateRows) > 0 & dim(spamFilters[rownames(spamFilters) %in% candidateRows,])[1]>0){
      spamFilters[rownames(spamFilters) %in% candidateRows,]$cos = 1
    }
    
    candidateRows <- overDiff(df,'annotSentence')
    if(length(candidateRows) > 0 & dim(spamFilters[rownames(spamFilters) %in% candidateRows,])[1]>0){
      #if(length(candidateRows) > 0){
      spamFilters[rownames(spamFilters) %in% candidateRows,]$annotSentence = 1
    }
    
    candidateRows <- belowDiff(df,'agr')
    if(length(candidateRows) > 0 & dim(spamFilters[rownames(spamFilters) %in% candidateRows,])[1]>0){
    #if(length(candidateRows) > 0){
      spamFilters[rownames(spamFilters) %in% candidateRows,]$agr = 1
    }
    
    spamCandidates[[f]] <- spamFilters
    
    if(is.null(out)){
      out <- df
    } else {
      out <- cbind(out, df)
    }
  }

  spamFilterOutput <- data.frame(row.names=worker_ids,
                                 filter1=rowSums(spamCandidates[['NULL']]),
                                 filter2=rowSums(spamCandidates[['SQRT']]),
                                 filter3=rowSums(spamCandidates[['NormSQRT']])
                                 )

  #Combine spamFilterOutput. 
  sf <- as.data.frame(rowSums(spamFilterOutput > 0) > 1)
  colnames(sf) = 'label'
  spamLabels <- rownames(sf[sf$label==TRUE,,drop=FALSE])

  #FIXME: modify insertFiltWorkers for new table format. 
  #insertFiltWorkers(job_id,file_id, '[cos,annotSentence,agr]',spamLabels)

  numFilteredSentences <- length(unlist(discarded))
  numFilteredWorkers <- length(spamLabels)
  
  updateResults(job_id, numFilteredSentences, numFilteredWorkers)  

  fname <- getFileName(job_id,fileTypes[['workerMetrics']])
  path <- getFilePath(job_id, folderTypes[['analysisFiles']])
  
  wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)

  ## createSheet(wb.new, name = "pivot-worker")
  ## writeOutputHeaders(wb.new,"pivot-worker")

  ## writeWorksheet(wb.new,data=cbind(out,spamFilterOutput[rownames(out),],spam=sf[rownames(out),]),sheet=1,startRow=2,startCol=1,header=TRUE,rownames='Worker ID')
  
  createSheet(wb.new, name = "singleton-workers-removed")
  
  writeOutputHeaders(wb.new,"singleton-workers-removed")
  writeWorksheet(wb.new,data=out[rownames(out) %in% setdiff(rownames(out),singletons),],sheet="singleton-workers-removed",startRow=2,startCol=1,header=TRUE,rownames='Worker ID')

  ## createSheet(wb.new, name="workerRelationScore")
  
  ## wrs <- workerRelScore
  ## wrs[is.na(workerRelScore)] <- 0
  ## wrs$worker_id <- rownames(wrs)
  
  ## writeWorksheet(wb.new,data=wrs[,all],sheet="workerRelationScore",startRow=1,startCol=1,header=TRUE,rownames='Worker ID')    

  createSheet(wb.new, name = "filtered-out-sentences")
  writeFilteredOutHeaders(wb.new,"filtered-out-sentences")

  currentCol <- 1
  for (f in filters){
    if(f != 'NULL'){
      writeWorksheet(wb.new,data=discarded[[f]],sheet='filtered-out-sentences',startRow=2,startCol=currentCol,header=FALSE)
      currentCol <- currentCol + 2
      #write.csv(discarded[[f]], paste(outputdirectory,paste(job_id,'filtered-out-sentences',f,'.csv',sep="_"),sep=""),row.names=FALSE)           
    }
  }

  createSheet(wb.new, name = "spammer-labels")
  writeWorksheet(wb.new,data=spamLabels,sheet='spammer-labels',startRow=1,startCol=1,header=FALSE)
  
  saveWorkbook(wb.new)

  #FIXME: get the adecuate value for the creator
  creator = 'script'
  saveFileMetadata(fname,path,mimeTypes[['excel']],-1,creator)
  
  dbDisconnect(con)
  cat('OK')	
}




