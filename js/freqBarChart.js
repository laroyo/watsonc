d3.csv("rowsums.csv", function(freqs) {

    freqs.map( function(d){
	alert(d[1]); 
    });     
}