## Introduction 

This module is in charge of (pre)processing the data extracted from CrowdFlower. More concretly, three main aspects have been identified: 
- Data "structuring" (refine and preprocess data, evolve structures to ease analysis, other transformations)
- Filtering, labelling and classification: to separate (filter or select) certain data, according to defined function or criteria. This may be used for operations such as spam detection, outlier removal, and the like. 
- Analysis: draw statistics from the data (TBC)

![General Schema](http://dl.dropbox.com/u/32442030/watson-images/Diagram1.png)

- The diagram represents the set of modules in a pipeline fashion, but only as a hint of how the components may be connected. This structure is intendend to be flexible, and the sequence of events may differ between processes. For instance, it is possible to restructure the data after filtering, apply different filters in different runs (instead of several at a time) and, in general, differente combinations of the pipeline blocks may be applied. An example of this is the spam detection process (see [description below](https://github.com/laroyo/watsonc/tree/master/dataproc#example-spam-filtering)), where after filtering a dataset (sentences), the results are used to build a new structure with the workers actions over the filtered sences, calculate new measures and apply filters over the workers. 

- The basic **workunit** is the dataset corresponding to one concrete experiment (ex: 90 sentences, with 10 annotations per sentence). Though that is the most typical case, it is desirable to 
be able to work across experiments and analyze different batches together (comparison). 

- **Implementation**: this module is implemented as an R library, to be used as base to build automation scripts, that can be used from the R shell or directly as shell (bash) scripts. Also, it is possible to use the library functions in an interactive manner (in an "exploratory mode", as opposited to writing "fixed" scripts). Though the learning curve of R is steep (or at least steeper than any "regular" high-level programming language), its data manipulation approach is well suited for the afore mentioned tasks, resulting on a big simplification of the implementation, both in terms of coding (specially for implementing new filters and measures) and usability (simple high-level functions). 

- The example scripts use Excel spreadsheets as an output. While this might not be necessary when working directly on R shell or on intercative mode, it comes handy for scripting, as it provides a way to store results in files (in a more user friendly way than plain data or text files)  and, in some cases, its easier to go over the data using the Excel interface rather than the barebone/minimalistic R shell. 

### Example: Spam filtering

One application so far of this process is spam filtering. To detect possible spammers, a few subsets (five, at the moment) of labeled sentences are selected (filtered) according to the distribution of "relation" tags for the sentences. The distribution of relation tags for a sentence is represented by a *sentence vector*, where each vector entry represents the number of times each relation has been identified on that sentence. The filtering of sentences consists of using the standard deviation of the sentence vector as a threshold (the sentences below the treshold will be discarded). 

To identify possible spammers, a set of metrics (average agreement, annotations/sentence, cos.) is defined and computed over the annotations done by the workers over the setences that they have annotated within the previously filtered sentence subsets. A combination of those metrics is used to classify users as spammers, in order to remove them. After removing spammers (and their annotations), the metrics for the sentences can be recalculated without the influence of the bad workers. 

(See Dropbox file Watson-crowdsourcing/data/CF-results-processed/90-sents-WebSci13-3metrics-workersv8.xlsx for further details)


## Overview

* Library files: 
  * [simplify.R](https://github.com/laroyo/watsonc/blob/master/dataproc/simplify.R) : preprocess raw data and create analysis structures. Also contains environmental variables. 
  * [measures.R](https://github.com/laroyo/watsonc/blob/master/dataproc/measures.R) : functions for calculating pre-difined measures over the data. 
  * [filters.R](https://github.com/laroyo/watsonc/blob/master/dataproc/filters.R) : functions to filter and discard items based on certain criteria/values. 

* Examples: 
  *  [cflower.R](https://github.com/laroyo/watsonc/blob/master/dataproc/cflower.R):  read a result file from Crowdflower (containing the results of a job on CSV file), and generate contingency tables for (sentence,relation) and (workers,relation). See [Example 1](https://github.com/laroyo/watsonc/wiki/Example-1:-Transform-raw-data-and-apply-basic-measures)
  *  [excel.R](https://github.com/laroyo/watsonc/blob/master/dataproc/excel.R): same as the previous one, but uses an excel file as data input. 
  *  [filter_data.R](https://github.com/laroyo/watsonc/blob/master/dataproc/filter_data.R): application of filters to sentences. See [Example 2](https://github.com/laroyo/watsonc/wiki/Example-2:-Applying-filters)

* How to run the examples: 
  * From the OS shell (tested for bash): 

     $ ./cflower.R [input_file.csv] [output_file.xlsx] 
     
     * The input/output file names are optional. If not specified, the default values (files) for test will be used. 
     * Be sure both have execution permission. (chmod +x cflower.R,chmod +x excel.R)
  * From the R shell: <br/>
      &gt; source('cflower.R')
     
 

