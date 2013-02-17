calc_measures <- function(dframe,measures,add=FALSE){

  for (m in measures) {
    #Add a mesure => calculate the measure and add the data.
      dframe[[m]] <- do.call(m,list(dframe))
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







