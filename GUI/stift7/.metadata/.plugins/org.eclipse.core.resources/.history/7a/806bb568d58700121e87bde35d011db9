function populateFiles(data) {
  for (var i = 0; i < data.length; i++) {
  //	var aa = data[i].filename;
  	$("#filearea").append("<div class='fileline'>"+data[i].filename+"</div>")
  }
  $( "#tabs" ).tabs();
}

$(document).ready(function() {
	//alert("hello hui");
	$.getJSON('services/getFiles.php',populateFiles)
	
})


$(document).ready(function() {
  $("#jobarea").load("oana/index.php");
});


/*function csvInput(data) {
	$("#historyarea").append("<div class='columnline'>"+"File ID" + "  " 
			+"Created Date" + "  "
			+"File ID" + "  "
			+"File Name" + "  "
			+"Job title" + "  "
			+"Judgement Per Unit" + "  "
			+"Max Judgement Per Worker" + "  "
			+"Units Per Assignment" + "  "                     
            + "</div>")
	  for (var i = 0; i < data.length; i++) {
	  	$("#historyarea").append("<div class='rowline'>"+data[i].file_id + "  " 
	  			+data[i].created_date + "  " 
	  			+data[i].file_name + "  " 
	  			+data[i].job_title + "  " 
	  			+data[i].judgement_per_unit + "  " 
	  			+data[i].max_judgement_per_worker + "  " 
	  			+data[i].units_per_assignment + "  "                              	  		                           	  			                            	  			                             
	  			+ "</div>")
	  }
	  $( "#tabs" ).tabs();
	}

	$(document).ready(function() {
		$.getJSON('historyOld.php', csvInput)		
	})
*/	
	
	

$(document).ready(function() {
	   $("#historytable").tablesorter();
	   $("#historytable > td").each( function(){ 
		    $(this).click( function(){
		    	alert("hello hui");
		    });
		});
	})

	
