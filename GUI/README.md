### Introduction

GUI is the integrations of the Crowdflower, Statistics and Games modules. It enables data storage and data integration and allows users to track and retrieve history and results data dynamically. 
It is the processing component of Crowd-Watson Architecture:


- index.php: gather interfaces towards preprocessing (Input tab), creating a job (Jobs tab), checking history data and refer to analtics data (History tab); Home and About tabs contain the descriptions of the Crowd-Watson project and project memebers. 
- js folder: contains jQuery or javaScripts to interact with multiple modules and to enable data retrieval, storage and integration.
- css folder: custome GUI layout.
- statuschange folder: enable to synchronize changes of the job statuses in History Table with Crowdflower account.
- img folder: contains images for the slideshow in Home tab and other GUI images.
- plugins: contains plugins used to build up the GUI.


### GUI Flow

##### Pre-process
Raw Files 
-> Processing Files 
-> Filtered Files 
-> Batches Files
(All the files are generated at the server and registered in the database)

##### Create a job
A Batches File are selected from the server 
-> Fill in certain parameters such as judgments, payments, time and etc.
-> Send the selected Batches File to CrowdFlower or other platforms 
-> The filled-in parameters and creating information would be saved in database and shown in History Table

##### History Table
Acts like a control centre:
- Connects input for creating jobs with output retrieved from finished jobs 
- Indicates integrated data
- Indicates job completion
- Manages job statuses
- Enables files download
- Enables to block spammers

##### Retrieve the Results
Job completion message would be sent from the platform to GUI when the job is finished
-> The Results File would be saved in the server and registered in the database
-> Results parameters offered by the platform would be shown in History Table
-> Results File can be downloaded through History Table

##### Post-process
Filtered Results (Sentences) 
-> Filtered Results (Workers) 
-> Curated Results (After Spam Detection) 
-> Analyses Files
(All the files are generated at the server and registered in the database;  Results parameters by the analytic analyses would be shown in History Table)

##### View the analytics results
Select a Job ID or multiple Job IDs  with Finished status from History Table and click on Analyze button
-> Results Page will pop up, which contains Sentence Metrics, Worker Metrics, Spammers detected and etc.

### References

##### Crowd-Watson Framework

http://crowd-watson.nl/wcs/GUI/img/Crowd-Watson%20Framework.jpg


##### Crowd-Watson Architecture

http://crowd-watson.nl/wcs/GUI/img/Crowd-Watson%20Architecture.jpg


##### Crowd-Watson GUI js descriptions

http://crowd-watson.nl/wcs/GUI/js/huimain.js
