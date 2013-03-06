# Returns the ids of the rows that have a value < factor for the column 'field'
belowFactor <- function(dframe,field, factor){
  return (rownames(dframe[dframe[[field]] < factor,,drop=FALSE]))
}

belowDiff <- function(dframe,field){
  mFactor <- mean(dframe[[field]]) - sd(dframe[[field]])
  return(belowFactor(dframe,field,mFactor))
}
