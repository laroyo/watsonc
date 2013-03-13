#!/usr/bin/Rscript
## Read file 90-sents-all-batches-GS-sentsv3.csv and applies the filters. 
## The filter output is the same as 90-sents-all-batches-CS-sentsv3.csv (Dropbox/data/CF-Results-processed/)

library(XLConnect)
print('library')
#dataproc_path <- 'C:/Users/gsc/watsonc/dataproc/'
dataproc_path <- 'C:/Users/IBM_ADMIN/Desktop/IBM/stift7/hui/dataproc/'
source(paste(dataproc_path,'lib/measures.R',sep=''),chdir=TRUE)
source(paste(dataproc_path,'lib/filters.R',sep=''),chdir=TRUE)
source(paste(dataproc_path,'lib/simplify.R',sep=''),chdir=TRUE)
source(paste(dataproc_path,'lib/output.R',sep=''),chdir=TRUE)

#For calculating the cosine. 
library(lsa)

args <- commandArgs(trailingOnly = TRUE)

#The script acepts parameters. If none passed, the following will be used as an examaple. 
if(length(args) == 0){
  inputfile <- paste(dataproc_path,"example_data/rf145547.csv",sep='')
  outputfile  <- "workerMetrics.xlsx"
} else {
  inputfile <- args[1]
  outputfile  <- args[2]
}

raw_data <- read.csv(inputfile)

#Shorten the names of some fields. 
names(raw_data)[names(raw_data)=="step_1_select_the_valid_relations"] <- "relation"
names(raw_data)[names(raw_data)=="step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1"] <- "selected_words"
names(raw_data)[names(raw_data)=="step_2b_if_you_selected_none_in_step_1_explain_why"] <- "explanation"

sentenceTable <- pivot(raw_data,'unit_id','relation')

sentenceDf <- getDf(sentenceTable)

#Calculate the measures to apply the filters.
filters <- list('SQRT','NormSQRT')

#Calculate the measures to apply the filters.filters <- list('SQRT','NormSQRT')
mdf <- calc_measures(sentenceDf,filters)

discarded <- list()
filtered <- list()

for (f in filters){
  #Apply the filters: each one returns the discarded rows (those below the threshold)
  discarded[[f]] <- belowDiff(mdf,f)
  #The 'filtered' 
  filtered[[f]] <- setdiff(rownames(sentenceDf),discarded[[f]])
}

#After applying the filters, add the "NULL" filter.
filters <- append('NULL', filters)
filtered[['NULL']] <- rownames(sentenceDf)
discarded[['NULL']] <- NULL


worker_ids <- unique(raw_data$worker_id)

out <- NULL
spamCandidates <- list()

for (f in filters){
  print(paste('computing metrics for filter ',f))
  
  filt <- raw_data[raw_data$unit_id %in% filtered[[f]],]

  filtWorkers <- unique(filt$worker_id)
  
  numSent <- numSentences(filt)
  numAnnot <- numAnnotations(filt)

  #sentMat <- list()
  
  agrValues <- agreement(filt)
  cosValues <- cosMeasure(filt)

  df <- data.frame(row.names=filtWorkers,numSents=numSent, cos=cosValues, agr=agrValues, annotSentence=(numAnnot/numSent))


  # Add empty values for filtered out workers
  missingworkers <- setdiff(worker_ids,filtWorkers)
  emptyCol <-  rep(0,length(missingworkers))
  
  filtrows <- data.frame(row.names=missingworkers,numSents=emptyCol,cos=emptyCol,agr=emptyCol,annotSentence=emptyCol)
  df <- rbind(df, filtrows)
  df <- df[order(as.numeric(row.names(df))),]

  #Empty dataframe
  spamFilters <- data.frame(row.names=worker_ids,cos=rep(0,length(worker_ids)),annotSentence=rep(0,length(worker_ids)),agr=rep(0,length(worker_ids)))

  candidateRows <- belowDiff(df,'cos')
   if(length(candidateRows) > 0){
     spamFilters[rownames(spamFilters) %in% candidateRows,]$cos = 1
   }

  candidateRows <- belowDiff(df,'annotSentence')
  if(length(candidateRows) > 0){
    spamFilters[rownames(spamFilters) %in% candidateRows,]$annotSentence = 1
  }

  candidateRows <- overDiff(df,'agr')
  if(length(candidateRows) > 0){
    spamFilters[rownames(spamFilters) %in% candidateRows,]$agr = 1
  }

  spamCandidates[[f]] <- spamFilters

  #Calculate the singletones: workers that have labeled only 1 or 2 sentences.
  if(f == 'NULL'){  
    singletones <- belowFactor(df,'numSents',3)
  }
  
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

sf <- as.data.frame(rowSums(spamFilterOutput > 0) > 1)
colnames(sf) = 'label'
spamLabels <- rownames(sf[sf$label==TRUE,,drop=FALSE])


wb.new <- loadWorkbook(outputfile, create = TRUE)

createSheet(wb.new, name = "pivot-worker")

writeOutputHeaders(wb.new,"pivot-worker")

writeWorksheet(wb.new,data=out,sheet=1,startRow=2,startCol=1,header=TRUE,rownames='Worker ID')

createSheet(wb.new, name = "singleton-workers-removed")

writeOutputHeaders(wb.new,"singleton-workers-removed")
writeWorksheet(wb.new,data=out[rownames(out) %in% setdiff(rownames(out),singletones),],sheet="singleton-workers-removed",startRow=2,startCol=1,header=TRUE,rownames='Worker ID')

createSheet(wb.new, name = "filtered-out-sentences")
writeFilteredOutHeaders(wb.new,"filtered-out-sentences")

currentCol <- 1
for (f in filters){
  if(f != 'NULL'){
    writeWorksheet(wb.new,data=discarded[[f]],sheet='filtered-out-sentences',startRow=2,startCol=currentCol,header=FALSE)
    currentCol <- currentCol + 2
  }
}

saveWorkbook(wb.new)


cat(format(length(discarded[[f]])))
cat(' $$ ')
cat(spamLabels)





