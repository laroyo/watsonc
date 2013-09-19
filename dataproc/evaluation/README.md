* evallib.R
* workerMetrics.R
* sentenceMetrics.R: 

In order to correctly use the proposed metrics, its necessary to evaluate them to establish the dataset size so the information they provide is stable (i.e. adding new data doesn't significantly changes the value of the metric). That way, it can be know whether the gathered data is sufficient to reach significative conclussions or else more data is needed. Also, its relevant to test whether the sentence and worker metrics are independent from each other. The opposite would mean that, in order to reach signficative conclussions, not only the sample size is to be taken into account, but also the quality of the sample.For instance, computing the worker metrics for a particular worker would need a bigger or smaller number of annotated sentences, if the clarity of the sentences is high or low.

Therefore, two hypothesis are to be evaluated:
* The metric m is stable for a threshold over &theta;
* The metric m<sub>i</sub> is independent of the metric m<sub>j</sub> &#124; (m<sub>i</sub> &ne; m<sub>j</sub>) &and; (m<sub>i</sub>; ; m<sub>j</sub> &isin; {WorkerMetrics; SentenceMetric}