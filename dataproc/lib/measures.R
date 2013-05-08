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

#numberOfSentences per worker. 
numSentences <- function(dframe){
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

  worker_ids <- sort(unique(dframe$worker_id))
  
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

  return (sumCos / length(workerSentences))

}

cosMeasure <- function(dframe){
  
  worker_ids <- sort(unique(dframe$worker_id))
  return (unlist(lapply(worker_ids, workerCosine, dframe=dframe)))
}

sentRelationScore <- function(unit_id, dframe){

  sentVector <- getSentenceVector(dframe,unit_id)

  scoreVector <- createSentenceVector(unit_id,all,rep(0,length(all)))
  
  for (relation in all){
    if(sentVector[,relation] > 0){
      unitVector <- createSentenceVector(unit_id,all,rep(0,length(all)))
      unitVector[,relation] <- 1
      scoreVector[,relation] <- cosine(as.vector(t(unitVector)), as.vector(t(sentVector)))[1,1]
    } else {
      scoreVector[,relation] <- 0
    }        
  }
  return(scoreVector)
}

sentClarity <- function(unit_id, dframe){
  scoreVector <- sentRelationScore(unit_id, dframe)
  return(max(scoreVector))
}

sentenceClarity <- function(sentRelDf){
  return (apply(sentRelDf,1,max))
}

sentRelScoreMeasure <- function(dframe){
  df <- as.data.frame(matrix(nrow=0,ncol=14,dimnames=list(c(),all)))
  unit_ids <- sort(unique(dframe$unit_id))
  for (unit_id in unit_ids){
    vector <- sentRelationScore(unit_id, dframe)
    df <- rbind(df,vector)
  }
  return (df)
}

relationSimilarity <- function(raw_data) {
  
  mulTable <- getRelCoOccur(raw_data)  

  relations <- raw_data$relation
  sinRelation <- relations[-grep("\n",relations)]
  simpTable <- table(unlist(lapply(sinRelation,abcol)))
  
  numLabelsMul <- sum(rowSums(mulTable))  /2
  numLabelsSimp <- sum(simpTable)
  numLabels <- numLabelsMul + numLabelsSimp

  probIndiv <- simpTable / numLabels
  probInter <- mulTable / numLabels

  probTable <- as.table(matrix(0,nrow=length(all),ncol=length(all),dimnames=list(all,all)))
  
  for (i  in 1:dim(mulTable)[1]){
    for (j  in i:dim(mulTable)[1]){
      if(i != j){
        probTable[i,j] <- (as.vector(probIndiv)[j] * probInter[i,j]) / as.vector(probIndiv)[i]
        probTable[j,i] <- (as.vector(probIndiv)[i] * probInter[j,i]) / as.vector(probIndiv)[j]
      } else {
        probTable[i,j] <- 0
      }
    }
  }
  return (probTable)
}

#Returns a 1-row matrix with the relation Clarity values for each relation. 
relationAmbiguity <- function(probTable){

  ambiguity = matrix(nrow = 1, ncol=length(all), dimnames=list(c('Rel Ambiguity'), all))

  for (r in all){
    ambiguity[1,r] <- max(probTable[r,])    
  }
  return (ambiguity)
}

relationClarity <- function(sentRelDf){
  return (rapply(sentRelDf,max))
}




