## Read file 90-sents-all-batches-GS-sentsv3.csv and applies the filters. 
## The filter output is the same as 90-sents-all-batches-CS-sentsv3.csv (Dropbox/data/CF-Results-processed/)

source('measures.R')
source('filters.R')
source('simplify.R')

raw_data <- read.csv('90-sents-all-batches-GS-sentsv3.csv',header=TRUE)

#As the format is not the same as the CFlower csv output files, it requires some preprocessing (1 & 2)
#1. Discard the rows and columns that do not contain useful data. 
data <- raw_data[1:90,]
df <- data[,1:15]

#2. Use the first column as row names
rownames(df) <- df[,1]
df <- df[,2:15] 

#Label the columns (abbr. version and missing columns)
df <- labeldf(df)

#Calculate the measures to apply the filters.
filters <- list('SQRT','NormSQRT','NormR','NormRAll')
mdf <- calc_measures(df,filters)

discarded <- list()
filtered <- list()

for (f in filters){
  #Apply the filters: each one returns the discarded rows (those below the threshold)
  discarded[[f]] <- belowDiff(mdf,f)
  #The 'filtered' 
  filtered[[f]] <- setdiff(rownames(df),discarded[[f]])
}

###################################################
# Access the results for ONE FILTER in particular.
###################################################

#Ex: get the rows that were discarded by filter NormSQRT

discDf <- df[discarded[['NormSQRT']],]

#Ex: get the rows that were filtered by filter NormSQRT

filtDf <- df[filtered[['NormSQRT']],]

#Check the values of the discarded rows: 
mdf[rownames(discDf),]


###################################################
# Access the results for a COMBINATION of filters
###################################################

# Similar to the previous one, but combining the rows of discarded elements for the differente filters.

disc <- union(discarded[['NormSQRT']],discarded[['NormRAll']])

#Select the discarded rows
drows <- df[disc,]

#Select the filtered rows. 
frows <- df[!(rowname(df) %in% disc),]

###################################################
# Access the result for ALL filters
###################################################

# Transform into a vector the list of all discarded rows
disc <- unique(as.vector(unlist(discarded)))

#Select the discarded rows
drows <- df[disc,]

#Select the filtered rows. 
frows <- df[!(rowname(df) %in% disc),]








