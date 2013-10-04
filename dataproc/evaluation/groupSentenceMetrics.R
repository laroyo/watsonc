#!/usr/bin/Rscript
source('/var/www/html/wcs/dataproc/envars.R')

# This scripts parses a results file from Crowdflower, and generates contingency tables for relations/workers and sentences, and relation clusters as an output.
# TODO: use only one script for both sets and jobs (merge this and sentenceMetrics.R)

library(XLConnect)


source(paste(libpath,'/','simplify.R',sep=''))
source(paste(libpath,'/','measures.R',sep=''))
source(paste(libpath,'/','filters.R',sep=''))
source(paste(libpath,'/','db.R',sep=''))
source(paste(libpath,'/','fileStorage.R',sep=''))

#source(paste(libpath,'/','output.R',sep=''))
#To calculate the permutations for the relation similarity. 
library(gtools)
#For calculating the cosine. 
library(lsa)


#job.ids <- c(139057 ,132248,132763,134491,145309,143343 ,145547 ,146309 ,146522, 178569,178597,179229,179366,196304,196306,196308,196309,196344,199057)
job.ids <- c(145309,143343 ,145547 ,146309 ,146522, 178569,178597,179229,179366,196304,196306,196308,196309,196344,199057)
#job.ids <- c(145309)

job.id <- 100

#eq <- dbGetQuery(con, 'select unit_id,chang_id from unit_index_equivalences where chang_id not like "%-%"')
query <- 'select chang_id,unit_id from unit_index_equivalences where chang_id in (select chang_id from unit_index_equivalences where chang_id not like "%-%" group by chang_id having count(*) > 1)'
eq <- dbGetQuery(con, query);


outputfile <- paste(filespath,'/','AnalysisFiles','/',job.id,'_sentenceMetrics.xlsx',sep='')

query <- sprintf('select unit_id,worker_id,worker_trust,external_type,relation,explanation,selected_words,started_at,created_at,term1,term2,sentence from cflower_results where job_id in (%s)', paste(job.ids, collapse=','))
raw_data <- dbGetQuery(con, query)

raw_data <- raw_data[raw_data$relation != '',]

if(dim(raw_data)[1] == 0){
  cat('JOB_NOT_FOUND')
} else {

  for (unit_id in eq$unit_id){
    raw_data[raw_data$unit_id == unit_id,]$unit_id <- eq[eq$unit_id == unit_id,]$chang_id
  }
  
  #Spammers
  query <- sprintf("select distinct(worker_id) from filtered_workers where filter = 'disag_filters' or filter = 'beh_filters'")
  spammers <- dbGetQuery(con,query)$worker_id
  
  #Shorten the names of some fields. 
  names(raw_data)[names(raw_data)=="step_1_select_the_valid_relations"] <- "relation"
  names(raw_data)[names(raw_data)=="step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1"] <- "selected_words"
  names(raw_data)[names(raw_data)=="step_2b_if_you_selected_none_in_step_1_explain_why"] <- "explanation"

  sentenceTable <- pivot(raw_data,'unit_id','relation')
  sentenceDf <- getDf(sentenceTable)
  
  #Save the initial pivot table (with data from CF)
  fname <- getFileName(job.id,fileTypes[['pivotCFOutput']])
  path <- getFilePath(job.id, folderTypes[['analysisFiles']], FALSE)
    
  wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)
  createSheet(wb.new, name = "pivot-CF-output")
  writeWorksheet(wb.new,data=format(sentenceDf,digits=2),sheet=1,startRow=1,startCol=1,rownames='unit.id')  
  saveWorkbook(wb.new)

  #Removing spammers. 
  data <- raw_data[!(raw_data$worker_id %in% spammers),]
  sentenceTable <- pivot(data,'unit_id','relation')
  sentenceDf <- getDf(sentenceTable)    
  
  #genHeatMap(sentenceDf,job_id,dir=path)

  #FIXME: get the adecuate value for the creator
  #creator = 'script'
  #saveFileMetadata(fname,path,mimeTypes[['excel']],-1,creator)

  #Get chang data and equivalences, to be added to the spreadsheets. 
  query <- 'select chang_id,unit_id from unit_index_equivalences where chang_id in (select chang_id from unit_index_equivalences)'
  equivalences <- dbGetQuery(con, query);

  chang.data <- dbGetQuery(con, "select ID as chang_id,relation_type,term1,b1,e1,term2,b2,e2,sentence from chang_data")

  lower.cases <- grep('[a-z]+', chang.data$term1)  
  chang.data[lower.cases,]$term1 <- paste('[',toupper(chang.data[lower.cases,]$term1),']',sep='')
  chang.data[lower.cases,]$term2 <- paste('[',toupper(chang.data[lower.cases,]$term2),']',sep='')   


  #####
  # Pivot with singleton workers.
  ####

  mDf <- addChangData(sentenceDf,chang.data)
  
  fname <- getFileName(job.id,fileTypes[['pivotWithSingletons']])
  path <- getFilePath(job.id, folderTypes[['analysisFiles']], FALSE)
  
  wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)
  createSheet(wb.new, name = "pivot-with-singleton-workers")

  for(r in seq(1:dim(mDf)[1]+1)){         
    mergeCells(wb.new, sheet = 1, reference = paste("X",r,":","AH",r,sep=''))                                        
  }

  writeWorksheet(wb.new,data=format(mDf,digits=2),sheet=1,startRow=1,startCol=1,rownames=NULL)
  saveWorkbook(wb.new)



  ###
  # Pivot without singleton workers
  ###
  
  #Singletons
  numSent <- numSentences(data)
  singletons <- belowFactor(numSent,'numSent',3)

  sentenceTable <- pivot(data[!(data$worker_id %in% singletons),],'unit_id','relation')
  sentenceDf <- getDf(sentenceTable)  
  
  #sentenceDf$unit_id <- row.names(sentenceDf)
  
  mDf <- addChangData(sentenceDf,chang.data)  
  fname <- getFileName(job.id,fileTypes[['pivotWithoutSingletons']])
  path <- getFilePath(job.id, folderTypes[['analysisFiles']], FALSE)
  
  wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)
  
  print('Generated file: ')
  print(paste(path,fname,sep='/'))
  
  createSheet(wb.new, name = "pivot-without-singleton-workers")
  for(r in seq(1:dim(mDf)[1]+1)){         
    mergeCells(wb.new, sheet = 1, reference = paste("X",r,":","AH",r,sep=''))                                        
  }
  writeWorksheet(wb.new,data=format(mDf,digits=2),sheet=1,startRow=1,startCol=1,rownames=NULL)
  saveWorkbook(wb.new)

  ###
  # Sentence and relation Metrics
  ###

  measuresDf <- calc_measures(sentenceDf,list('SumSQ','SQRT','Difference','NormSQRT','SumSQRel','SQRTRel','DiffRel'))

  #Merge the data and the measures data frames into a single df, to export it. 
  #combinedDf <- merge(sentenceDf,measuresDf,by=0)
  fname <- getFileName(job.id,fileTypes[['sentenceMetrics']])
  path <- getFilePath(job.id, folderTypes[['analysisFiles']], FALSE)
  
  wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)
  print('Generated file: ')
  print(paste(path,fname,sep='/'))
  
  createSheet(wb.new, name = "sentence-metrics")

  mDf <- addChangData(measuresDf,chang.data)
  for(r in seq(1:dim(mDf)[1]+1)){         
    mergeCells(wb.new, sheet = 'sentence-metrics', reference = paste("R",r,":","X",r,sep=''))                                        
  }
  
  writeWorksheet(wb.new,data=format(mDf,digits=2),sheet=1,startRow=1,startCol=1,rownames='unit_id')
   
  ## workerTable <- pivot(raw_data,'worker_id','relation')
  ## workerDf <- as.data.frame(rbind(sentenceTable))
  ## workerDf <- labeldf(workerDf)
  
  ## wmeasuresDf <- calc_measures(workerDf,list('RelCount'))
  ## combinedWorkersDf <- merge(workerDf,wmeasuresDf,by=0)
  
  ## createSheet(wb.new, name = "pivot-worker")
  ## writeWorksheet(wb.new,data=combinedWorkersDf,sheet=2,startRow=1,startCol=1,rownames=NULL)

  sentRelDf <- sentRelScoreMeasure(data)
  sentClarity <- sentenceClarity(sentRelDf)

  createSheet(wb.new, name = "sent-rel-metrics")
  saveSentenceMetrics(wb.new,"sent-rel-metrics",sentRelDf,sentClarity)
  
  relClarity <- relationClarity(sentRelDf)

  createSheet(wb.new, name = "relation-metrics")
  relSimilarity <- relationSimilarity(data)
  relAmbiguity <- relationAmbiguity(relSimilarity)

  saveRelationSimilarity(wb.new,"relation-metrics",relSimilarity,relAmbiguity,relClarity)

  saveWorkbook(wb.new)
  
  ###
  # Relation clusters
  ###


  clusters <- list()
  for(sent.id in row.names(sentenceDf)){

    #To calculate the top, second, etc. 
    order.values <- rev(sort(as.vector(as.matrix(sentenceDf[sent.id,]))))
    
    
    for (relation in rels){
      #if(sentenceDf[sent.id, relation] > 0){

        ## if(as.numeric(sent.id) < 1000) {
        ##   chang.id <- sent.id
        ##   unit.id <- 0
        ## } else {

        if(as.numeric(sent.id) < 200000000) {
          unit.id <- 'M'
          chang.id <- sent.id
        } else {
          chang.id <- dbGetQuery(con,sprintf('select chang_id from unit_index_equivalences where unit_id = %s limit 1', sent.id))$chang_id
          if(is.null(chang.id)){
            chang.id <- 0
          }
          unit.id <- sent.id
        }
       
        
        row <- data.frame(chang.id=chang.id,
                          unit.id=unit.id,
                          sentRelScore=sentRelDf[sent.id, relation], selectedBy=sentenceDf[sent.id, relation],
                          totalWorkers=length(unique(data[data$unit_id == sent.id,]$worker_id)),
                          rank=match(sentenceDf[sent.id,relation],order.values),
                          numRankElems=length(order.values[order.values > 0])
                          )
        
        row.names(row) <- sent.id
        if(is.null(clusters[[relation]]))
          clusters[[relation]] <- row
        else
          clusters[[relation]] <- rbind(clusters[[relation]], row)
      #}
      }
    
  }
  

  #Sort the clusters by sentRelScore (desc).
  for(relation in rels){
    if(!is.null(clusters[[relation]])){
      clusters[[relation]] <- clusters[[relation]][order(-clusters[[relation]]$sentRelScore),]
    }
  }

  fname <- getFileName(job.id,fileTypes[['relationCluster']])
  path <- getFilePath(job.id, folderTypes[['analysisFiles']], FALSE)
  
  wb.new <- loadWorkbook(paste(path,fname,sep='/'), create = TRUE)
   
  for(relation in rels){
    createSheet(wb.new, name = relation)
    for(r in seq(1:dim(mDf)[1]+1)){         
      mergeCells(wb.new, sheet = relation, reference = paste("Q",r,":","Z",r,sep=''))                                        
    }
    mDf <- addChangData(clusters[[relation]],chang.data)
    writeWorksheet(wb.new,data=mDf, startRow=2, sheet=relation, startCol=1, rownames=NULL,header=TRUE)    
  }
  
  saveWorkbook(wb.new)
  
  dbDisconnect(con)
  cat('OK')

}


