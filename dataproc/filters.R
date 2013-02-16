# |V| < STDEV
belowDiff <- function(dframe,field, factor){
  return (dframe[dframe[[field]] < factor,])
}
