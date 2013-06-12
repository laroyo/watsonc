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

saveWorkerMetrics <- function(dframe,set_id){
  for(worker_id in row.names(dframe)){
    row <- dframe[worker_id,]
    query <- sprintf("insert into  worker_metrics (set_id, worker_id, filter, numSents, cos, agreement,annotSentence) values (%s, %s,NULL, %s, %s, %s,%s)",                      set_id,worker_id, row$numSent, row$cos, row$agr, row$annotSentence);
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
getTaskCompletionTimes <- function(job_id, df=FALSE){
  query <- sprintf("select unit_id,worker_id,started_at,created_at from cflower_results where job_id = %s", job_id)     
  
  rs <- dbGetQuery(con, query)
  t <- as.numeric(strptime(rs$created_at,'%Y-%m-%d %H:%M:%S') - strptime(rs$started_at,'%Y-%m-%d %H:%M:%S'))
  if(df){    
    return (data.frame(unit_id=rs$unit_id, worker_id=rs$worker_id,time=t))
  } else {
    return(t)
  }
} 

insertSentClarity <- function(sentClarity){
  for (i in seq(1:length(sentClarity))){
    query <- sprintf("insert into sent_clarity (unit_id,clarity) values (%s,%s)", names(sentClarity[i]), sentClarity[i]);
    dbGetQuery(con,query)    
  }
}

selectWorkerSentenceScore <- function(worker_id){
  query <- sprintf("select unit_id,score from workerSentenceScore where worker_id = %s", worker_id)
  return (dbGetQuery(con,query))  
}

insertWorkerSentenceScore <- function(workerSentenceScore,workerSentenceCosine){
  for (i in seq(1:dim(workerSentenceScore)[1])){
    for (j in seq(1:dim(workerSentenceScore)[2])){
      if(!is.na(workerSentenceScore[i,j])){        
        query <- sprintf("insert into workerSentenceScore (worker_id,unit_id,cos,score) values (%s,%s,%s,%s)", rownames(workerSentenceScore)[i],
                         colnames(workerSentenceScore)[j],workerSentenceCosine[i,j], workerSentenceScore[i,j]);
        dbGetQuery(con,query)            
      }
    }    
  }    
}

insertWorkerRelationScore <- function(workerRelScore){
    for (i in seq(1:dim(workerRelScore)[1])){
      for (j in seq(1:dim(workerRelScore)[2])){
        if(!is.na(workerRelScore[i,j]) & workerRelScore[i,j] > 0){
          print(colnames(workerRelScore)[j])
          print(rownames(workerRelScore)[i])
          print(workerRelScore[i,j])
          query <- sprintf("insert into workerRelationScore (worker_id,relation,score) values (%s,'%s',%s)", rownames(workerRelScore)[i],
                           colnames(workerRelScore)[j],workerRelScore[i,j]);
          dbGetQuery(con,query)            
          
        }
      }
    }
  }

saveFilteredWorkers <- function(set.id,worker.ids, filter){
  for(worker.id in worker.ids){
    if(is.null(filter))
      query <- sprintf("insert into filtered_workers (set_id,filter,worker_id) values (%s,NULL,%s)", set.id,worker.id)
    else
      query <- sprintf("insert into filtered_workers (set_id,filter,worker_id) values (%s,'%s',%s)", set.id,filter,worker.id)

    dbSendQuery(con,query)                
  }  
}
