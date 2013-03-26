<?php

echo urlencode("<p>In the sentence:&#160;<strong><em>\"</em></strong>
{{sentence}}<strong><em>\"</em></strong></p>
<p>Is<strong>&#160;</strong>
{{term1}}<strong>&#160;</strong>&#160;<em><strong>----</strong>related-to<strong>----&#160;</strong></em>&#160;
{{term2}}?</p>
<p><strong></strong></p>
<cml:checkboxes label=\"Select the valid RELATION(s)\" class=\"\" instructions=\"It is important that you understand what the different relation types mean. Definitions and examples are given in each choice\" validates=\"required\"><cml:checkbox label=\"[TREATS]: therapeutic use of an ingredient or a drug, e.g. penicillin cures infection, etc.\"/><cml:checkbox label=\"[PREVENTS]: preventative use of an ingredient or a drug, e.g. vitamin C reduces the risk of influenza, etc.\"/><cml:checkbox label=\"[DIAGNOSED_BY_TEST_OR_DRUG]: diagnostic use of an ingredient, test or a drug, e.g.  RINNE test is used for determining hearing loss, etc.\" id=\"\"/><cml:checkbox label=\"[CAUSES]: the underlying reason for a symptom or a disease, e.g. fever induces dizziness etc.\" id=\"\"/><cml:checkbox label=\"[LOCATION]: body part or anatomical structure in which disease or disorder is observed, e.g. leukimia is found in the circulatory system, etc.\" id=\"\"/><cml:checkbox label=\"[SYMPTOM]: deviation from normal function indicating the presence of disease or abnormality, e.g. pain is a symptom of a broken arm, etc.\" id=\"\"/><cml:checkbox label=\"[MANIFESTATION]: links disorders to the observations (manifestations) that are closely associated with them, e.g. abdominal distension is a manifestation of liver failure\" id=\"\"/><cml:checkbox label=\"[CONTRAINDICATES]: a condition that indicates that drug or treatment SHOULD NOT BE USED, e.g. patients with obesity should avoid using danazol\" id=\"\"/><cml:checkbox label=\"[ASSOCIATED_WITH]: signs, symptoms or findings that often appear together, e.g. patients who smoke often have yellow teeth.\" id=\"\"/><cml:checkbox label=\"[SIDE_EFFECT]: a secondary condition or symptom that results from a drug or treatment, e.g. use of antidepressants causes dryness in the eyes.\" id=\"\"/><cml:checkbox label=\"[IS_A]: a relation that indicates that one of the terms is more specific variation of the other, e.g. migraine is a kind of headache. \" id=\"\"/><cml:checkbox label=\"[PART_OF]: an anatomical or structural sub-component, e.g. the left ventrical is part of the heart\" id=\"\"/><cml:checkbox label=\"[OTHER]: the words are related, but not by any of the above relations\" id=\"\"/><cml:checkbox label=\"[NONE]: there is no relation between those words in this sentence\" id=\"\"/></cml:checkboxes>");

?>