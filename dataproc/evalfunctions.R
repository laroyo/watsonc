getWorkerMetrics <- function(job.id, filters,without.singletons){

  worker.metrics <- list()
  if(without.singletons)
    table.name <- 'worker_metsing'
  else
    table.name <- 'worker_metrics'
  
  for (f in filters){
    if(f == 'NULL'){
      query <- sprintf("select worker_id,numSents,cos,agreement as agr,annotSentence as annotSent from %s where set_id = %s and
         filter is null order by worker_id", table.name, job.id)

      worker.metrics[['NULL']] <- dbGetQuery(con,query)
      row.names(worker.metrics[['NULL']]) <- worker.metrics[['NULL']]$worker_id
      #worker.metrics[['NULL']] <- worker.metrics[['NULL']][!(worker.metrics[['NULL']]$worker_id %in% singletons),]
    } else {
      query <- sprintf("select worker_id,numSents,cos,agreement as agr,annotSentence as annotSent from %s where set_id = %s and
         filter ='%s' order by worker_id", table.name,job.id, f)
      worker.metrics[[f]] <- dbGetQuery(con,query)
      row.names(worker.metrics[[f]]) <- worker.metrics$worker_id
      #worker.metrics[[f]] <- metrics[!(metrics$worker_id %in% singletons),]      
    }
  }
  return (worker.metrics)
}

getWorkers <- function(job.ids, filter){
  if(filter == 'NULL'){
      query <- sprintf("select distinct(worker_id) from filtered_workers where set_id in (%s) and filter is null order by worker_id",
                       paste(job.ids, collapse=','), filter)
  } else {
    query <- sprintf("select distinct(worker_id) from filtered_workers where set_id in (%s) and filter ='%s' order by worker_id",
                     paste(job.ids, collapse=','), filter)
  }
  res <- dbGetQuery(con,query)
  return(res$worker_id)
}

# Given a list of spammers (filtered) and the annotated spammers (annotations)
# returns  the false/true positives/negatives for the filtered individuals. 
evalResults <- function(worker.ids, filtered, annotations){
  
  spam <- c(annotations['spammers'], annotations['lqw'])
  no.spam <- c(annotations['no_spam'])
  
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

#Computes precission and recall (et al) measures, and prints them. 
printCoverageMeasures <- function(res){
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

#Computes the overlap between disagreement and other filter.
printOverlap <- function(filtered.disagr, filtered.other,filter.name,singletons){
  flagged <- filtered.other[!(filtered.other %in% singletons)]
  overlap <- length(intersect(flagged, filtered.disagr))  
  print(sprintf(' %s: identified workers: %d - Overlapping: %d - percentage : %s', filter.name, length(flagged), overlap, (overlap * 100 /   length(flagged))))

}

