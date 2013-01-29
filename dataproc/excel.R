#!/usr/bin/Rscript

library(XLConnect)
#data <- readWorksheet(loadWorkbook("smetrics.xlsx"),sheet=1)

args <- commandArgs(trailingOnly = TRUE)

if(length(args) == 0){
  ## Default options
  inputfile <- "90-sents-WebSci13-all-batches-with-manual-annotations.xlsx"
  outputfile  <- "output.xlsx"
} else {
  inputfile <- args[1]
  outputfile  <- args[2]
}



data <- readWorksheet(loadWorkbook(inputfile),sheet=1)

#For excel spreadsheets, discard empty columns
nullCols <- colSums(is.na(data))
data = data[,nullCols!=dim(data)[1]]

names(data)[names(data)=="STEP1..Chosen.relation"] <- "relation"
names(data)[names(data)=="STEP2A..Words.from.the.sentence"] <- "selected_words"
names(data)[names(data)=="STEP2B..Explain.why"] <- "explanation"

field = 'UID'
pField = 'relation'

source('simplify.R')

d <- colselect(data,field,pField)
res <- simplify(d,pField)
#Replace the names of the columns by its abbreviated key. 
colnames(res) <- unlist(lapply(colnames(res),abcol))
dfres <- as.data.frame(rbind(res))

rels <- c('D','S','CA','M','L','AW','P','SE','IA','PO','T','CI')
othernone <- c('NONE')
#othernone <- c('OTHER','NONE')
all <- c(rels,othernone)

dfres$SumSQ <- rowSums(dfres[,all]^2)
dfres$SQRT <- sqrt(dfres$SumSQ)
dfres$Difference <- dfres$SQRT - apply(dfres[,all],1,max)
dfres$NormalizedSQRT <- dfres$SQRT/ rowSums(dfres[,all])
dfres$SumSQRels <- rowSums(dfres[,rels]^2)
dfres$SQTRels <- sqrt(dfres$SumSQRels)
dfres$DiffRel <- dfres$SQTRels - apply(dfres[,rels],1,max)


wb.new <- loadWorkbook(outputfile, create = TRUE)
createSheet(wb.new, name = "pivot-sentence")
writeWorksheet(wb.new,data=dfres,sheet=1,startRow=1,startCol=1,rownames='UID')

field = 'WID'
pField = 'relation'
d <- colselect(data,field,pField)
res <- simplify(d,pField)

rels <- c('D','S','CA','M','L','AW','P','SE','IA','PO','T','CI')
othernone <- c('NONE')
#othernone <- c('OTHER','NONE')
all <- c(rels,othernone)

dfres$SumSQ <- rowSums(dfres[,all]^2)
dfres$SQRT <- sqrt(dfres$SumSQ)
dfres$Difference <- dfres$SQRT - apply(dfres[,all],1,max)
dfres$NormalizedSQRT <- dfres$SQRT/ rowSums(dfres[,all])
dfres$SumSQRels <- rowSums(dfres[,rels]^2)
dfres$SQTRels <- sqrt(dfres$SumSQRels)
dfres$DiffRel <- dfres$SQTRels - apply(dfres[,rels],1,max)

createSheet(wb.new, name = "pivot-worker")
writeWorksheet(wb.new,data=rbind(res),sheet=2,startRow=1,startCol=1,rownames='WID')
saveWorkbook(wb.new)





