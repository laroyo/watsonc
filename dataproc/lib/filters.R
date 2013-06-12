# Returns the ids of the rows that have a value < factor for the column 'field'
belowFactor <- function(dframe,field, factor){
  return (rownames(dframe[dframe[[field]] < factor,,drop=FALSE]))
}

overFactor <- function(dframe,field, factor){
  return (rownames(dframe[dframe[[field]] > factor,,drop=FALSE]))
}

belowDiff <- function(dframe,field){
  mFactor <- mean(dframe[[field]]) - sd(dframe[[field]])
  return(belowFactor(dframe,field,mFactor))
}

overDiff <- function(dframe,field){
  mFactor <- mean(dframe[[field]]) + sd(dframe[[field]])
  return(overFactor(dframe,field,mFactor))
}

filterOutliers <- function(tVector, expCompletionTime){

  numElem <- length(tVector)
  
  upBound <- expCompletionTime * 3
  lowBound <- expCompletionTime / 3
  
  lowFiltered <- length(tVector[tVector < lowBound])
  upFiltered <- length(tVector[tVector > upBound])
  
  times <- tVector[tVector < upBound & tVector > lowBound]   
  
  return (list(lowFiltered, upFiltered, times))
  
}
containsRelation <- function(text){

  relations <- c("NONE","PART?OF","MANIFESTATION","ASSOCIATED?WITH","CAUSES","OTHER","SIDE?EFFECT","TREATS","LOCATION","DIAGNOSE?BY?TEST?OR?DRUG","SYMPTOM","IS?A","PREVENTS","CONTRAINDICATES")
  for (r in relations){
    if(length(grep(r,text, ignore.case=TRUE)) > 0)
      return(TRUE)      
  }  
  return(FALSE)
}

#Verifies whether a word has synsets in wordnet
hasSyn <- function(word){  
  return(system(sprintf('wn %s -synsn',word),ignore.stdout=TRUE) > 0)                                        
}

#Auxiliary function to validWords Filter.
#Verifies whether there's a valid word in 'text'
validateWords <- function(text,sentence){

  if(!is.null(sentence))
    sent.words <- strsplit(sentence, ' +')[[1]]
  
  for (ch in c('\\[','\\]','-','_','\\.',',','\\n', '\\(','\\)', '\'', '\"', '&')){
    text <- gsub(ch,' ',text,ignore.case=TRUE)    
  }
  #Trim (initial/final white spaces not replaced by strsplit)
  text <- gsub("(^ +)|( +$)", "", text)
  
  text.words <- strsplit(text, ' +')[[1]]

  i = 1
  found <- FALSE

  while(!found && (i <= length(text.words))){
    if(length(grep('\\(',text.words[i])) > 0){
      print(paste('word: ',text.words[i]))
      stop('err')
    }
    if(length(grep('\'',text.words[i]))>0){
      print(text.words[i])
      i = i+1 
      next
    }
    if(nchar(text.words[i])>0)      
      found <- hasSyn(text.words[i])    
    
    if(!found && (length(grep(text.words[i],sentence,ignore.case=TRUE))>0)){      
      found <- TRUE
    } else {
      i = i+1
    }
  }
  
  return (found)  
}

# At least one valid word (i.e. existing in wordnet, present in the sentence or a relation) has been used.
validWords <- function(dframe,field='explanation'){

  worker.ids <- NULL
  
  for (i in seq(1,dim(dframe)[1])) {    
    r <- dframe[i,]
    if(r[[field]] == 'N/A' || nchar(r[[field]])==0)
      next
    
    text <- r[[field]]
    sentence <- r$sentence

    t <- as.numeric(Sys.time())
    if(!validateWords(text,sentence) && !containsRelation(text)){
      #worker_ids <- c(worker_ids, r$worker_id)
      worker.ids <- rbind(worker.ids,data.frame(worker_id = r$worker_id, field = r[[field]]))      
    }
    #print(paste('loop time: ',(as.numeric(Sys.time()) - t), sep=''))
  }                                        
  return (worker.ids)
}
#Check whether other relations are used together with [NONE] or [OTHER]    
noneOther <- function(dframe){
  
  worker_ids <- c()
  
  for (i in seq(1,dim(dframe)[1])) {
    r <- dframe[i,]
    relations <- strsplit(r$relation, '\n')[[1]]
    if (length(relations > 1))
      worker_ids <- c(worker_ids, r$worker_id)        
  }
  return (sort(unique(worker_ids)))
}

#Check if the same text is used for filling the "explanations" and selected words textfields. 
repeatedResponse <- function(dframe){

  worker_ids <- c()
  
  for (i in seq(1,dim(dframe)[1])) {
    r <- res[i,]
    
    if(r$explanation != 'N/A' && r$explanation != '')
      if(r$explanation == r$selected_words)
        worker_ids <- c(worker_ids, r$worker_id)        
  }
  return (sort(unique(worker_ids)))
}

#Workers that use the same explanation and / or selected_words across all their tasks.
#By default, it is "explanation" or "selected_words", but it can be refined to filter only one of both. 
repeatedText <- function(set_id,field='both'){
  if(!(field %in% c('selected_words','explanation','both'))){
    stop(paste('Invalid field type: ',field,'. Accepted values: [selected_words, explanation,both]',sep=''))
  }
  
  if(field == 'both'){ 
    selWords <- repeatedText(set_id,'selected_words')
    explanations <- repeatedText(set_id,'explanation')
    return(union(selWords, explanations))
  } else {    
    onetype <- sprintf("select distinct(worker_id) from cflower_results where job_id = %s group by worker_id having count(distinct(%s))=1",set_id, field)
    repeated_words <- sprintf("select distinct(worker_id) from cflower_results where job_id = %s and %s != '' and %s != 'N/A' and %s != 'na' and %s != 'none'
           group by %s,worker_id having count(*) > 1 order by count(*) desc", set_id, field, field,field,field,field, field,field)
    otype <- dbGetQuery(con,onetype)
    rwords <- dbGetQuery(con,repeated_words)
    return(intersect(otype$worker_id, rwords$worker_id))
  }         
}



