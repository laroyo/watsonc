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

abcol <- function(key){
  k <- list('[CAUSES]'='CA','[SYMPTOM]'='S','[LOCATION]'='L','[PREVENTS]'='P','[DIAGNOSED_BY_TEST_OR_DRUG]'='D','[MANIFESTATION]'='M',
            '[ASSOCIATED_WITH]'='AW','[PART_OF]'='PO','[OTHER]'='OTH','[TREATS]'='T','[NONE]'='NONE','[IS_A]'='IA','[SIDE_EFFECT]'='SE',
            '[CONTRAINDICATES]'='CI')
  
  return(k[as.character(key)][[1]])
}

simplify <- function(data,mField) {
  
  allOptions <- unique(d[[mField]])

  mulOptions <- grep("\n",allOptions)
  simpOptions <- setdiff(c(1:length(unique(d[[mField]]))),mulOptions)

  #df with the values for simpleOptions
  sr <- table(d)[,allOptions[simpOptions]]

  #df with the values for multOptions
  #To be decomposed and the sum to sr. 
  mr <- d[d[[mField]] %in% allOptions[mulOptions],]

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
