## Read file 90-sents-all-batches-GS-sentsv3.csv and applies the filters. 
## The filter output is the same as 90-sents-all-batches-CS-sentsv3.csv (Dropbox/data/CF-Results-processed/)

source('measures.R')
source('filters.R')

abcol2 <- function(key){
  k <- list('causes'='c','symptom'='s','location'='l','prevents'='p','diagnosed_by_test_or_drug'='d','manifestation'='m','associated_with'='aw','part_of'='po','other'='oth','treats'='t','none'='none','is_a'='ia','side_effect'='se',
      'contraindicates'='ci')
  
  return(k[as.character(key)][[1]])
}

raw_data <- read.csv('90-sents-all-batches-GS-sentsv3.csv',header=TRUE)


#Discard the rows and columns that do not contain useful data. 
data <- raw_data[1:90,]
d <- data[,1:15]

#Abbreviate the field names (use initials instead of full names)
colnames(d) <- unlist(lapply(colnames(d),abcol2))
colnames(d) <- c('unit_id',colnames(d)[1:14])

#Order the columns (put 'oth' and 'none' at the end. 
d <- d[,c('unit_id','c','s','l','d','p','t','m','aw','po','ia','se','ci','oth','none')]

#Transform data into numeric values (instead of factors). 
d <- data.frame(lapply(lapply(d,as.character),as.numeric))

#Use the first column as row names
rownames(d) <- d[,1]
d <- d[,2:15] 


#Create a separated data frame for measures (makes visualization in R-shell simpler)
mdf <- data.frame(names=rownames(d))
rownames(mdf) <- rownames(d)

#Calculate the measures to apply the filters
mdf <- calc_measures(d,list('SQRT','NormSQRT','NormR','NormAllR'))

#Calculate the factor for the filters
STDEVal<- mean(mdf$SQRT) - sd(mdf$SQRT)
STDEVNorm <- mean(mdf$NormSQRT) - sd(mdf$NormSQRT)
STDEVNormR<- mean(mdf$NormR) - sd(mdf$NormR)
STDEVNormAllR <- mean(mdf$NormAllR) - sd(mdf$NormAllR)

#Apply the filters: each one returns the discarded rows (those below the threshold)
disc1 <- belowDiff(mdf,'SQRT',STDEVal)
disc2 <- belowDiff(mdf,'NormSQRT',STDEVNorm)
disc3 <- belowDiff(mdf,'NormR',STDEVNormR)
disc4 <- belowDiff(mdf,'NormAllR',STDEVNormAllR)


#Obtain the filtered rows (== exclude the discarded rows)
filtered1 <- d[! rownames(d) %in% rownames(filt1),]
filtered2 <- d[! rownames(d) %in% rownames(filt2),]
filtered3 <- d[! rownames(d) %in% rownames(filt3),]
filtered4 <- d[! rownames(d) %in% rownames(filt4),]

# TO DO: store/keep record of the filtered rows (contained in filtered{1..4})

# TO DO: store/keep record the discarded rows (contained in the variable filt{1..4})








