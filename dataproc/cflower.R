#!/usr/bin/Rscript

library(XLConnect)

args <- commandArgs(trailingOnly = TRUE)

if(length(args) == 0){
  inputfile <- "results.csv"
  outputfile  <- "output-results-csv.xlsx"
} else {
  inputfile <- args[1]
  outputfile  <- args[2]
}

data <- read.csv(inputfile)
names(data)[names(data)=="step_1_select_the_valid_relations"] <- "relation"
names(data)[names(data)=="step_2a_copy__paste_only_the_words_from_the_sentence_that_express_the_relation_you_selected_in_step1"] <- "selected_words"
names(data)[names(data)=="step_2b_if_you_selected_none_in_step_1_explain_why"] <- "explanation"

field = 'unit_id'
pField = 'relation'

source('simplify.R')

d <- colselect(data,field,pField)
res <- simplify(d,pField)

dfres <- as.data.frame(rbind(res))
#Replace the names of the columns by its abbreviated key. 
colnames(dfres) <- unlist(lapply(colnames(dfres),abcol))

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

field = 'worker_id'
pField = 'relation'
d <- colselect(data,field,pField)
res <- simplify(d,pField)

#Replace the names of the columns by its abbreviated key. 
colnames(res) <- unlist(lapply(colnames(res),abcol))

createSheet(wb.new, name = "pivot-worker")
writeWorksheet(wb.new,data=rbind(res),sheet=2,startRow=1,startCol=1,rownames='WID')
saveWorkbook(wb.new)
