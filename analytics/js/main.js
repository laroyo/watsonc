
function myToggle(id){

//$('#SE-icon').click(function(){    
    //alert('--'+$('#SE-icon').html()+'--');
    if($('#'+id+'-icon').html() == ' + ')
	$('#'+id+'-icon').html(' - ')
    else 
	$('#'+id+'-icon').html(' + ')
    
    $('.'+id+'-row').toggle();
//});   
}