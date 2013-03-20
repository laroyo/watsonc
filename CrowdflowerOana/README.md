Files Description
------------------

The script index.php is the web interface for creating a CrowdFlower job: uploads the data file, sets the job title, 
the number of judgments per unit, the number of units per assignment, the maximum number of judgments that one worker 
can make, the maximum number of judgments from the same IP address and the payment for one assignment. 

The script indexcrowdflower.php creates and orders a CrowdFlower job using the specified settings. The following API 
functions were used:
- create an empty job with main settings: title, instructions, judgments per unit, max judgments per worker/ip, payment 
per assignment, the webhook address
- execute cURL query for including countries whose workers are allowed to solve the job
- execute cURL query for excluding countries whose workers are forbidden to solve the job
- execute cURL query for adding job options
- execute cURL query for adding available channels to the job
- upload the data to be annotated into the new job (csv file)
- order the job
- extract the link of the job from the internal interface of CrowdFlower 

The script extractinfo.php gets the results from a finished CrowdFlower job. The following API functions were used:
- cURL query for seeing the channels that were used
- cURL query for getting all the units from a job; save the units' ids in a vector 
- creates two reports by taking each unit (sentence) of the job and extracting the workers' responses

After running the script, two output files will be generated in the home directory of the user (the files may not be 
correctly created if running the script directly from the browser because of access rights; you should run the script 
from command line; in case you run the script directly from browser, the output files should be created in advance). 
These are the output files:
- results.csv: takes each unit of the job and saves all the judgments made by the workers; the main attributes are the 
following: job id, unit id, worker id, worker trust, relation type, channel, the time when the worker started to solve 
the assignment, the time when the worker finished the assignment, terms, sentence
- overview.csv: for each unit of the job, extracts the basic characteristics; the main attributes are the following: 
job id, unit id, the time when the first worker started to solve that unit, the time when the last worker finished to 
solve that unit, the unit agreement, agg which represents the highest agreement weighted by worker trust   
