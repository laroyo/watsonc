#!/usr/bin/Rscript

source('/var/www/html/wcs/dataproc/envars.R')
	
source(paste(libpath,'/db.R',sep=''),chdir=TRUE)
source(paste(libpath,'/measures.R',sep=''),chdir=TRUE)
source(paste(libpath,'/filters.R',sep=''),chdir=TRUE)
source(paste(libpath,'/simplify.R',sep=''),chdir=TRUE)
source(paste(libpath,'/fileStorage.R',sep=''),chdir=TRUE)

library(lsa)

args <- commandArgs(trailingOnly = TRUE)
if(length(args) > 0){
  shell <- TRUE
  job_ids <- args
} else {
  if(!exists('job_ids')){
    stop('Error: invalid number of arguments')
  }
}

set_id <- createSet(job_ids)

raw_data <- getJob(job_ids)

if(dim(raw_data)[1] == 0){
  cat('JOB_NOT_FOUND')
} else {

  sentenceTable <- pivot(raw_data,'unit_id','relation')
  sentenceDf <- getDf(sentenceTable)

  genHeatMap(sentenceDf,set_id,prefix='set',dir=imagespath)
  
  #Calculate the measures to apply the filters.
  filters <- list('SQRT','NormSQRT','NormR')
  mdf <- calc_measures(sentenceDf,filters)

  discarded <- list()
  filtered <- list()
  
  for (f in filters){
    #Apply the filters: each one returns the discarded rows (those below the threshold)
    discarded[[f]] <- belowDiff(mdf,f)
    #The filtered *in* 
    filtered[[f]] <- setdiff(rownames(sentenceDf),discarded[[f]])
    file_id = -1
    insertFiltSentences(set_id, file_id, f, discarded[[f]])
  }
}

filters <- append('NULL', filters)
filtered[['NULL']] <- rownames(sentenceDf)
discarded[['NULL']] <- NULL

numSent <- numSentences(raw_data)
worker_ids <- sort(unique(raw_data$worker_id))

#Singletons: workers with less than 3 judgments (not sufficient to classify them as spammers). 
singletons <- belowFactor(data.frame(row.names = worker_ids, numSents=numSent), 'numSents',3)
raw_data <- raw_data[!(raw_data$worker_id %in% singletons),]

out <- NULL
spamCandidates <- list()

for (f in filters){
  ## if(shell)
  ##   print(paste('computing metrics for filter ',f))
  
  filt <- raw_data[raw_data$unit_id %in% filtered[[f]],]
  
  filtWorkers <- sort(unique(filt$worker_id))
  
  numSent <- numSentences(filt)
  numAnnot <- numAnnotations(filt)
  
  #sentMat <- list()
  
  agrValues <- agreement(filt)
  cosValues <- cosMeasure(filt)

  workerMetrics <- data.frame(row.names=filtWorkers,numSents=numSent, cos=cosValues, agr=agrValues, annotSentence=(numAnnot/numSent)) 
  saveWorkerMetrics(set_id, f, workerMetrics)

  spamFilters <- data.frame(row.names=worker_ids,cos=rep(0,length(worker_ids)),annotSentence=rep(0,length(worker_ids)),agr=rep(0,length(worker_ids)))
  
  candidateRows <- overDiff(workerMetrics,'cos')
  if(length(candidateRows) > 0 & dim(spamFilters[rownames(spamFilters) %in% candidateRows,])[1]>0){
    spamFilters[rownames(spamFilters) %in% candidateRows,]$cos = 1
  }
  
  candidateRows <- overDiff(workerMetrics,'annotSentence')
  if(length(candidateRows) > 0 & dim(spamFilters[rownames(spamFilters) %in% candidateRows,])[1]>0){
    #if(length(candidateRows) > 0){
    spamFilters[rownames(spamFilters) %in% candidateRows,]$annotSentence = 1
  }
    
  candidateRows <- belowDiff(workerMetrics,'agr')
  if(length(candidateRows) > 0 & dim(spamFilters[rownames(spamFilters) %in% candidateRows,])[1]>0){
    #if(length(candidateRows) > 0){
    spamFilters[rownames(spamFilters) %in% candidateRows,]$agr = 1
  }
    
  spamCandidates[[f]] <- spamFilters
  
}

tVector <- c()

for (id in job_ids){
  tVector <- c(tVector, getTaskCompletionTimes(id)) 
}

fVector <- filterOutliers(tVector,40)
plotTimes(fVector[[3]],set_id,prefix='set',dir=imagespath)

workerTable <- pivot(raw_data,'worker_id','relation')
workerDf <- getDf(workerTable)

sumVector <- sort(rowSums(workerDf),decreasing=TRUE)[1:40]
ids <- rownames(as.data.frame(sumVector))

workerDf <- getDf(workerTable)
mcolors <- colors()[c(26,51,76,90,93,95,101,126,151,376,56,81,255,448)]

path <- paste(imagespath, 'workerLabels_',set_id,'.jpg',sep='')

subDf <- workerDf[rownames(workerDf) %in% ids,]

jpeg(path,width=750,height=750)
barplot(t(subDf), col=mcolors, space=0.1, cex.axis=0.8,las=2,names.arg=rownames(subDf),cex=0.8,ylab='numAnnotations')
legend(5, 60, colnames(subDf), cex=0.8, fill=mcolors);
dev.off()

cat(1)
