//  Toggle function for the relation distribution table. 
function relTableToggle(id){

    if($('#'+id+'-icon').html() == ' + ')
	$('#'+id+'-icon').html(' - ')
    else 
	$('#'+id+'-icon').html(' + ')
    
    $('.'+id+'-row').toggle();
}

var abbr = {'C' :'[CAUSES]', 
	    'S' : '[SYMPTOM]',
	    'L':'[LOCATION]',
	    'P':'[PREVENTS]',
	    'D':'[DIAGNOSE_BY_TEST_OR_DRUG]',
	    'M':'[MANIFESTATION]',
	    'AW':'[ASSOCIATED_WITH]',
	    'PO':'[PART_OF]',
	    'OTH':'[OTHER]',
	    'T':'[TREATS]',
	    'NONE':'[NONE]',
	    'IA':'[IS_A]',
	    'SE':'[SIDE_EFFECT]',
	    'CI':'[CONTRAINDICATES]',
	    'D':'[DIAGNOSED_BY_TEST_OR_DRUG]'};
