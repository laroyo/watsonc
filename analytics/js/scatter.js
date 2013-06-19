function addScatterPlot(data,container,legend){
    nv.addGraph(function() {
	var chart = nv.models.scatterChart()
            .showDistX(true)
            .showDistY(true)
            .color(d3.scale.category10().range());

	
	
	chart.xAxis.tickFormat(d3.format('.02f'));
	chart.yAxis.tickFormat(d3.format('d'));

	chart.xAxis.axisLabel('Time percentile');

	d3.select('#'+container+' svg')
	    .datum(data)
	    .transition().duration(500)
	    .call(chart);
		
	//nv.utils.windowResize(chart.update);
	
	return chart;
    });
}



/**

   var data = [{
   'key': 'Group 1',
   'values' : [
   {'x': 1,
   'y': 2,
   'size' : 1
   }]
   }]

**/