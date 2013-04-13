calc_measures <- function(dframe,measures,add=FALSE){

  for (m in measures) {
    if(m != 'NULL'){
      #Add a mesure => calculate the measure and add the data.
      dframe[[m]] <- do.call(m,list(dframe))
    }
  }
  if(add){
    return(dframe)
  } else{
    return (dframe[,unlist(measures),drop=FALSE])
  }
}

RelCount <- function(dframe){
  return(rowSums(dframe[,all]))
}

SumSQ <- function(dframe){
    return(rowSums(dframe[,all]^2))
}

SumSQRel <- function(dframe){
    return(rowSums(dframe[,rels]^2))
}

SQRT <- function(dframe){
  return(sqrt(rowSums(dframe[,all]^2)))
}

SQRTRel <- function(dframe){
  return(sqrt(rowSums(dframe[,rels]^2)))
}

#Difference between the SQRT and the max element of the row.
Difference <- function(dframe){
  return(sqrt(rowSums(dframe[,all]^2)) - apply(dframe[,all],1,max))
}

DiffRel <- function(dframe){
  return(sqrt(rowSums(dframe[,rels]^2)) - apply(dframe[,rels],1,max))
}

NormSQRT <- function(dframe){
  return(sqrt(rowSums(dframe[,all]^2)) / rowSums(dframe[,all]))
}

NormR <- function(dframe){
    return(sqrt(rowSums(dframe[,rels]^2)) / rowSums(dframe[,rels]))
}

NormRAll <- function(dframe){
  return(sqrt(rowSums(dframe[,rels]^2)) / rowSums(dframe[,all]))    
}

numSentences <- function(dframe){
  #return (as.vector(rowSums(rbind(table(dframe$worker_id, dframe$unit_id)))))
  agr <- aggregate(dframe,by=list(dframe$worker_id),FUN=length)
  return(agr[,2])
}

numAnnotations <- function(dframe){
  filtTable <- pivot(dframe,'worker_id','relation')
  filtDf <- getDf(filtTable)
  return(as.vector(rowSums(filtDf)))
}

agreement <- function(dframe){

  # Sentence Matrix => contains the vector sentences for each worker. 
  
  sentMat <- list()

  worker_ids <- unique(dframe$worker_id)
  
  for (worker_id in worker_ids){
    sentMat[[as.character(worker_id)]] <- getSentenceMatrix(dframe, worker_id)
  }
  return (unlist(lapply(worker_ids, workerAgreement, raw_data=dframe, sentMat=sentMat)))
}

workerAgreement <- function(worker_id, raw_data, sentMat) {
  
  sentences <- unique(raw_data[raw_data$worker_id == worker_id,]$unit_id)

  coworkers <- unique(raw_data[raw_data$unit_id %in% sentences,]$worker_id)

  #exclude the worker_id
  #coworkers <- setdiff(unique(coworkers),c(worker_id))
  
  weightedSum <- 0
  weightedCount <- 0
  
  for (coworker in coworkers){
    
    hitCount <- 0
    annotCount <- 0
    
    #sentInCommon <- sentencesInCommon[worker_id, coworker]
    sm1 <- sentMat[[as.character(worker_id)]]
    sm2 <- sentMat[[as.character(coworker)]]   
    
    sentInCommon <- intersect(rownames(sm1), rownames(sm2))        

    for (sent in sentInCommon){
      v1 <- sm1[as.character(sent),]
      v2 <- sm2[as.character(sent),]
       
      hitCount <- hitCount + rowSums(v1 & v2)   
      annotCount <- annotCount + rowSums(v1)         
    }


    if(annotCount > 0){
      weightedSum <- weightedSum + length(sentInCommon) * (hitCount / annotCount)
    } 

    weightedCount <- weightedCount + length(sentInCommon)        
  }
  return (weightedSum[[1]] / weightedCount)
}

workerCosine <- function(worker_id, dframe){
  
  workerSentences <- dframe[dframe$worker_id == worker_id,]$unit_id

  sumCos <- 0 
    
  for (sentence_id in workerSentences){

    workerSV <- getSentenceVector(dframe,sentence_id, worker_id)
    sentVector <- getSentenceVector(dframe, sentence_id)
  
    restVector <- (sentVector - workerSV)

    sumCos <- sumCos + cosine( as.vector(t(restVector)), as.vector(t(workerSV)))
  }

  return (1 - (sumCos / length(workerSentences)))

}

cosMeasure <- function(dframe){
  
  worker_ids <- unique(dframe$worker_id)  
  return (unlist(lapply(worker_ids, workerCosine, dframe=dframe)))
}










