#Contains functions to modify and preprocess data structures. 

if(!exists('rels')){
rels <- c('D','S','C','M','L','AW','P','SE','IA','PO','T','CI')
othnone <- c('OTH','NONE')
all <- c(rels,othnone)

  lockBinding("rels",globalenv())
  lockBinding("all",globalenv())
}


# Create a Data frame from a contingency table. 
getDf <- function(table,firstID=FALSE){
  df <- as.data.frame(rbind(table))

  #Use the first column as the row identifier.
  if(firstID){
    rownames(df) <- df[,1]
    df <- df[,2:ncol(df)]
  }

  #Replace col names by abbreviated keys (for readability). 
  return(labeldf(df))
}

colselect <- function(data,field,pfield){

  if(length(dim(data)) != 2 || dim(data)[1] <= 0 || dim(data)[2] <= 0){
    stop(sprintf("Error: invalid data frame. Dimensions: (%i,%i)",dim(data)[1],dim(data)[2]))
  }
  
  if (! field %in% names(data)){
    stop(sprintf("Error: %s is not a valid field of the data.frame",field))
  }
  
  if(!pfield %in% names(data)){
    stop(sprintf("Error: %s is not a valid field of the data.frame",pfield))
  }

  return(data[,c(field,pfield)])  
}

labeldf <- function(dframe){
  colnames(dframe) <- unlist(lapply(colnames(dframe),abcol))
  if(length(all[!(all %in% names(dframe))])){
     #add missing (empty) columns
    for (col in all[!(all %in% names(dframe))]){
      dframe[[col]] <- c(0)
      #print(paste('Warn: column ',col,' not found, will be considered empty. Dataset may be incomplete'))
    }
  }
  #Order the columns (OTH and NONE at the end)
  return(dframe[,all])
}

abcol <- function(key){
  
  k1 <- list('[CAUSES]'='C','[SYMPTOM]'='S','[LOCATION]'='L','[PREVENTS]'='P','[DIAGNOSE_BY_TEST_OR_DRUG]'='D','[MANIFESTATION]'='M',
             '[ASSOCIATED_WITH]'='AW','[PART_OF]'='PO','[OTHER]'='OTH','[TREATS]'='T','[NONE]'='NONE','[IS_A]'='IA','[SIDE_EFFECT]'='SE',
             '[CONTRAINDICATES]'='CI','[DIAGNOSED_BY_TEST_OR_DRUG]'='D')
  k2 <- list('causes'='C','symptom'='S','location'='L','prevents'='P','diagnosed_by_test_or_drug'='D','manifestation'='M','associated_with'='AW','part_of'='PO','other'='OTH','treats'='T','none'='NONE','is_a'='IA','side_effect'='SE',
             'contraindicates'='CI')

  if(key %in% names(k1)){
    return(k1[as.character(key)][[1]])
  } else if(key %in% names(k2)){
    return(k2[as.character(key)][[1]])
  } else {
    stop(paste('Error: column not found or incorrect format: ',as.character(key)))
  }
}

simplify <- function(data,field,mField) {

  allOptions <- unique(data[[mField]])
    
  mulOptions <- grep("\n",allOptions)
  simpOptions <- setdiff(c(1:length(unique(data[[mField]]))),mulOptions)


  #df with the values for multOptions
  #To be decomposed and the sum to sr.
  mr <- data[data[[mField]] %in% allOptions[mulOptions],,drop=FALSE]

  #Express the mr dframe as a contingency table.
 
  mrdf <- as.data.frame(table(mr))
  mrdf <- mrdf[mrdf$Freq > 0,]

  mulrels <- unique(mrdf$relation)

  splitlabels <- strsplit(as.character(mulrels),'\n')

  srels <- unique(union(allOptions[simpOptions],unlist(splitlabels)))

  mm <- matrix(0,length(rownames(table(data))),length(srels))
  mult <- as.table(mm)
  rownames(mult) <- rownames(table(data))
  colnames(mult) <- srels

  sr <- mult

  #df with the values for simpleOptions
  #if(length(simpOptions) < length(srels)){
  #DataFrame with *only* the simple relations
  sred <- table(data)[,allOptions[simpOptions],drop=FALSE]
  for(rel in allOptions[simpOptions]){
    sr[,rel] <- sred[,rel]
  }    
  #}
  
  #rels <- unique(merge(unlist(splitlabels))
  if(length(mulrels) > 0){
    for (i in 1:length(mulrels)){
      
      rowset <- mrdf[mrdf$relation==mulrels[i],]
      for(j in 1:dim(rowset)[1]){

        row = rowset[j,];
        
        for(l in splitlabels[i][[1]]){
          tryCatch({
            mult[as.character(row[[field]]),l] <- mult[as.character(row[[field]]),l] + row$Freq
          }, error = function(e){
            print('error aquí')          
            ## print(row[[field]])
            ## print(l)
            ## print(colnames(mult))
            ##res <- mult[as.character(row[[field]]),l]
            ##print(rownames(mult))
            ##print(res)
            ## print(row)
            ## print(l)
          });           
        }
      }
    }
  }
  #print(mult)
  return (sr + mult)
}

#data: raw_data (as read from the csv file)
#pfield: pivot field (worker_id, unit_id).
#field: the other field in the contingency table (for the moment, only 'relation')
pivot <-function(data,pfield,field){
  data <- colselect(data,pfield,field)
  sentenceTable <- simplify(data,pfield,field)
  if(job.id == 132248 || job.id == 132763 || job.id == 134491 || job.id == 139057){
    
    equ <- list('sign_or_symptom_of' = '[SYMPTOM]', 
                'may_cause'='[CAUSES]', 
                'diagnosed_by'='[DIAGNOSE_BY_TEST_OR_DRUG]',
                'DIAGNOSED_BY_TEST_OR_DRUG'='[DIAGNOSE_BY_TEST_OR_DRUG]',
                '[DIAGNOSED_BY_TEST_OR_DRUG]'='[DIAGNOSE_BY_TEST_OR_DRUG]',
                'DIAGNOSIS_TEST_OR_DRUG'='[DIAGNOSE_BY_TEST_OR_DRUG]',
                '[DIAGNOSIS_TEST_OR_DRUG]'='[DIAGNOSE_BY_TEST_OR_DRUG]',
                'treated_by'='[TREATS]',
                '[TREATED_BY]'='[TREATS]', 
                'location_of'='[LOCATION]',
                '[HAS_LOCATION]'='[LOCATION]',
                'LOCATION_OF'='[LOCATION]',
                '[LOCATION_OF]'='[LOCATION]',
                'may_diagnose'='[DIAGNOSE_BY_TEST_OR_DRUG]',
                'cause_of'='[CAUSES]',
                'causes'='[CAUSES]',
                'CAUSES'='[CAUSES]',
                'has_manifestation'='[MANIFESTATION]',
                'prevented_by'='[PREVENTS]',
                'treats'='[TREATS]',
                'TREATS'='[TREATS]',
                'TREATED_BY'='[TREATS]',
                'may_treat'='[TREATS]',
                'diagnose'= '[DIAGNOSE_BY_TEST_OR_DRUG]',
                'prevents'='[PREVENTS]',
                'PREVENTS'='[PREVENTS]',
                'PREVENTED_BY'='[PREVENTS]',
                '[PREVENTED_BY]'='[PREVENTS]',
                "may_prevent"='[PREVENTS]',
                '[OTHER]'='[OTHER]',
                '[NONE]'='[NONE]',
                'symptom_of'='[SYMPTOM]',
                'SYMPTOM_OF'='[SYMPTOM]',
                '[SYMPTOM_OF]'='[SYMPTOM]',
                'HAS_SYMPTOM'='[SYMPTOM]',
                '[HAS_SYMPTOM]'='[SYMPTOM]',
                'MANIFESTATION_OF'='[MANIFESTATION]',
                '[MANIFESTATION_OF]'='[MANIFESTATION]',
                'HAS_MANIFESTATION'='[MANIFESTATION]',
                '[HAS_MANIFESTATION]'='[MANIFESTATION]',
                'CAUSED_BY'='[CAUSES]',
                '[CAUSED_BY]'='[CAUSES]'                               
                )
    
    
    relations <- c('[CAUSES]','[SYMPTOM]','[LOCATION]','[PREVENTS]','[DIAGNOSE_BY_TEST_OR_DRUG]','[MANIFESTATION]',
                   '[ASSOCIATED_WITH]','[PART_OF]','[OTHER]','[TREATS]','[NONE]','[IS_A]','[SIDE_EFFECT]',
                   '[CONTRAINDICATES]')
    
    df <- as.data.frame(matrix(data=0,nrow=dim(sentenceTable)[1],ncol = 14))
    colnames(df) <- relations
    rownames(df) <- rownames(sentenceTable)
    
    mcols <- colnames(sentenceTable)
    for(mcol in mcols){
      if (mcol != 'disease_has_finding' && mcol != 'desease_has_finding' && mcol != '[HA'){
        for(row.id in rownames(sentenceTable)) {
          if(mcol %in% relations){
            df[row.id, mcol] <- (df[row.id,mcol] +  sentenceTable[row.id,mcol])
          } else  {
            df[row.id, equ[[mcol]]] <- (df[row.id,equ[[mcol]]] +  sentenceTable[row.id,mcol])
          }
        }
      }        
    }
    return(df)
  } else {
    return(sentenceTable)
  }
}


getSentenceVector <- function(raw_data,unit_id,worker_id=NULL){
  if(is.null(worker_id)){
    annotations <- raw_data[raw_data$unit_id == unit_id,]
  } else {
    annotations <- raw_data[raw_data$worker_id == worker_id & raw_data$unit_id == unit_id,]
  }

  sentTable <- pivot(annotations,'unit_id','relation')
  return(getDf(sentTable))      
}

# Used for testing, mainly, may be useful sometime. 
createSentenceVector <- function(unit_id, keys, values){
  df <- as.data.frame(t(values))
  rownames(df) <- unit_id
  colnames(df) <- keys
  return (df)  
}

#Matrix (dataframe) with the annotations of a worker
getSentenceMatrix <- function(raw_data,worker_id){
  annotations <- raw_data[raw_data$worker_id == worker_id,]
  sentTable <- pivot(annotations,'unit_id','relation')
  return(getDf(sentTable))      
}

#Data frame with the individual annotations of the workers. 

getSentenceAnnotations <- function(raw_data,unit_id){

  worker_ids <- getSentenceAnnotators(raw_data, unit_id)
  if(length(worker_ids) > 1){
    df <- getSentenceVector(raw_data,unit_id,worker_ids[1])
    row.names(df) <- worker_ids[1]    
    
    for(worker_id in worker_ids[2:length(worker_ids)]){      
      sentVector <- getSentenceVector(raw_data,unit_id,worker_id)
      row.names(sentVector) <- worker_id
      df <- rbind(df, sentVector)      
    }
    return(df)
  }
  if(length(worker_ids) == 1){
    sentVector <- getSentenceVector(raw_data,unit_id,worker_ids[1])
    row.names(sentVector) <- worker_ids[1]    
    return (sentVector)
  }  
}

getSentenceAnnotators <- function(raw_data, unit_id){
  annotations <- raw_data[raw_data$unit_id == unit_id,]
  return (sort(unique(annotations$worker_id))); 
}

#The unitary vector for a relation in a sentence. 
getRelationVector <- function(sentVect,relation){

  mm <- matrix(0,1,length(colnames(sentVect)))
  vect <- as.table(mm)  
  rownames(vect) <- rownames(sentVect)
  colnames(vect) <- colnames(sentVect)
  
  vect[,relation] <- sentVect[,relation]
  return(vect)
}

#Vector with the annotators of a sentences
getAnnotators <- function(data,unit_id){
  return(raw_data[raw_data$unit_id == unit_id,]$worker_id)
}

sentMat <- function(raw_data, coworkers) {
  sentMat <- list()
  for (worker_id in coworkers){
    sentMat[[as.character(worker_id)]] <- getSentenceMatrix(raw_data, 216)
  }
}
# Relation co-occurence table. 
getRelCoOccur <- function(raw_data){
 
  relations <- raw_data$relation
  
  mulRelations <- relations[grep("\n",relations)]
  #mulTable <- table(all,all)
  mulTable <- as.table(matrix(0,nrow=length(all),ncol=length(all),dimnames=list(all,all)))
    
  splitted <- lapply(lapply(mulRelations,strsplit, "\n"),unlist)

  #To apply abcol to each of the splitted elements
  simplRel <- function(elem){
    unlist(lapply(elem, abcol))
  }

  getPairs <- function(elem){    
    perm <- permutations(length(elem),2,elem)
    return (lapply(seq_len(nrow(perm)), function(i) perm[i,]))
  }

  relabeled <- lapply(splitted, simplRel)
  pairs <- lapply(relabeled,getPairs)
  
  for (p in pairs) {
    for (tuple in p) {
      mulTable[tuple[1],tuple[2]] =  mulTable[tuple[1],tuple[2]] + 1      
    }
  }
  return (mulTable)
}

setToOne <- function(sentenceDf){

  df <- as.data.frame(matrix(0, nrow=dim(sentenceDf)[1],ncol=length(all),dimnames=list(rownames(sentenceDf),all)))
  for(i in (seq(1, dim(sentenceDf)[1]))){
    for(j in (seq(1,dim(sentenceDf)[2]))){
      if(sentenceDf[i,j] > 1){
        df[i,j] <- 1        
      } else {
        df[i,j] <- 0
      }
    }
  }
  return (df)
}

zeroDiagonal <- function(df){
  for(i in seq(1,dim(df)[1])){
    df[i,i] <- 0
  }
  return(df)         
}

correctMisspells <- function(text){
  #Accepted alternative ways to write N/A
  misspells <- c('na', 'n/a', 'N/a', 'NA','N.a','naa', 'Na')
  
  if(text %in% misspells){
    text <- 'N/A'    
  }
  return(text)
}


#Returns the sentences of sentRelDf where the relation is the one chosen by the majority
getMajSentences <- function(sentRelDf, relation){  
  res <- c()
  for (i in seq(1,dim(sentRelDf)[1])){
    if(max(sentRelDf[i,]) == sentRelDf[i,relation]){
      res <- c(res, rownames(sentRelDf)[i])
    }
  }
  return (res)
}

#Returns the majoritary relation(s) for a sentence
majRelation <- function(sentence){ 
  max.value <- max(sentence)
  sent <- unlist(sentence)
  return (row.names(as.data.frame((sent[sent==max.value]))))
}

#Transforms an element into a sentence Vector
#Mult indicates wheter multiple relations are considered.
#By default (FALSE), multiple relations are ignored. 
#Applied in conjuntion with majRelation and getRelationVector
toSentenceVector <- function(list.elem,mult=FALSE){

  sv <- createSentenceVector(1,all, rep(0,14)) 
  if(length(list.elem) > 1){
    if(mult)
      for(elem in list.elem)
        sv[elem] = 1
  } else
      sv[list.elem] = 1
  return (sv)
}


#R lists are encoded in JSON as objects where each attribute is an element of a list
#This function encodes the list as an array of json objects. 
getListAsArray <- function(mlist,key.name,value.name){
  json <- '['
  for(n in names(mlist)){
    elem <- paste('{"',key.name,'":"',n,'", "',value.name,'" : ',toJSON(mlist[[n]]),'}', sep='')
    if(nchar(json) == 1)
      separator = ''
    else
      separator = ','
    
    json <- paste(json, elem,sep=separator)
  }
  json <- paste(json,']',sep='')
  
  return (json)  
}

addChangData <- function(sentenceDf,chang.data,no.ids=FALSE){
  #Create empty data frame. 
  mDf <- as.data.frame(matrix(nrow=1,ncol=24,dimnames=list(0,c('unit_id','chang_id',all,'relation_type','term1','b1','e1','term2','b2','e2','sentence'))))
  mDf <- mDf[0,]
  
  for(r in seq(2:dim(sentenceDf)[1])){        
    
    sent.id <- as.numeric(row.names(sentenceDf[r, ]))
    if(no.ids){
      chang.id <- sentenceDf[r,]$chang.id
      unit.id.id <- sentenceDf[r,]$unit.id
    } else {
    
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
    }
    
    ## if(unit.id  > 200000000)
    ##   chang.id <- equivalences[equivalences$unit_id == unit.id,][1,1]
    ## else
    ##   chang.id <- unit.id
                                        #sentenceDf$unit_id <- row.names(sentenceDf)
      
    if(!is.na(chang.id) && chang.id != 0) {
      if(no.ids){
        row <- cbind(sentenceDf[r,], chang.data[chang.data$chang_id== chang.id,c('relation_type','term1','b1','e1','term2','b2','e2','sentence')])      
      } else {
        row <- cbind(unit.id=as.character(unit.id),chang.id,sentenceDf[r,], chang.data[chang.data$chang_id== chang.id,c('relation_type','term1','b1','e1','term2','b2','e2','sentence')])      
      }
      mDf <- rbind(mDf,row)
    }    
  }
  return (mDf)
}



