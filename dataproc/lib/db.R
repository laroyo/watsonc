# Commond code for connecting and querying/inserting the database from the R scripts. 
library(RMySQL)
library(rjson)

con <- dbConnect(MySQL(), user=dbuser,password=dbpwd,dbname=dbname,host=host)


getJob <- function(job_ids){
  query <- sprintf('select unit_id,worker_id,worker_trust,external_type,relation,explanation,selected_words,started_at,created_at,term1,term2,sentence from cflower_results where job_id in (%s)', paste(job_ids,collapse=','))
  #print(query)
  return(dbGetQuery(con, query))
}

insertFiltSentences <- function(set_id, file_id, filter, filteredSentences){
  unit_ids <- toJSON(filteredSentences)
  query <- sprintf("insert into filtered_sentences (set_id, file_id,filter, unit_ids) values (%s,%s,'%s','%s')", set_id, file_id,filter,unit_ids)
  #print(query)  
  return(dbGetQuery(con, query))  
}

insertFiltWorkers <- function(set_id, file_id, filter, filteredWorkers){

  worker_ids <- toJSON(filteredWorkers)    
  query <- sprintf("insert into filtered_workers (set_id, file_id,filter, worker_ids) values (%s,%s,'%s','%s')", set_id, file_id,filter,worker_ids)
  return(dbGetQuery(con, query))  
}

updateResults <- function(job_id, numFiltSentences, numFiltWorkers){
  
  query <- sprintf("update results_table set number_filtered_sentences = %d, number_filtered_workers = %d where job_id = %d" , numFiltSentences,
                   numFiltWorkers,job_id)  
  return(dbGetQuery(con, query))  
}

saveFileMetadata <- function(filename, path,mime_type, filesize, creator){
  query <- sprintf("insert into file_storage (original_name,storage_path,mime_type,filesize,createdby) values ('%s', '%s', '%s',%s,'%s')",filename, path,mime_type,filesize,creator)
  #print(query)
  return(dbGetQuery(con, query))  
}

createSet <- function(job_ids){  
  query <- sprintf("insert into analysis_sets (members) values ('%s') ", toJSON(as.list(as.numeric(job_ids))))
  #print(query)
  rs <- dbSendQuery(con, query)
  dbClearResult(rs)
  id <- dbGetQuery(con, "select last_insert_id();")[1,1]  

  return(id)
}

saveWorkerMetrics <- function(set_id, filter, workerMetrics){
  numRows <- dim(workerMetrics)[1]
  for (i in seq(1,numRows)){
    row <- workerMetrics[i,]
    
    if(filter != 'NULL'){
      query <- sprintf("insert into  worker_metrics (set_id, worker_id, filter, numSents, cos, agreement,annotSentence) values (%s, %s,'%s', %s, %s, %s,%s)",
                       set_id,rownames(workerMetrics)[i], filter,row$numSent, row$cos, row$agr, row$annotSentence);
    } else {
      query <- sprintf("insert into  worker_metrics (set_id, worker_id, numSents, cos, agreement,annotSentence) values (%s, %s,%s, %s, %s,%s)",
                       set_id,rownames(workerMetrics)[i], row$numSent, row$cos, row$agr, row$annotSentence);
    }
    
    rs <- dbSendQuery(con, query)
  } 
}

getWorkerMetrics <- function(set_id){

  query <- sprintf("select * from worker_metrics where set_id = %d and filter is null", set_id)    
  print(query)
  rs<- (dbGetQuery(con, query))
  return (rs)
      
  ## query <- sprintf("select distinct(filter) from worker_metrics where set_id = %d",set_id)
  ## filters <- (dbGetQuery(con, query))
  ## res <- list()

  ## for (i in seq(1,dim(filters)[1])){
  ##   query <- sprintf("select * from worker_metrics where set_id = %d and filter = '%s'", set_id,filters[i,])    
  ##   print(query)
  ##   rs<- (dbGetQuery(con, query))    
  ##   res[[filters[i,]]] <- rs[,c('worker_id','numSents','cos','agreement','annotSentence')]    
  ## }
  ## return (res)
}

getJobsInSet <- function(set_id){
  query <- sprintf("select members from analysis_sets where set_id = %d ", set_id)
  row <- (dbGetQuery(con, query))[1,1]
  return (fromJSON(row))
}

#job_ids <- getJobsInSet(set_id)
## strptime(,'%Y-%m-%d %H:%M:%S')

getTaskCompletionTimes <- function(job_id){  
                                        
  query <- sprintf("select job_id,worker_id,started_at,created_at from cflower_results where job_id = %s", job_id)  
  rs <- dbGetQuery(con, query)
  return (as.numeric(strptime(rs$created_at,'%Y-%m-%d %H:%M:%S') - strptime(rs$started_at,'%Y-%m-%d %H:%M:%S')))    
}


getSecondsPerUnit <- function(job_id){
  
  query <- sprintf("select seconds_per_unit  from history_table where job_id = %s", job_id)
  return(dbGetQuery(con, query)[1,1])
  
}
