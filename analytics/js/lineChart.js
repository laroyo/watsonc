function addLineChart(container,data,maxValue){
    nv.addGraph(function() {      
	//alert(Math.max(data)); 
	//alert(Object.keys()); 
	if(maxValue > 100){
	    forceY = 200;
	} else {
	    forceY = 1; 
	}
	var chart = nv.models.lineChart().forceY([0,forceY]);
	
	chart.xAxis
            .axisLabel('Value')
            .tickFormat(d3.format(',r'));
	
	chart.yAxis
            .axisLabel('Frequency');
	//alert(Object.keys(chart.yAxis)); 
	
	d3.select(container)
	    .datum(data)
	    .transition().duration(500)
	    .call(chart);
	
	nv.utils.windowResize(function() { d3.select('#overview svg').call(chart) });
	return chart; 
    }); 
}