FilteredWorkers <- 'FilteredWorkers'
FilteredSentences <- 'FilteredSentences'
AnalysisFiles <- 'AnalysisFiles'

folderTypes <- list('filtWorkers' = 'FilteredWorkers','filtSentences'='FilteredSentences', 'analysisFiles'='AnalysisFiles')
fileTypes <- list('heatMap' = 'heatmap','workerMetrics'='workerMetrics','sentenceMetrics' = 'sentenceMetrics', 'histogram' = 'histogram', 'relationCluster'='relationCluster','pivotCFOutput' = 'pivotCFOutput', 'pivotWithSingletons'='pivotWithSingletons','pivotWithoutSingletons'='pivotWithoutSingletons','sentMetricsEvaluation'='sentMetricsEvaluation','workerMetricsEvaluation'='workerMetricsEvaluation')
mimeTypes <- list('csv'='text/csv', 'excel'='application/vnd.ms-excel', 'jpg' = 'image/jpeg')
fileExt <- list('csv'='.csv','excel'= '.xlsx', 'jpg' = '.jpg')

eval(parse(text="csv='text/csv'; excel='application/vnd.ms-excel'"),envir=mimeTypes)

getFilePath <- function(job_id,folderName, useTimeStamp=TRUE){
  
  if(folderName %in% unlist(folderTypes)){
    if(useTimeStamp) {
      Sys.setlocale("LC_TIME", "en_US.utf8")
    
      timestamp <- format(Sys.time(), "%d%b%Y-%H:%M:%S")
      path <- paste(filespath,folderName,timestamp,sep='/')
    } else{
      path <- paste(filespath,folderName, paste('Job_',job_id,sep=''), sep='/')
    }
        
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
  writeWorksheet(workbook,data="norm-all|R| < STDEV",sheet=worksheet,startRow=1,startCol=18,header=FALSE)

  mergeCells(workbook, sheet = worksheet, reference = "A1:A2")
  mergeCells(workbook, sheet = worksheet, reference = "B1:E1")
  mergeCells(workbook, sheet = worksheet, reference = "F1:I1")
  mergeCells(workbook, sheet = worksheet, reference = "J1:M1")
  mergeCells(workbook, sheet = worksheet, reference = "N1:Q1")
  mergeCells(workbook, sheet = worksheet, reference = "R1:U1")
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

writeBehFiltersHeaders <- function(workbook, worksheet){

  writeWorksheet(workbook,data="None - Other",sheet=worksheet,startRow=1,startCol=1,header=FALSE)  
  writeWorksheet(workbook,data="Rep Response",sheet=worksheet,startRow=1,startCol=3,header=FALSE)  
  writeWorksheet(workbook,data="No val words",sheet=worksheet,startRow=1,startCol=5,header=FALSE)
  writeWorksheet(workbook,data="Repeated Text",sheet=worksheet,startRow=1,startCol=7,header=FALSE)

  mergeCells(workbook, sheet = worksheet, reference = "A1:B1")
  mergeCells(workbook, sheet = worksheet, reference = "C1:D1")
  mergeCells(workbook, sheet = worksheet, reference = "E1:F1")
  mergeCells(workbook, sheet = worksheet, reference = "G1:H1")
  mergeCells(workbook, sheet = worksheet, reference = "I1:J1")
}

saveSentenceMetrics <- function(workbook, worksheet,sentRelDf,sentClarity) {

  writeWorksheet(wb.new,data="Sentence-relation score",sheet=worksheet,startRow=1,startCol=1,header=FALSE)
  mergeCells(workbook, sheet = worksheet, reference = "A1:C1")
 
  writeWorksheet(wb.new,data=sentRelDf,sheet=worksheet,startRow=3,startCol=1,rownames="unit_id")
    
  writeWorksheet(wb.new,data=format(sentRelDf,digits=1),sheet=worksheet,startRow=3,startCol=2,rownames=row.names)

  writeWorksheet(wb.new,data=format(as.data.frame(sentClarity),digits=2),sheet=worksheet,startRow=3,startCol=17,rownames=NULL)  
}

saveRelationSimilarity <- function(workbook, worksheet,relSimilarity,relAmbiguity, relClarity) {

  writeWorksheet(wb.new,data="Relation similarity",sheet=worksheet,startRow=1,startCol=1,header=FALSE)
  
  writeWorksheet(wb.new,data=format(as.data.frame.matrix(relSimilarity),digits=1,scientific=FALSE),sheet=worksheet,startRow=3,startCol=2,rownames=NULL)

  writeWorksheet(wb.new,data=all,sheet=worksheet,startRow=4,startCol=1,header=FALSE,rownames=NULL)

  writeWorksheet(wb.new,data="Relation Ambiguity",sheet=worksheet,startRow=dim(relSimilarity)[1] +5, startCol=1,header=FALSE)
  writeWorksheet(wb.new,data=format(as.data.frame(relAmbiguity),digits=1),sheet=worksheet,startRow = dim(relSimilarity)[1] +6, startCol=2)

  writeWorksheet(wb.new,data="Relation Clarity",sheet=worksheet,startRow=dim(relSimilarity)[1] +8, startCol=1,header=FALSE)
  writeWorksheet(wb.new,data=format(as.data.frame(t(relClarity)),digits=2),sheet=worksheet,startRow = dim(relSimilarity)[1] +9, startCol=2)
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

plotTimes <- function(tVector, job_id, prefix=NULL, dir=NULL){

  fname <- getFileName(job_id,fileTypes[['histogram']],prefix)
  if(is.null(dir)){
    dir <- getFilePath(job_id,folderTypes[['analysisFiles']])
  }
  
  path = paste(dir, fname,sep='/')
  
  jpeg(path,width=750,height=750)
  h<-hist(tVector, breaks=10, col="blue", xlab="Task completion time",ylab="Number of tasks completed on that time", main="Task completion time")
  dev.off()
  
  fname2 <- getFileName(job_id,fileTypes[['histogram']],paste(prefix,'_line',sep=''))
  path2 = paste(dir, fname2,sep='/')
  jpeg(path2,width=750,height=750)

  plot(sort(tVector),type='p',xlab='Element (Index)',ylab='time',main='Task completion times (sorted asc)')
  dev.off()                          
}

saveEvaluationMetrics <- function(ws, ws.name, diff1, diff2,diff3){

  createSheet(ws, name = ws.name)
  writeWorksheet(ws,data='s1 - all',sheet=ws.name, header=FALSE, startRow=3,startCol=2)
  writeWorksheet(ws,data='s2 - all',sheet=ws.name, header=FALSE, startRow=3,startCol=3)
  writeWorksheet(ws,data='s3 - all',sheet=ws.name, header=FALSE, startRow=3,startCol=4)
  
  writeWorksheet(ws,data=diff1,sheet=ws.name, header=FALSE, startRow = 4, startCol=1,rownames='unit_id')
  
  writeWorksheet(ws,data='Difference in the Sentence Clarity values',sheet=ws.name,startRow = 1, header=FALSE)
  mergeCells(ws, sheet = ws.name, reference = "A1:D1")
  
  mergeCells(ws, sheet=ws.name,reference='G3:I3')
  mergeCells(ws, sheet=ws.name,reference='G4:I4')
  mergeCells(ws, sheet=ws.name,reference='G5:I5')
  mergeCells(ws, sheet=ws.name,reference='G6:L6')

  writeWorksheet(ws,data='S1: One third of the workers',sheet=ws.name,startRow = 3, startCol=7,header=FALSE)  
  writeWorksheet(ws,data='S2: Half of the workers',sheet=ws.name,startRow = 4, startCol=7,header=FALSE)
  writeWorksheet(ws,data='S3: Two thirds of the workers',sheet=ws.name,startRow = 5, startCol=7,header=FALSE)
  writeWorksheet(ws,data='--: Not enough annotations to draw a sample of the required size',sheet=ws.name,startRow = 6, startCol=7,header=FALSE)
  
  avg1 <- mean(diff1)
  abs.avg1 <- mean(abs(diff1))
  max1 <- max(abs(diff1))

  avg2 <- mean(diff2)
  abs.avg2 <- mean(abs(diff2))
  max2 <- max(abs(diff2))

  avg3 <- mean(diff3)
  abs.avg3 <- mean(abs(diff3))
  max3 <- max(abs(diff3))
  
  #Add entries for missing values. 
  colnames(diff2) <- 'diff2'
  colnames(diff3) <- 'diff3'  
  
  for (unit.id in excfrom3){
    diff2 <- rbind(as.data.frame(diff2),data.frame(diff2=NA,row.names=unit.id) )
  }
  
  diff2 <- diff2[order(rownames(diff2)),,drop=FALSE]
  
  for (unit.id in excfrom4){
    diff3 <- rbind(as.data.frame(diff3),data.frame(diff3=NA,row.names=unit.id) )
  }
  
  diff3 <- diff3[order(rownames(diff3)),,drop=FALSE]
  
  


  ## print(colnames(diff2))
  ## print(colnames(diff3))
  
  for(i in seq(1:dim(diff2)[1])){           
    if(is.na(diff2[i,1])){      
      writeWorksheet(ws,data='--',sheet=ws.name, header=FALSE, startRow = i+3, startCol=3)
    } else {
      writeWorksheet(ws,data=diff2[i,1],sheet=ws.name, header=FALSE, startRow = i+3, startCol=3)
    }
  }

  for(i in seq(1:dim(diff3)[1])){
    if(is.na(diff3[i,1])){
      writeWorksheet(ws,data='--',sheet=ws.name, header=FALSE, startRow = i+3, startCol=4)
    } else {
      writeWorksheet(ws,data=diff3[i,1],sheet=ws.name, header=FALSE, startRow = i+3, startCol=4)
    }
  }    
  
  #writeWorksheet(ws,data=diff3,sheet=ws.name, header=TRUE, startRow = 3, startCol=4,rownames=NULL)
 
  num.elem <- length(diff1)
  writeWorksheet(ws, sheet=ws.name, data='AVG: ', startRow =  num.elem +5,startCol=1, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=avg1, startRow = num.elem+5,startCol=2, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=avg2, startRow = num.elem+5,startCol=3, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=avg3, startRow = num.elem+5,startCol=4, header=FALSE)

  writeWorksheet(ws, sheet=ws.name, data='|AVG|: ', startRow = num.elem+6,startCol=1, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=abs.avg1, startRow = num.elem+6,startCol=2, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=abs.avg2, startRow = num.elem+6,startCol=3, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=abs.avg3, startRow = num.elem+6,startCol=4, header=FALSE)

  writeWorksheet(ws, sheet=ws.name, data='|MAX|: ', startRow = num.elem+7,startCol=1, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=max1, startRow = num.elem+7,startCol=2, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=max2, startRow = num.elem+7,startCol=3, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=max3, startRow = num.elem+7,startCol=4, header=FALSE)

}

saveSampleSize <- function(ws, ws.name, s){

  writeWorksheet(ws, sheet=ws.name, data=3, startRow=1,startCol=2, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=6, startRow=1,startCol=3, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=9, startRow=1,startCol=4, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=12, startRow=1,startCol=5, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data=15, startRow=1,startCol=6, header=FALSE)

  writeWorksheet(ws, sheet=ws.name, data='N1', startRow=1,startCol=7, header=FALSE)
  writeWorksheet(ws, sheet=ws.name, data='N2', startRow=1,startCol=8, header=FALSE)
  
  
  count <- 2

  num.cols  <- length(s[[1]])
  
  for(key in names(s)){
    worker.id <- as.numeric(key)

    writeWorksheet(ws, sheet=ws.name, data=worker.id, startRow = count,startCol=1, header=FALSE)
    
    for(i in seq(1:num.cols)){
      writeWorksheet(ws, sheet=ws.name, data=length(s[[key]][[i]]), startRow = count,startCol=(i+1), header=FALSE)
    }
    count <- (count + 1)
  }

}

saveWorkerMetrics <- function(ws, ws.name, m){

  num.cols  <- length(m[[1]])
  count <- 3

  mergeCells(ws, sheet = ws.name, reference = "B1:D1")    
  writeWorksheet(ws, sheet=ws.name, data='S1', startRow = 1, startCol=2,header=FALSE)
  mergeCells(ws, sheet = ws.name, reference = "E1:G1")
  writeWorksheet(ws, sheet=ws.name, data='S2', startRow = 1, startCol=5,header=FALSE)
  mergeCells(ws, sheet = ws.name, reference = "H1:J1")
  writeWorksheet(ws, sheet=ws.name, data='S3', startRow = 1, startCol=8,header=FALSE)
  mergeCells(ws, sheet = ws.name, reference = "K1:M1")
  writeWorksheet(ws, sheet=ws.name, data='S4', startRow = 1, startCol=11,header=FALSE)
  mergeCells(ws, sheet = ws.name, reference = "N1:P1")
  writeWorksheet(ws, sheet=ws.name, data='S5', startRow = 1, startCol=14,header=FALSE)
  mergeCells(ws, sheet = ws.name, reference = "Q1:S1")
  writeWorksheet(ws, sheet=ws.name, data='S6', startRow = 1, startCol=17,header=FALSE)    
  
  writeWorksheet(ws, sheet=ws.name, data='worker.id', startRow = 2,startCol=1, header=FALSE)
  
  for(i in seq(1:num.cols)){
    if(i == 1){
      writeWorksheet(ws, sheet=ws.name, data='cos', startRow = 2,startCol=2, header=FALSE)
      writeWorksheet(ws, sheet=ws.name, data='agr', startRow = 2,startCol=3, header=FALSE)
      writeWorksheet(ws, sheet=ws.name, data='annotSent', startRow = 2,startCol=4, header=FALSE)
    } else {
      print((i-1)*3+2)
      writeWorksheet(ws, sheet=ws.name, data='cos', startRow = 2,startCol=((i-1)*3+2), header=FALSE)
      writeWorksheet(ws, sheet=ws.name, data='agr', startRow = 2,startCol=((i-1)*3+3), header=FALSE)      
      writeWorksheet(ws, sheet=ws.name, data='annotSent', startRow = 2,startCol=((i-1)*3+4), header=FALSE)
    }
  }

  
  for(key in names(m)){
    worker.id <- as.numeric(key)

    writeWorksheet(ws, sheet=ws.name, data=worker.id, startRow = count,startCol=1, header=FALSE)    
    
    for(i in seq(1:num.cols)){

      if(i==1) {
        writeWorksheet(ws, sheet=ws.name, data=m[[key]][[i]][['cos']], startRow = count,startCol=2, header=FALSE)
        writeWorksheet(ws, sheet=ws.name, data=m[[key]][[i]][['agr']], startRow = count,startCol=3, header=FALSE)
        writeWorksheet(ws, sheet=ws.name, data=m[[key]][[i]][['annotSent']], startRow = count,startCol=4, header=FALSE)
      } else {
        writeWorksheet(ws, sheet=ws.name, data=m[[key]][[i]][['cos']], startRow = count,startCol=((i-1)*3+2), header=FALSE)
        writeWorksheet(ws, sheet=ws.name, data=m[[key]][[i]][['agr']], startRow = count,startCol=((i-1)*3+3), header=FALSE)
        writeWorksheet(ws, sheet=ws.name, data=m[[key]][[i]][['annotSent']], startRow = count,startCol=((i-1)*3+4), header=FALSE)
      }
    }
    count <- (count + 1)    
  }
  
}



