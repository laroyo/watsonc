#!/usr/bin/Rscript

#Get a data frame with the relations majoritarily chosen for each of the sentences of a job
#FIXME: extend to set.

source('/var/www/html/wcs/dataproc/envars.R')

source(paste(libpath,'/db.R',sep=''),chdir=TRUE)
source(paste(libpath,'/simplify.R',sep=''),chdir=TRUE)

args <- commandArgs(trailingOnly = TRUE)

if(length(args) > 0){
  job.id <- args[1]
}

raw.data <- getJob(job.id)

sentenceTable <- pivot(raw.data,'unit_id','relation')
sentenceDf <- getDf(sentenceTable)

majRelations <- apply(sentenceDf,1,majRelation)
freqRelations <- table(Reduce(c,majRelations))

relFreq <- list()
for (unit.id in names(majRelations)){
  rel <- majRelations[[unit.id]]
  for (r in rel){
    pos <- length(relFreq[[r]])+ 1
    elem <- list()
    elem[['unit_id']] <- unit.id
    #FIXME: clarity is not present in all the elements of the database. 
    ## query <- sprintf('select clarity from sent_clarity where unit_id = %s',unit.id)
    ## elem[['clarity']] <- dbGetQuery(con,query)[[1]]
    elem[['clarity']] <- 0.5

    relFreq[[r]][[pos]] <- elem
  }  
}

l <- list()
l[['aggr']] <- rev(sort(freqRelations))
l[['rels']] <- relFreq
cat(toJSON(l))
  
     
	 

