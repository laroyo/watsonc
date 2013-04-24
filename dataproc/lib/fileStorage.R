FilteredWorkers <- 'FilteredWorkers'
FilteredSentences <- 'FilteredSentences'
AnalysisFiles <- 'AnalysisFiles'

folderTypes <- list('filtWorkers' = 'FilteredWorkers','filtSentences'='FilteredSentences', 'analysisFiles'='AnalysisFiles')
fileTypes <- list('heatMap' = 'heatmap','workerMetrics'='workerMetrics','sentenceMetrics' = 'sentenceMetrics', 'histogram' = 'histogram')
mimeTypes <- list('csv'='text/csv', 'excel'='application/vnd.ms-excel', 'jpg' = 'image/jpeg')
fileExt <- list('csv'='.csv','excel'= '.xlsx', 'jpg' = '.jpg')

eval(parse(text="csv='text/csv'; excel='application/vnd.ms-excel'"),envir=mimeTypes)

getFilePath <- function(job_id,folderName){
  
  if(folderName %in% unlist(folderTypes)){
    Sys.setlocale("LC_TIME", "en_US.utf8")

    timestamp <- format(Sys.time(), "%d%b%Y-%H:%M:%S")    
    path <- paste(filespath,folderName,timestamp,sep='/')
    dir.create(path, showWarnings = FALSE)
       
    return(path)
    
  } else {
    stop(paste('Error: wrong type of file : ',folderName,sep=''))
  }
}

getFileName <- function (job_id,fileType,prefix=NULL){
  if(fileType %in% unlist(fileTypes)){
    if(fileType == 'heatmap' || fileType=='histogram'){
      ext = fileExt[['jpg']]
    } else {
      ext = fileExt[['excel']]
    }
      
    filename <- paste(fileType,'_',job_id,ext,sep='')
    if(! is.null(prefix)){
      filename <- paste(prefix,'_',filename,sep='')
    }
    return (filename)
  } else {
    stop(paste('Error: wrong type of file : ',fileType,sep=''))
  }
}

writeOutputHeaders <- function(workbook, worksheet){

  writeWorksheet(workbook,data="Worker ID",sheet=worksheet,startRow=1,startCol=1,header=FALSE)  
  writeWorksheet(workbook,data="No Filter",sheet=worksheet,startRow=1,startCol=2,header=FALSE)  
  writeWorksheet(workbook,data="|V| < STDEV",sheet=worksheet,startRow=1,startCol=6,header=FALSE)
  writeWorksheet(workbook,data="norm|V| < STDEV",sheet=worksheet,startRow=1,startCol=10,header=FALSE)
  writeWorksheet(workbook,data="norm|R| < STDEV",sheet=worksheet,startRow=1,startCol=14,header=FALSE)

  mergeCells(workbook, sheet = worksheet, reference = "A1:A2")
  mergeCells(workbook, sheet = worksheet, reference = "B1:E1")
  mergeCells(workbook, sheet = worksheet, reference = "F1:I1")
  mergeCells(workbook, sheet = worksheet, reference = "J1:M1")
  mergeCells(workbook, sheet = worksheet, reference = "N1:Q1")
}

writeFilteredOutHeaders <- function (workbook, worksheet) {
  
  writeWorksheet(workbook,data="|V| < STDEV",sheet=worksheet,startRow=1,startCol=1,header=FALSE)
  writeWorksheet(workbook,data="norm|V| < STDEV",sheet=worksheet,startRow=1,startCol=3,header=FALSE)
  writeWorksheet(workbook,data="norm|R| < STDEV",sheet=worksheet,startRow=1,startCol=5,header=FALSE)
  mergeCells(workbook, sheet = worksheet, reference = "A1:B1")
  mergeCells(workbook, sheet = worksheet, reference = "C1:D1")
  mergeCells(workbook, sheet = worksheet, reference = "E1:F1")
  for(i in seq(from=2,to=100)){
    cells <- paste("A",as.character(i),":","B",as.character(i),sep='')
    mergeCells(workbook, sheet = worksheet, reference = cells)
    cells <- paste("C",as.character(i),":","D",as.character(i),sep='')
    mergeCells(workbook, sheet = worksheet, reference = cells)
    cells <- paste("E",as.character(i),":","F",as.character(i),sep='')
    mergeCells(workbook, sheet = worksheet, reference = cells)
  }
}

genHeatMap <- function(dframe,job_id,prefix=NULL,dir=NULL){
  
  library(gplots)
  library(RColorBrewer)

  fname <- getFileName(job_id,fileTypes[['heatMap']],prefix)
  if(is.null(dir)){
    dir <- getFilePath(job_id,folderTypes[['analysisFiles']])
  }
  
  path = paste(dir, fname,sep='/')

  jpeg(path,width=750,height=750)   
  heatmap.2(as.matrix(dframe), Rowv=FALSE,Colv=FALSE,dendrogram='none',scale='none',col=brewer.pal(11,'RdYlGn')[6:11],trace='none',key=FALSE,cellnote=dframe,notecol='black',lmat=rbind( c(1, 3), c(2,1), c(1,4) ), lhei=c(1, 4, 2 ))
  dev.off()
  #FIXME set a valid creator,
  creator <- 'script'
  saveFileMetadata(fname,path,mimeTypes[['jpg']],-1,creator)
  
}

genHistogram <- function(tVector,job_id,prefix=NULL,dir=NULL){

  fname <- getFileName(job_id,fileTypes[['histogram']],prefix)
  if(is.null(dir)){
    dir <- getFilePath(job_id,folderTypes[['analysisFiles']])
  }

  path = paste(dir, fname,sep='/')
  
  jpeg(path,width=750,height=750)
  h<-hist(tVector, breaks=10, col="blue", xlab="Task completion time",main="Histogram with Normal Curve")
  dev.off()
  ## xfit<-seq(min(tVector),max(tVector),length=40) 
  ## yfit<-dnorm(xfit,mean=mean(tVector),sd=sd(tVector)) 
  ## yfit <- yfit*diff(h$mids[1:2])*length(tVector) 
  ## lines(xfit, yfit, col="blue", lwd=2)
}



