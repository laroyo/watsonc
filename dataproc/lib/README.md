## Filters

* filterOutliers
  Receives a vector with the completion times for different task and the expected completion time and "filters out" the times outside two predified bounds:
  - Lower bound: expectedCompletionTime / 3
  - Upper bound: expectedCompletionTime * 3
  
Returns a list with three elements: 
	- Time values that were below the lower bound. 
	- Time values that were over the upper bound. 
	- Time values that were between the upper and the lower bound ('correct ones'). 
	
# Explanation filters: apart from choosing the relation that holds in the sentence, the user is required to fill in some additional text with either the words in the
sentences that lead to that conclusion (selected_words) or a brief explanation to support the relation chosen. 
  
* validWords: verifies that at least one "valid" word is used in the text. A "valid" word is one that is present in Wordnet, is present in the sentence (i.e. a medical term) or it's a relation of the predefined set.  
* repeatedResponse:  verifies whether the same text is used for filling both textfield ('explanations' and 'selected_words')
* repeatedText: deteces workers that use the same text for the explanations and / or selected_words across all of their tasks. 

#Other filters:

* noneOther: by definition, the relations "NONE" or "OTHER" are not to be used in conjunction with other relations. Likewise, they're mutually exclusive. 
Therefore, using those relation in combination with other reflects a poor undestanding of nature of the task. 



