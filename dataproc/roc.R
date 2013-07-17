source('envars.R')
source('lib/db.R')
source('lib/measures.R')
source('lib/filters.R')
source(paste(libpath,'/simplify.R',sep=''),chdir=TRUE)

library(lsa)
library(ROCR)

#job.ids <- c(145547,146309,146522)
job.ids <- c(178569, 178597, 179229, 179366)

raw.data <- getJob(job.ids)

worker.ids <- sort(unique(raw.data$worker_id))

## sentenceTable <- pivot(raw.data,'unit_id','relation')

## sentenceDf <- getDf(sentenceTable)

## #Calculate the measures to apply the filters.
#filters <- list('NULL', 'NormRAll')
filters <- list('NULL', 'SQRT','NormSQRT','NormR', 'NormRAll')

## #Calculate the measures to apply the filters.filters <- list('SQRT','NormSQRT')
## mdf <- calc_measures(sentenceDf,filters)

## for (f in filters){
##   print(paste('computing metrics for filter ',f))

##   if(f != 'NULL')
##     filt <- raw.data[data$unit_id %in% filtered[[f]],]
##   else 
##     filt <- raw.data
  
##   filtWorkers <- sort(unique(filt$worker_id))
  
##   numSent <- numSentences(filt)
##   numAnnot <- numAnnotations(filt)
  
##   annotSentence <- numAnnot / numSent
##   colnames(annotSentence) <- 'annotSentence'
  
  
##   agrValues <- agreement(filt)
##   cosValues <- cosMeasure(filt)
##   df <- cbind(numSent,cosValues, agrValues,annotSentence)
##   saveWorkerMetrics(cbind(agrValues, cosValues,annotSentence,numSent), job_id,f)
## }

#Remove the singletons
numSent <- numSentences(raw.data)
singletons <- belowFactor(numSent,'numSent',3)
worker.ids <- setdiff(worker.ids,singletons)
data <- raw.data[!(raw.data$worker_id %in% singletons),]

job.id <- 105 #Aggregated 90 sentences.



#job.id <- job.ids[4]
worker.metrics <- list()
query <- sprintf("select worker_id,numSents,cos,agreement as agr,annotSentence as annotSent from worker_metrics where set_id = %s and
 filter is null order by worker_id", job.id)
worker.metrics[['NULL']] <- dbGetQuery(con,query)
row.names(worker.metrics[['NULL']]) <- worker.metrics[['NULL']]$worker_id
worker.metrics[['NULL']] <- worker.metrics[['NULL']][!(worker.metrics[['NULL']]$worker_id %in% singletons),]


query <- sprintf("select worker_id,numSents,cos,agreement as agr,annotSentence as annotSent from worker_metrics where set_id = %s and
 filter ='SQRT' order by worker_id", job.id)
worker.metrics[['SQRT']] <- dbGetQuery(con,query)
row.names(worker.metrics[['SQRT']]) <- worker.metrics[['SQRT']]$worker_id
worker.metrics[['SQRT']] <- worker.metrics[['SQRT']][!(worker.metrics[['SQRT']]$worker_id %in% singletons),]

query <- sprintf("select worker_id,numSents,cos,agreement as agr,annotSentence as annotSent from worker_metrics where set_id = %s and
 filter ='NormSQRT' order by worker_id", job.id)
worker.metrics[['NormSQRT']] <- dbGetQuery(con,query)
row.names(worker.metrics[['NormSQRT']]) <- worker.metrics[['NormSQRT']]$worker_id
#Remove singletons
worker.metrics[['NormSQRT']] <- worker.metrics[['NormSQRT']][!(worker.metrics[['NormSQRT']]$worker_id %in% singletons),]


query <- sprintf("select worker_id,numSents,cos,agreement as agr,annotSentence as annotSent from worker_metrics where set_id = %s and
 filter ='NormR' order by worker_id", job.id)

worker.metrics[['NormR']] <- dbGetQuery(con,query)
row.names(worker.metrics[['NormR']]) <- worker.metrics[['NormR']]$worker_id

#Remove singletons
worker.metrics[['NormR']] <- worker.metrics[['NormR']][!(worker.metrics[['NormR']]$worker_id %in% singletons),]


query <- sprintf("select worker_id,numSents,cos,agreement as agr,annotSentence as annotSent from worker_metrics where set_id = %s and
 filter ='NormRAll' order by worker_id",job.id)
worker.metrics[['NormRAll']] <- dbGetQuery(con,query)
row.names(worker.metrics[['NormRAll']]) <- worker.metrics[['NormRAll']]$worker_id

#Remove singletons
worker.metrics[['NormRAll']] <- worker.metrics[['NormRAll']][!(worker.metrics[['NormRAll']]$worker_id %in% singletons),]

## query <- sprintf("select worker_id, relation,explanation,selected_words,sentence from cflower_results where job_id in (%s)", paste(job_id, collapse=','))
## res <- dbGetQuery(con,query)

spamFilters <- list()
spamCandidates <- list()

for (f in filters){

  spamFilters[[f]] <- data.frame(row.names=worker.metrics[[f]]$worker_id,cos=rep(0,length(worker.metrics[[f]]$worker_id)),
                                    annotSentence=rep(0,length(worker.metrics[[f]]$worker_id)),agr=rep(0,length(worker.metrics[[f]]$worker_id)))

  candidateRows <- belowDiff(worker.metrics[[f]],'cos')

  if(length(candidateRows) > 0 & dim(spamFilters[[f]][rownames(spamFilters[[f]]) %in% candidateRows,])[1]>0){
    spamFilters[[f]][rownames(spamFilters[[f]]) %in% candidateRows,]$cos = 1
  }

  candidateRows <- overDiff(worker.metrics[[f]],'annotSent')
  
  if(length(candidateRows) > 0 & dim(spamFilters[[f]][rownames(spamFilters[[f]]) %in% candidateRows,])[1]>0){    
    spamFilters[[f]][rownames(spamFilters[[f]]) %in% candidateRows,]$annotSentence = 1
  }
  
  candidateRows <- belowDiff(worker.metrics[[f]],'agr')
  if(length(candidateRows) > 0 & dim(spamFilters[[f]][rownames(spamFilters[[f]]) %in% candidateRows,])[1]>0){
                                        #if(length(candidateRows) > 0){
    spamFilters[[f]][rownames(spamFilters[[f]]) %in% candidateRows,]$agr = 1
  }
  
  spamCandidates[[f]] <- spamFilters[[f]][rowSums(spamFilters[[f]]) > 1,]
}


candidates <- intersect(rownames(spamCandidates[['NULL']]),rownames(spamCandidates[['SQRT']]))             
candidates <- intersect(candidates, rownames(spamCandidates[['NormSQRT']]))
candidates <- intersect(candidates, rownames(spamCandidates[['NormR']]))
candidates <- intersect(candidates, rownames(spamCandidates[['NormRAll']]))

## res <- getJob(job.ids)

## res$selected_words <- apply(res[,'selected_words',drop=FALSE],1,FUN=correctMisspells)
## res$explanation <- apply(res[,'explanation',drop=FALSE],1,FUN=correctMisspells)

## oth.non <- res[intersect(grep('OTHER|NONE',res$relation),grep('\n',res$relation)),]

## filtWorkers <- list()
## filtWorkers[['none_other']] <- noneOther(oth.non)
## filtWorkers[['rep_response']] <- repeatedResponse(res)
## filValWords <- validWords(res)
## filtWorkers[['valid_words']] <- sort(unique(filValWords$worker_id))
## filtWorkers[['rep_text']] <- repeatedText(job_id,'both')
 
evalResults <- function(worker.ids, filtered){

spammers <- c(390141,5254360,5958908,7478095,8071333,8947442,9705524,9767020,9844590,12936896,13617382,13830562,13917479,13997142,14067668,14111684)
lqw <- c(6501147,8885952,9277827,12300670,12974606,14119448)
r.norm <- c(3008101,5254360,8947442,9277827,9873337,12300670,13300701,13438162,13617382,13745870,14050729)

no.spam <- c(46633,275944,1246916,5983773,6768661,7336768,8927822,9873337,11307060,12299718,12507046,13291371,13300701,13438162,13637372,13664512,13769712,14050729,14058877,14081714,14083895,14133477,14157276,14161692)

spam <- c(spammers, lqw, r.norm)

not.found <- c()
false.pos <- c()
true.pos <- c()
true.neg <- c()
false.neg <- c()

res <- list()

for (worker.id in worker.ids){

  if(worker.id %in% spam || worker.id %in% no.spam){

    if(worker.id %in% no.spam){
      #Predicted positive, result is negative.
      if(!(worker.id %in% filtered)){
        true.neg <- c(true.neg, worker.id)
      } else {
        false.pos <- c(false.pos, worker.id)
      }      
    } else {
      if(worker.id %in% filtered){
        true.pos <- c(true.pos, worker.id)
      } else {
        false.neg <- c(false.neg, worker.id)
      }
    }   
  } else {
    not.found <- c(not.found,worker.id)
  }
}

res[['false.neg']] <- false.neg
res[['false.pos']] <- false.pos
res[['true.pos']] <-  true.pos
res[['true.neg']] <-  true.neg
res[['not.found']] <-  not.found
return (res)

}

calc.perf <- function(res){
  output <- c(rep(1,length(res[['true.pos']])),
              rep(0,length(res[['true.neg']])),
              rep(1, length(res[['false.pos']])),
              rep(0, length(res[['false.neg']])))
  labels <- c(rep(1,length(res[['true.pos']])),
              rep(0,length(res[['true.neg']])),
              rep(0, length(res[['false.pos']])),
              rep(1, length(res[['false.neg']])))

  ## print(output)
  ## print(labels)
  pred <- prediction(output, labels)
  perf <- performance(pred, "tpr", "fpr")
  return (perf)
}


## pred <- prediction(c(rep(1,length(true.pos)),rep(0,length(true.neg)),rep(1, length(false.pos)), rep(0, length(false.neg))),c(rep(1,length(true.pos)),rep(0,length(true.neg)),rep(0, length(false.pos)), rep(1, length(false.neg))))
## perf <- performance(pred, "tpr", "fpr")
## plot(perf)

query <- paste("select distinct(worker_id) from filtered_workers where set_id in (", paste(job.ids, collapse=','), ") and filter ='none_other' order by worker_id", sep='')
res <- dbGetQuery(con,query)
none.other <- res$worker_id

query <- paste("select distinct(worker_id) from filtered_workers where set_id in (", paste(job.ids, collapse=','), ") and filter ='rep_response' order by worker_id", sep='')
res <- dbGetQuery(con,query)
rep.resp <- res$worker_id

query <- paste("select distinct(worker_id) from filtered_workers where set_id in (", paste(job.ids, collapse=','), ") and filter ='rep_text' order by worker_id", sep='')
res <- dbGetQuery(con,query)
rep.text <- res$worker_id

query <- paste("select distinct(worker_id) from filtered_workers where set_id in (", paste(job.ids, collapse=','), ") and filter ='valid_words' order by worker_id", sep='')
res <- dbGetQuery(con,query)
valid.words<- res$worker_id

res.none.other <- evalResults(worker.ids, none.other)
res.rep.text <- evalResults(worker.ids, rep.text)
res.rep.resp <- evalResults(worker.ids, rep.resp)
res.valid.words <- evalResults(worker.ids, valid.words)

res.none.other[['not.found']] == res.rep.text[['not.found']] 
res.rep.resp[['not.found']] == res.rep.text[['not.found']] 

print('none.other')
#perf.none.other <- calc.perf(res.none.other)
print('rep.text')
#perf.rep.text <- calc.perf(res.rep.text)
print('rep.resp')
#perf.rep.resp <- calc.perf(res.rep.resp)

## par(mfrow=c(1,3))
## plot(perf.none.other)
## plot(perf.rep.text)
## plot(perf.rep.resp)


fmeasure <- function(true.pos, true.neg, false.pos, false.neg){
    return ((2 * true.pos)  / (2 * true.pos +  false.pos + false.neg))
}

fscore <- function(prec, rec){
  return ((2 * rec * prec) / (rec+prec))
}

precision <- function(true.pos, true.neg, false.pos, false.neg){
   return (true.pos / (true.pos + false.pos))
}

recall <- function(true.pos, true.neg, false.pos, false.neg){
  return (true.pos / (true.pos + false.neg))
}

accuracy <- function(true.pos, true.neg, false.pos, false.neg){
  return ((true.pos + true.neg) / (true.pos + false.pos + true.neg + false.neg))
}

measures <- function(res){
   true.pos <- length(res$true.pos)
   false.neg <- length(res$false.neg)
   false.pos <- length(res$false.pos)
   true.neg <- length(res$true.neg)

   print(paste('fmeasure: ', fmeasure(true.pos, true.neg, false.pos, false.neg)))

   prec <- precision(true.pos, true.neg, false.pos, false.neg)
   rec <- recall(true.pos, true.neg, false.pos, false.neg)

   print(paste('precision: ', prec))
   print(paste('recall: ', rec))
   print(paste('fscore: ', fscore(prec, rec)))

   print(paste('accuracy: ', accuracy(true.pos, true.neg, false.pos, false.neg)))
   print('------------------------')
}

print('None other: ')
measures(res.none.other)
print('Repeated response: ')
measures(res.rep.resp)
print('Repeated text: ')
measures(res.rep.text)
print('valid words: ')
measures(res.valid.words)


## fmeasure(res.none.other)
## precision(res.none.other)
## recall(res.none.other)
## accuracy(res.none.other)
## fmeasure(res.rep.resp)
## precision(res.rep.resp)
## recall(res.rep.resp)
## accuracy(res.rep.resp)


#plot(perf)















