#!/usr/bin/Rscript

#source('/var/www/html/wcs/dataproc/envars.R')
source('/home/gsc/watson/dataproc/envars.R')

source(paste(libpath,'/db.R',sep=''),chdir=TRUE)
source(paste(libpath,'/simplify.R',sep=''),chdir=TRUE)

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

