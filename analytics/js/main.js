//  Toggle function for the relation distribution table. 
function relTableToggle(id){

    if($('#'+id+'-icon').html() == ' + ')
	$('#'+id+'-icon').html(' - ')
    else 
	$('#'+id+'-icon').html(' + ')
    
    $('.'+id+'-row').toggle();
}