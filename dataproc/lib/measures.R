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
  return (data.frame(row.names=agr[,1],numSent=agr[,2]))    
  #return(agr[,2])
}

numAnnotations <- function(dframe){
  filtTable <- pivot(dframe,'worker_id','relation')
  filtDf <- getDf(filtTable)
  res <- rowSums(filtDf)
  return (data.frame(row.names=names(res),numAnnot=res))  
}

agreement <- function(dframe){

  # Sentence Matrix => contains the vector sentences for each worker.   
  sentMat <- list()

  worker_ids <- sort(unique(dframe$worker_id))
  
  for (worker_id in worker_ids){
    sentMat[[as.character(worker_id)]] <- getSentenceMatrix(dframe, worker_id)
  }
  values <- (unlist(lapply(worker_ids, workerAgreement, raw_data=dframe, sentMat=sentMat)))
  return (data.frame(row.names = worker_ids, agr=values))
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
  values <- (unlist(lapply(worker_ids, workerCosine, dframe=dframe)))
  return (data.frame(row.names = worker_ids, cos=values))
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
  
  numLabelsMul <- sum(rowSums(mulTable))/2
  numLabelsSimp <- sum(simpTable)
  numLabels <- numLabelsMul + numLabelsSimp

  probIndiv <- simpTable / numLabels
  probInter <- mulTable / numLabels

  probTable <- as.table(matrix(0,nrow=length(all),ncol=length(all),dimnames=list(all,all)))
  
  for (i  in 1:dim(mulTable)[1]){
    for (j  in i:dim(mulTable)[1]){
      if(i != j){
        probTable[i,j] <- as.vector(probInter[j,i]) / as.vector(probIndiv)[i]
        probTable[j,i] <- as.vector(probInter[i,j]) / as.vector(probIndiv)[j]
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

#Cosine similarity for worker_id and unit_id
#WARNING: when more than two labels are used, cosine is averaged
#(used for workerSentenceScore, may not make sense for every scenario). 

workerSentenceCos <- function(raw_data, unit_id,worker_id){
  if(dim(raw_data[raw_data$worker_id == worker_id & raw_data$unit_id == unit_id,])[1] >0){
    workerVector <- getSentenceVector(raw_data,unit_id,worker_id)
    sentVector <- getSentenceVector(raw_data,unit_id)
    restVector <- sentVector - workerVector
    
     #If worker has used multiple lables, and several of the coincide with the rest
    if(rowSums(restVector & workerVector)>1){
      scoreVector <- createSentenceVector(unit_id,all,rep(NA,length(all)))            
      
      for (relation in colnames(workerVector[,workerVector>0])){
        
        unitVector <- createSentenceVector(unit_id,all,rep(0,length(all)))
        unitVector[,relation] <- 1
        cos <- cosine(as.vector(t(unitVector)), as.vector(t(restVector)))[1,1]
        scoreVector[,relation] <- cosine(as.vector(t(unitVector)), as.vector(t(restVector)))[1,1]        
      }
      return(rowMeans(scoreVector,na.rm=TRUE)[[1]])      
    } else { 
      return (cosine(as.vector(t(workerVector)), as.vector(t(restVector)))[1,1])
    }    
  } else {
    return(NA)
  }
}

workerSentenceCosTable <- function(raw_data){

  worker_ids <- sort(unique(raw_data$worker_id))
  unit_ids <- sort(unique(raw_data$unit_id))
  
  cosTable <- as.data.frame(matrix(NA,nrow=length(worker_ids),ncol=length(unit_ids),dimnames=list(worker_ids,unit_ids)))

  for (i in worker_ids){
    for (j in unit_ids){
      if(dim(raw_data[raw_data$worker_id == i & raw_data$unit_id == j,])[1] >0){       
        cosTable[as.character(i),as.character(j)] <- workerSentenceCos(raw_data, j,i)
      } else {
        cosTable[as.character(i),as.character(j)] <- NA
      }
    }
  }
  return (cosTable)
}

# Worker-Sentence score metric
# @param SentenceClarity and a table with the cosine similarity for workers and unit_ids (from workerSentenceCosTable function)
workerSentenceScoreTable <- function(raw_data,workerSentCosTable,sentClarity){

  worker_ids <- sort(unique(raw_data$worker_id))
  unit_ids <- sort(unique(raw_data$unit_id))
  
  scoreTable <- as.data.frame(matrix(NA,nrow=length(worker_ids),ncol=length(unit_ids),dimnames=list(worker_ids,unit_ids)))
    
  for (i in worker_ids){
    for (j in unit_ids){
      if(dim(raw_data[raw_data$worker_id == i & raw_data$unit_id == j,])[1] >0){
        scoreTable[as.character(i),as.character(j)] <- sentClarity[[as.character(j)]] - workerSentCosTable[as.character(i),as.character(j)]
     } else {
        scoreTable[as.character(i),as.character(j)] <- NA
      }
    }
  }
  return (scoreTable)
}

#These functions are tentative metrics (discussed with Chris).
# Not yet ready for prime time, need revision. 

#Measures relation coOccurence for each of the sentenceVector (aggregated annotations).
#For relation CoOccurence in worker Vectors see simplify/getRelCoOccur. 
aggRelCoOccurence <- function(sentenceDf){

  df <- as.data.frame(matrix(0, nrow=length(all),ncol=length(all),dimnames=list(all,all)))
  
  for (i in seq(1,length(all))){
    for (j in seq(i, length(all))){
      if(i != j){   
        df[i,j] <- (dim(sentenceDf[sentenceDf[all[i]] & sentenceDf[all[j]],])[1])
        df[j,i] <- df[i,j]
      }
    }
  }
  return (df)
}

#Correlation between the relations of a job.
#Returns a matrix of correlations Corr(Rel_x,Rel_y)
relationsCorrelation <- function(sentenceDf){

  df <- as.data.frame(matrix(0, nrow=length(all),ncol=length(all),dimnames=list(all,all)))
  
  for (i in seq(1,length(all))){
    for (j in seq(i, length(all))){
      if(i != j){
        df[i,j] <- cor(sentenceDf[all[i]],sentenceDf[all[j]])                
        df[j,i] <- df[i,j]
      }
    }
  }
  return (df)
}


