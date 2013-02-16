calc_measures <- function(dframe,measures,add=FALSE){

  for (m in measures) {
    #Add a mesure => calculate the measure and add the data.
      dframe[[m]] <- do.call(m,list(dframe))
  }
  if(add){
    return(dframe)
  } else{
    return (dframe[,unlist(measures)])
  }
}


rels <- c('D','S','CA','M','L','AW','P','SE','IA','PO','T','CI')
#othernone <- c('NONE')
othernone <- c('OTH','NONE')
all <- c(rels,othernone)

SumSQ <- function(dframe){
  return(rowSums(dframe^2))
}


SQRT <- function(dframe){
  return(sqrt(rowSums(dframe^2)))
}

Difference <- function(dframe){
  return(sqrt(rowSums(dframe^2)) - apply(dframe,1,max))
}

NormSQRT <- function(dframe){
  rels <- c('c','s','l','d','p','t','m','aw','po','ia','se','ci')
  all <- c(rels,c('oth','none'))
  return(sqrt(rowSums(dframe[,all]^2)) / rowSums(dframe[,all]))
}

NormR <- function(dframe){
  #FIXME: rewrite to avoid using the list of relations as a fixed constant
  rels <- c('c','s','l','d','p','t','m','aw','po','ia','se','ci')
  return(sqrt(rowSums(dframe[,rels]^2)) / rowSums(dframe[,rels]))  
}

NormAllR <- function(dframe){
  #FIXME: rewrite to avoid using the list of relations as a fixed constant
  rels <- c('c','s','l','d','p','t','m','aw','po','ia','se','ci')
  all <- c(rels,c('oth','none'))
  return(sqrt(rowSums(dframe[,rels]^2)) / rowSums(dframe[,all]))    
}







