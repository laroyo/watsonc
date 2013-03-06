#returns the sentences in common between two workers. 
sentInCommon <- function (raw_data, worker1, worker2){
  return (intersect(raw_data[raw_data$worker_id == worker1,]$unit_id, raw_data[raw_data$worker_id == worker2,]$unit_id))
}

# Returns a matrix of the number of sentences in common between workers. 
numSentInCommon <- function (raw_data){

  worker_ids <- unique(raw_data$worker_id)
  m <- matrix(nrow = length(worker_ids), ncol = length(worker_ids))
  
  for (i in 1:length(worker_ids)) {
    for (j in i:length(worker_ids)){
      if(i == j){
        wid <- worker_ids[i]
        m[i,j] <- length(unique(raw_data[raw_data$worker_id == wid,]$unit_id))
      } else {        
        count <-  length(sentInCommon(raw_data, worker_ids[i], worker_ids[j]))
        m[i,j] <- count
        m[j,i] <- count
      }
    }
  }
  return(m)
}
