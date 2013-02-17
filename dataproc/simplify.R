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
      print(paste('Warn: column ',col,' not found, will be considered empty. Dataset may be incomplete'))
    }
  }
  #Order the columns (OTH and NONE at the end)
  return(dframe[,all])
}

abcol <- function(key){
  
  k1 <- list('[CAUSES]'='C','[SYMPTOM]'='S','[LOCATION]'='L','[PREVENTS]'='P','[DIAGNOSED_BY_TEST_OR_DRUG]'='D','[MANIFESTATION]'='M',
             '[ASSOCIATED_WITH]'='AW','[PART_OF]'='PO','[OTHER]'='OTH','[TREATS]'='T','[NONE]'='NONE','[IS_A]'='IA','[SIDE_EFFECT]'='SE',
             '[CONTRAINDICATES]'='CI')
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

simplify <- function(data,mField) {
  
  allOptions <- unique(data[[mField]])

  mulOptions <- grep("\n",allOptions)
  simpOptions <- setdiff(c(1:length(unique(data[[mField]]))),mulOptions)

  #df with the values for simpleOptions
  sr <- table(data)[,allOptions[simpOptions]]

  #df with the values for multOptions
  #To be decomposed and the sum to sr. 
  mr <- data[data[[mField]] %in% allOptions[mulOptions],]

  #Express the mr dframe as a contingency table.

  mult <- as.table(matrix(0,length(rownames(sr)),length(colnames(sr))))
  rownames(mult) <- rownames(sr)
  colnames(mult) <- colnames(sr)

  
  mrdf <- as.data.frame(table(mr))
  mrdf <- mrdf[mrdf$Freq > 0,]

  rels <- unique(mrdf$relation)

  splitlabels <- strsplit(as.character(rels),'\n')
    
  for (i in 1:length(rels)){
    rowset <- mrdf[mrdf$relation==rels[i],]
    for(j in 1:dim(rowset)[1]){
      row = rowset[j,];
        
      for(l in splitlabels[i][[1]]){
        mult[as.character(row$unit_id),l] <- mult[as.character(row$unit_id),l] + row$Freq;          
      }
    }
  }
  return (sr + mult)
}

#data: raw_data (as read from the csv file)
#pfield: pivot field (worker_id, unit_id).
#field: the other field in the contingency table (for the moment, only 'relation')
pivot <-function(data,pfield,field){
  data <- colselect(raw_data,pfield,field)
  return(simplify(data,field))
}

