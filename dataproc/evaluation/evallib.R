#Auxiliary functions for metric evaluation.

sampleWorkers <- function (raw.data,sel){

  # Draws several samples of sentences (sample size: 3, 6, 9, 12, 0.75 and max) for each worker in raw.data according to a certain selection criterion (sel).
  #
  # Args:
  #   raw.data :
  #   sel: selection criterion. 1 = random, 2 == sentenceClarity >= 0.6, 3 == sentenceClarity < 0.6
  
  #
  # Returns:
  #   s: a list where, for each worker returns several lists, each one of them containing a sample of $num.elements sentences that have been annotated by the worker
  #   and meet the specified sampling condition, if any (sentenceClarity).  If there are no sentences that meet the conditions, returns an empty list in that pos. 
  
  worker.ids <- sort(unique(raw.data$worker_id))
  s <- list()
  
  for (worker.id in worker.ids){
    
    sentMat <- getSentenceMatrix(raw.data, worker.id)
    worker.sents <- row.names(sentMat)
    
    if(sel == 1 ){
      cand.sents <- worker.sents
    }
    
    if(sel == 2){
      # Select clear sentences (sentClarity >= 0.6)
      cand.sents <- intersect(
                              rownames(as.data.frame(sentClarity[sentClarity >= 0.6])),
                              rownames(as.data.frame(sentClarity[worker.sents]))
                              )    
    }
    if(sel == 3){    
      #Select unclear sentences (sentClarity < 0.5).
      cand.sents <- intersect(
                              rownames(as.data.frame(sentClarity[sentClarity < 0.6])),
                              rownames(as.data.frame(sentClarity[worker.sents]))
                              )    
    }
    
    num.sent <- c(3,6,9,12)
    
    num.cand.sents <- length(cand.sents)
    
    s[[as.character(worker.id)]] <- list()
    for (i in seq(1:length(num.sent))){
      ns <- num.sent[i]
      
      if(ns <= num.cand.sents){
      s[[as.character(worker.id)]][[i]] <- sample(cand.sents, ns)
    } else {
      s[[as.character(worker.id)]][[i]] <- list()
    }      
    }
    
    if(num.cand.sents > 15){
      if(round(num.cand.sents * 0.75) >= 20){ # this implies that worker.annot >= 27        
                                        #Two additional  amples, one 0.75, bigger than 20 and another one for the max value.
        s[[as.character(worker.id)]][[length(num.sent) + 1 ]] <- sample(cand.sents, round(num.cand.sents * 0.75))
        s[[as.character(worker.id)]][[length(num.sent) + 2 ]] <- cand.sents
      } else {
                                        #Only one additional sample, for the max == using all the sentences.
        s[[as.character(worker.id)]][[length(num.sent) +1 ]] <- cand.sents
        s[[as.character(worker.id)]][[length(num.sent) +2]] <- list()
      }
    } else {
      s[[as.character(worker.id)]][[length(num.sent) + 1]] <- list()
      s[[as.character(worker.id)]][[length(num.sent) + 2]] <- list()
    }         
  }
  return(s)
}

compWorkerMetrics <- function(s){

  # Computes the worker metrics for a sample of sentences. 
  #
  # Args:
  #   s: Sample of sentences for whiche the worker metrics are to be computed. 
  #
  # Returns:
  #   m: a list with each of the worker metric (cos, agr, annotSent) for each of the workers in the sample. 

  m <- list()
  sentMat <- list()
  
  for(worker.id in names(s)){
    key <- as.character(worker.id)
    m[[key]] <- list()
    for(i in seq(1:length(s[[key]]))){
      m[[key]][[i]] <- list()
      
      if(length(s[[key]][[i]]) > 0){        
        
        unit.ids <- s[[key]][[i]]

        data <- raw.data[raw.data$unit_id %in% unit.ids,]
        
        cos <- workerCosine(worker.id,data)[1]
        m[[key]][[i]][['cos']] <- cos
        
        sentMat[[as.character(worker.id)]] <- getSentenceMatrix(raw.data, worker.id)
        
        coworkers <- unique(raw.data[raw.data$unit_id %in% unit.ids,]$worker_id)

        for(worker.id in coworkers){
          sentMat[[as.character(worker.id)]] <- getSentenceMatrix(raw.data, worker.id)
        }
        
        agr <- workerAgreement(worker.id, data,sentMat)
        m[[key]][[i]][['agr']] <- agr

        numAnnot <- numAnnotations(data)
        numSent <- numSentences(data)
        annotSent <- (numAnnot / numSent)[key,]
        m[[key]][[i]][['annotSent']] <- annotSent

      }
    }
  }
  return(m)
}
