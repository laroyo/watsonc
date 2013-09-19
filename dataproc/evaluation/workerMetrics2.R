source('envars.R')
source('lib/db.R')

source('lib/measures.R')
source('lib/filters.R')
source('lib/simplify.R')
source('lib/fileStorage.R')

library(XLConnect)
library(lsa)

job.ids <- c(178569, 178597, 179229, 179366)
#job.ids <- c(196304, 196306,196308, 196309)


set <- TRUE
if(set){
  job.id <- 105 #the 'old' 90 sentences [145547,146309,146522] 
  #job.id <- 106 #the 'new' 90 sentences [196304,196306,196308]
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

for (worker.id in unique(raw.data$worker_id)){
  sentMat <- getSentenceMatrix(raw.data, worker.id)
  worker.sents <- row.names(sentMat)
  
  num.worker.sents <- length(worker.sentences)

  if(sel == 1 ){
    cand.sents <- worker.sents
  }
  
  if(sel == 2){
    # Select clear sentences.      
  }
  if(sel == 3){
  #Select unclear sentences.
  
  }
  num.sent <- c(3,6,9,15)
  for (i in seq(1:4)){
    ns <- num.sent[i]
    
    if(ns <= num.worker.sents){
      s[i] <- sample(worker.sents, ns)
    } else {
      s[i] <- NULL
    }      
  }
  
  if(wo >= 15){
    if(round(num.worker.sents * 0.75) >= 20){ # this implies that worker.annot >= 27        
                                        #Two additional  amples, one 0.75, bigger than 20 and another one for the max value.
      s[5] <- sample(workers, round(num.worker.sents * 0.75))
      s[6] <- sample(workers, num.worker.sents)                                
    } else {
                                        #Only one additional sample, for the max.
      s[5] <- sample(workers, num.worker.sents)
        s[6] <- list()
      }
    } else {
      s[5] <- list()
      s[6] <- list()
    }         
  }
  


computeWorkerMetrics <- function(raw.data){
  
}

#orig.worker.ids <- sort(unique(raw.data$worker_id))
orig.unit.ids <- sort(unique(raw.data$unit_id))

annotators <- lapply(orig.unit.ids,getSentenceAnnotators,raw_data=raw.data)

max.sample.size <- max(unlist(lapply(annotators,length)))
sample.size2 <- round(max.sample.size / 3)
sample.size3 <- round(max.sample.size / 2)

d <- list()
d2 <- NULL
d3 <- NULL
d4 <- NULL

comment <- list()

high.q <- TRUE
if(high.q || low.q){
  wm <- getWorkerMetrics(job.id, list('NULL'),without.singletons)[['NULL']]
}

excfrom3 <- NULL
excfrom4  <- NULL

for(i in seq(1:length(orig.unit.ids))){
  num.annotators <- length(annotators[[i]])

  s2 <- sample(annotators[[i]],round(max.sample.size / 3))
  d2 <- rbind(raw.data[raw.data$unit_id == orig.unit.ids[i] & raw.data$worker_id %in% s2,], d2)

  if(round(max.sample.size / 2)- num.annotators > 0) {
    print(sprintf('Warning: too small to sample 1 half (%s). Skipping row %s',num.annotators,orig.unit.ids[i]))
    excfrom3 <- c(excfrom3, orig.unit.ids[i])
    excfrom4 <- c(excfrom4, orig.unit.ids[i])
    next
  } else {
    s3 <- sample(annotators[[i]],round(max.sample.size / 2))
    d3 <- rbind(raw.data[raw.data$unit_id == orig.unit.ids[i] & raw.data$worker_id %in% s3,], d3)
  }
  
  
  if(round(2*max.sample.size / 3)- num.annotators > 1) {
    print(sprintf('Warning: too small to sample 2 thirds(%s). Skipping row %s',num.annotators,orig.unit.ids[i]))
    excfrom4 <- c(excfrom4, orig.unit.ids[i])
    next
  }
  
  if(round(2*max.sample.size / 3)- num.annotators == 1) {    
    sample.size4 <- num.annotators
    s4 <- sample(annotators[[i]],num.annotators)
  } else {
    if(!exists('sample.size4')){
      sample.size4 <- round(2*max.sample.size / 3)
    }
    s4 <- sample(annotators[[i]],round(2*max.sample.size / 3))
    d4 <- rbind(raw.data[raw.data$unit_id == orig.unit.ids[i] & raw.data$worker_id %in% s4,], d4)
  } 
}

comment[1] <- sprintf("Orignal metrics (using all the available workers)")
comment[2] <- sprintf("Using only %s workers per sentence (1 third of the number of workers per sentence [%s])", sample.size2, max.sample.size)
comment[3] <- sprintf("Using only %s workers per sentence (1 half of the number of workers per sentence [%s])", sample.size3, max.sample.size)
comment[4] <- sprintf("Using only %s workers per sentence (2 thirds of the number of workers per sentence [%s])", sample.size4, max.sample.size)

d[[1]] <- raw.data
d[[2]] <- d2
d[[3]] <- d3
d[[4]] <- d4

fname <- getFileName(job.id,fileTypes[['sentMetricsEvaluation']])
path <- getFilePath(job.id, folderTypes[['analysisFiles']], FALSE)

wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)

ws.name <- list("original-sent-rel-metrics","sent-rel-metrics1", "sent-rel-metrics2", "sent-rel-metrics3")

sentClarity <- list()
sentRelDf <- list()

for (i in seq(1:4)){

  sentRelDf[[i]] <- sentRelScoreMeasure(d[[i]])
  sentClarity[[i]] <- sentenceClarity(sentRelDf[[i]])  
}

sc1 <- as.data.frame(sentClarity[[1]])

#colnames(diff) <- c('all - s1',,'all -s3')

diff1 <- sentClarity[[1]] - sentClarity[[2]]
diff2 <- sc1[!(row.names(sc1) %in% excfrom3),,drop=FALSE] - sentClarity[[3]]
diff3 <- sc1[!(row.names(sc1) %in% excfrom4),,drop=FALSE] - sentClarity[[4]]

saveEvaluationMetrics(wb.new,'Overview',diff1,diff2,diff3)

for (i in seq(1:4)){
  createSheet(wb.new, name = ws.name[[i]])
  saveSentenceMetrics(wb.new,ws.name[[i]],sentRelDf[[i]],sentClarity[[i]])
  
  writeWorksheet(wb.new,data=comment[[i]],sheet=ws.name[[i]],startRow=1,startCol=5,header=FALSE)
  mergeCells(wb.new, sheet = ws.name[[i]], reference = "E1:M1")
}

 saveWorkbook(wb.new)














