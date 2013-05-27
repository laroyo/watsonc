#!/usr/bin/Rscript
source('envars.R')
source('lib/db.R')
source('lib/simplify.R')


args <- commandArgs(trailingOnly = TRUE)

if(length(args) > 0){
  job_id <- args[1]
}

raw_data <- getJob(job_id)

sentenceTable <- pivot(raw_data,'unit_id','relation')
sentenceDf <- getDf(sentenceTable)
#cat(paste("{'rownames' :",toJSON(row.names(sentenceDf)),",'annotations' :", toJSON(sentenceDf), "}"),sep='')
res = list()
res[['rownames']] <- row.names(sentenceDf)
res[['matrix']] <- sentenceDf
cat(toJSON(res))
#cat('OK')
## for (r in row.names(sentenceDf)){
##   rows += toJSON(sentenceDf[r,]))
## }

