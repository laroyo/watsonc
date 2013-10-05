/**
 * Generic util  functions for the analytics. To be loaded for every analytics page (included in the header). 
 **/

/**
 * Returns the position of the object in arr that contains whose value for the key {key} is {value}.  
 * @returns {pos} The position of the object in the array, or -1 if there's no object with that value. 
 **/

function getByValue(arr, key, value) {
    for (var i=0; i<arr.length; i++) {	
	if (arr[i][key] == value) return i;
    }
    return -1; 
}

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

//For each entity, an analytics page. 
var entityTypes = ['sentence', 'worker', 'job', 'relation']; 

// Dinamically loads an analytics page. Equivalent to do a redirection. 
//@param {entityType}  A valid entity type. 
//@param {entityID} A numeric identifier for the entity to be loaded. 
function loadAnalyticsPage(entityType,entityID){
    
    if($.inArray(entityType, entityTypes) == -1)
	throw "Invalid entityType"; 
    
    window.location.href = "/wcs/analytics/"+entityType+'.php?'+entityType+'_id='+entityID;    
}
