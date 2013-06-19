function addLinePlusBar(data,container,legend) {

    nv.addGraph(function() {    
	chart = nv.models.linePlusBarChart()
            .margin({top: 30, right: 60, bottom: 50, left: 70})
	    .x(function(d,i) {return i})
            .color(d3.scale.category10().range());	    
	
	chart.xAxis.tickFormat(function(d) {return (d)});
		
	chart.y1Axis
            .tickFormat(d3.format(',f'));
	
	
	//chart.bars.forceY([0]);
	chart.lines.forceY([0,1]);
	
	d3.select('#'+container+' svg')
            .datum(data)
	    .transition().duration(500).call(chart);
	
	nv.utils.windowResize(chart.update);
    
	return chart;
    });
    if(legend){
	d3.select('#'+container+' svg')
	    .append("text")
	    .attr("x", 170)             
	    .attr("y", 280)
	    .attr("text-anchor", "middle")  
	    .text(legend);
    }
}


