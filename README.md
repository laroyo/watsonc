## Crowd Watson	

Watson is an artiﬁcial intelligent system capable of answering questions posed in natural language designed by IBM. To build its knowledge base Watson was
trained on a series of databases, taxonomies, and ontologies of publicly available data. Currently, IBM Research aims at adapting the Watson technology for
question-answering in the medical domain. For this, large amounts of training and evaluation data (ground truth medical text annotation) are needed, and the
traditional ground-truth annotation approach is slow and expensive, and constrained by too restrictive annotation guidelines that are necessary to achieve
good inter-annotator agreement, which result in the aforementioned over generalization.

The Crowd Watson project implements the [*Crowd truth*](http://www.researchgate.net/publication/236463327_Crowd_Truth_Harnessing_disagreement_in_crowdsourcing_a_relation_extraction_gold_standard/file/60b7d517f69c26c5d7.pdf) approach to generate a crowdsourced gold standard for training and evaluation of IBM Watson
NLP components in the medical domain. 

The *Crowd Watson* framework supports the composition of crowd-truth gathering workﬂows, where a sequence of micro-annotation-tasks can be executed jointly either
by the general crowd on platforms like CrowdFlower, or by specialized crowd of domain experts on gaming platform as Dr. Detective. *Crowd-Watson* framework focuses
on micro-tasks for knowledge extraction in medical text. The main steps involved in the *Crowd-Watson* workﬂow are: **pre-processing** of the input, **data collection**, **disagreement analytics** for the results, and ﬁnally **post-processing**. These steps are realized as an automatic end-to-end workﬂow, that can support a continuous collection of high quality gold standard data with feedback loop to all steps of the process. The input consists of medical documents, from various sources such as Wikipedia articles or patient case reports. The output generated through this framework is annotation for medical text, in the form of concepts and the relations between them, together with a collection of visual analytics to explore these results. 

![Crowd Watson architecture](https://github.com/guillelmo/cloudsync/blob/master/docs/imgs/workflow-picture.jpg?raw=true)

### Modules

The functionality of the Crowd Watson framework is divided in modules, separating the different concerns, plus some interface modules to control and review the crowdsourcing process. 

- Core: 
    - preprocessing: creation of tasks, templates, data selection for tasks. 
    - crowdflower: interaction with the Crowdflower platform. 
    - dataproc: post-processing, spam detection and and metrics computation. 

- Interface: 
    - GUI: web interface, to control and integrate the crowdsourcing process. 
    - analytics: web interface to monitor and explore the results. 

- includes: general code, shared by the different modules, for reusability and avoid repetition. 
- services: auxiliar functionality encapsulated as services. 

### Setup and configuration

For details about dependency and instructions to set up a Crowd-watson instance, see [[Setup]] page. 


### References 
  
- [Crowdsourcing for Watson](http://drwatsonsynonymgame.wordpress.com/) - Project's blog
- [Crowd Truth](http://www.researchgate.net/publication/236463327_Crowd_Truth_Harnessing_disagreement_in_crowdsourcing_a_relation_extraction_gold_standard/file/60b7d517f69c26c5d7.pdf) - disagreement-based approach to generate a crowdsourced gold standard for training
- [Crowd Watson: Crowdsourced Text Annotations](http://crowd-watson.nl/
tech-reports/20130704.pdf). Hui Lin.  Technical report, VU University Amsterdam, July 2013.