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
