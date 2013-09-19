source('envars.R')
source('lib/db.R')

source('lib/measures.R')
source('lib/filters.R')
source('lib/simplify.R')
source('lib/fileStorage.R')
source('evalmfunctions.R')

library(XLConnect)
library(lsa)

#job.ids <- c(178569, 178597, 179229, 179366)
job.ids <- c(196304, 196306,196308, 196309)


set <- TRUE
if(set){
  #job.id <- 105 #the 'old' 90 sentences [145547,146309,146522] 
  job.id <- 106 #the 'new' 90 sentences [196304,196306,196308]
  raw.data <- getJob(job.ids)
} else {
  job.id <- job.ids[1]
  raw.data <- getJob(job.id)
}

if(job.id == 196344){
  raw.data <- raw.data[raw.data$relation != '',]
}

#Removing spammers 

query <- sprintf("select distinct(worker_id) from filtered_workers where set_id in (%s) and filter = 'disag_filters'",paste(job.ids,collapse=','))
spammers <- dbGetQuery(con,query)$worker_id
raw.data <- raw.data[!(raw.data$worker_id %in% spammers),]

#Without singleton *workers* 
without.singletons <- TRUE

if(without.singletons){  
  numSent <- numSentences(raw.data)
  singletons <- belowFactor(numSent,'numSent',3)
  raw.data <- raw.data[!(raw.data$worker_id %in% singletons),]
}

sentenceTable <- pivot(raw.data,'unit_id','relation')

sentenceDf <- getDf(sentenceTable)
#orig.sent.metrics <- getSentenceMetrics(job.id, filters,without.singletons)
measuresDf <- calc_measures(sentenceDf,list('SumSQ','SQRT','Difference','NormSQRT','SumSQRel','SQRTRel','DiffRel'))


sentRelDf <- sentRelScoreMeasure(raw.data)
sentClarity <- sentenceClarity(sentRelDf)

nnot <- numAnnotations(raw.data)

sel <- 1

ws.names <- list()
ws.names[[1]] <- 'randomWorkers'
ws.names[[2]] <- 'highQWorkers'
ws.names[[3]] <- 'lowQWorkers'


## s1 <- sampleWorkers(raw.data, 2)
## s2 <- sampleWorkers(raw.data, 2)
## s3 <- sampleWorkers(raw.data, 3)

fname <- getFileName(job.id,fileTypes[['workerMetricsEvaluation']])
path <- getFilePath(job.id, folderTypes[['analysisFiles']], FALSE)

wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)

s <- list()
m <- list()

for (sel in seq(1:3)){  
  createSheet(wb.new, name = ws.names[[sel]])
  s[[sel]] <- sampleWorkers(raw.data, sel)  
  #saveSampleSize(wb.new, ws.names[[sel]], s[[sel]])
  
  m[[sel]] <- compWorkerMetrics(s[[sel]])
  saveWorkerMetrics(wb.new, ws.names[[sel]],m[[sel]])
}

#m.1 <- compWorkerMetrics(s[[1]])
## for (sel in seq(1:3)){
  
## }

saveWorkbook(wb.new)




  
  
















