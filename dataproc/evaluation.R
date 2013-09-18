source('envars.R')
source('lib/db.R')
source('lib/measures.R')
source('lib/filters.R')
source(paste(libpath,'/simplify.R',sep=''),chdir=TRUE)
source('evalfunctions.R')

library(lsa)
library(XLConnect)

#job.ids <- c(145547,146309,146522)
job.ids <- c(178569, 178597, 179229, 179366)
#job.ids <- c(196304, 196306,196308)

set <- TRUE
if(set){
  #job.id <- 105 #the 'old' 90 sentences.
  job.id <- 106 #the 'new' 90 sentences.
  raw.data <- getJob(job.ids)
} else {
  job.id <- job.ids[3]
  raw.data <- getJob(job.id)
}

#Without singleton *workers* 
without.singletons <- TRUE

worker.ids <- sort(unique(raw.data$worker_id))


filters <- list('NULL', 'SQRT','NormSQRT','NormR', 'NormRAll')

#Remove the singletons
numSent <- numSentences(raw.data)
singletons <- belowFactor(numSent,'numSent',3)

worker.ids <- setdiff(worker.ids,singletons)
data <- raw.data[!(raw.data$worker_id %in% singletons),]


worker.metrics <- getWorkerMetrics(job.id, filters,without.singletons)


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
candidates <- as.numeric(candidates)

disagr <- union(rownames(spamCandidates[['NULL']]),rownames(spamCandidates[['NormSQRT']]))
disagr <- union(disagr, rownames(spamCandidates[['SQRT']]))
disagr <- union(disagr, rownames(spamCandidates[['NormSQRT']]))
disagr <- union(disagr, rownames(spamCandidates[['NormR']]))
disagr <- union(disagr, rownames(spamCandidates[['NormRAll']]))
disagr <- sort(as.numeric(disagr))

count.disagr <- data.frame(countDisagr= rep(0, length(worker.ids)))
rownames(count.disagr) <- worker.ids

for(wid in disagr){
  for (f in filters){
    if(wid %in% rownames(spamCandidates[[f]]))
      count.disagr[as.character(wid),1] <-  count.disagr[as.character(wid),1] + 1
  }
}

count.disagr[order(count.disagr$count),,drop=FALSE]

annotations <- list()

annotations[['spammers']] <- c(390141,5254360,5958908,7478095,8071333,8947442,9705524,9767020,9844590,12936896,13617382,13830562,13917479,13997142,14067668,14111684)
annotations[['lqw']] <- c(6501147,8885952,9277827,12300670,12974606,14119448)
annotations[['no_spam']] <- c(46633,275944,1246916,5983773,6768661,7336768,8927822,9873337,11307060,12299718,12507046,13291371,13300701,13438162,13637372,13664512,13769712,14050729,14058877,14081714,14083895,14133477,14157276,14161692)

if(set){
  none.other <- getFilteredWorkers(job.ids, 'none_other')
  valid.words <- getFilteredWorkers(job.ids, 'valid_words')
  rep.text <- getFilteredWorkers(job.ids, 'rep_text')
  rep.response <- getFilteredWorkers(job.ids, 'rep_response')
} else {
  none.other <- getFilteredWorkers(job.id, 'none_other')
  valid.words <- getFilteredWorkers(job.id, 'valid_words')
  rep.text <- getFilteredWorkers(job.id, 'rep_text')
  rep.response <- getFilteredWorkers(job.id, 'rep_response')  
}

content <- union(none.other,valid.words)
content <- union(content, rep.text)
content <- union(content, rep.response)
content <- sort(as.numeric(content))

empty.col <- rep(0, length(worker.ids))
count.content <- data.frame(none.other=empty.col, rep.text=empty.col, rep.response=empty.col, valid.words=empty.col, countContent=empty.col)
rownames(count.content) <- worker.ids

for(wid in content[!(content %in% singletons)]){

  wid <- as.character(wid)
  
  if(wid %in% none.other)
    count.content[wid,'none.other'] <- 1
  if(wid %in% valid.words)
    count.content[wid,'valid.words'] <- 1
  if(wid %in% rep.text)
    count.content[wid,'rep.text'] <- 1
  if(wid %in% rep.response)
    count.content[wid,'rep.response'] <- 1

  count.content[wid, 'countContent']<- rowSums(count.content[wid,])[[1]]
  
}

count.filters <- cbind(count.disagr, count.content)
count.filters$disagContent <- count.filters$countDisagr + count.filters$countContent

#exclude the candidates, and order by disagCount. 
no.disag <- count.filters[!(row.names(count.filters) %in% candidates),]
disag.order <- c(row.names(no.disag[order(no.disag$disagContent),]),candidates)

ag.metrics <- worker.metrics[['NULL']][,c('numSents','cos','agr','annotSent')]
for(f in filters){
  if(f != 'NULL'){
    ag.metrics <- cbind(ag.metrics, worker.metrics[[f]][,c('numSents','cos','agr','annotSent')])
  }
}

ag.metrics <- cbind(ag.metrics,count.filters)

wb.new <- loadWorkbook('/home/gsc/Exp3-90-sents-spam.xlsx', create = TRUE)
createSheet(wb.new, name = "singleton-workers-removed")
writeWorksheet(wb.new,ag.metrics[disag.order,],sheet="singleton-workers-removed",startRow=2,startCol=1,header=TRUE,rownames='Worker ID')
saveWorkbook(wb.new)

overlap <- TRUE

if(overlap){
  print(paste('filtered by disagreement: ', length(candidates), sep=''))
  printOverlap(candidates, none.other, 'none.other', singletons)
  printOverlap(candidates, rep.response, 'rep.response', singletons)
  printOverlap(candidates, rep.text, 'rep.text', singletons)
  printOverlap(candidates, valid.words, 'valid.words', singletons)
}

exc.expl <- union(none.other, valid.words)
exc.expl <- union(exc.expl, rep.text)
exc.expl <- union(exc.expl, rep.response)

exc.expl <- setdiff(exc.expl, candidates)

print(paste('Exclusive from explanation filters: ', length(exc.expl[!(exc.expl %in% singletons)]),sep=''))

res.none.other <- evalResults(worker.ids, none.other, annotations)
res.rep.text <- evalResults(worker.ids, rep.text, annotations)
res.rep.resp <- evalResults(worker.ids, rep.resp, annotations)
res.valid.words <- evalResults(worker.ids, valid.words, annotations)

#Section: numeric measures (precision, recall, etc)
numeric.measures <- FALSE

if(numeric.measures){
  print('None other: ')
  printCoverageMeasures(res.none.other)
  print('Repeated response: ')
  printCoverageMeasures(res.rep.resp)
  print('Repeated text: ')
  printCoverageMeasures(res.rep.text)
  print('valid words: ')
  printCoverageMeasures(res.valid.words)
}


## fmeasure(res.none.other)
## precision(res.none.other)
## recall(res.none.other)
## accuracy(res.none.other)
## fmeasure(res.rep.resp)
## precision(res.rep.resp)
## recall(res.rep.resp)
## accuracy(res.rep.resp)
















