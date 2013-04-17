# Commond code for connecting and querying/inserting the database from the R scripts. 
library(RMySQL)
library(rjson)

con <- dbConnect(MySQL(), user=dbuser,password=dbpwd,dbname=dbname,host=host)


getJob <- function(job_id){
  query <- sprintf('select unit_id,worker_id,worker_trust,external_type,relation,explanation,selected_words,started_at,created_at,term1,term2,sentence from cflower_results where job_id = %s', job_id)
  #print(query)
  return(dbGetQuery(con, query))
}

insertFiltSentences <- function(job_id, file_id, filter, filteredSentences){
  unit_ids <- toJSON(filteredSentences)    
  query <- sprintf("insert into filtered_sentences (job_id, file_id,filter, unit_ids) values (%s,%s,'%s','%s')", job_id, file_id,filter,unit_ids)
  #print(query)
  
  return(dbGetQuery(con, query))  
}

insertFiltWorkers <- function(job_id, file_id, filter, filteredWorkers){

  worker_ids <- toJSON(filteredWorkers)    
  query <- sprintf("insert into filtered_workers (job_id, file_id,filter, worker_ids) values (%s,%s,'%s','%s')", job_id, file_id,filter,worker_ids)
  return(dbGetQuery(con, query))  
}

updateResults <- function(job_id, numFiltSentences, numFiltWorkers){
  
  query <- sprintf("update results_table set number_filtered_sentences = %s, number_filtered_workers = %s where job_id = %s" , numFiltSentences,
                   numFiltWorkers,job_id)  
  return(dbGetQuery(con, query))  
}

saveFileMetadata <- function(filename, path,mime_type, filesize, creator){
  query <- sprintf("insert into file_storage (original_name,storage_path,mime_type,filesize,createdby) values ('%s', '%s', '%s',%s,'%s')",filename, path,mime_type,filesize,creator)
  print(query)
  return(dbGetQuery(con, query))  
}

