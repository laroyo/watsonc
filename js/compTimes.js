d3.csv("dataproc/res.csv", function(times) {

    cosvals = [];
    agrvals = [];

    count = 0; 
    times.map(function(d){	 
    	count += 1; 
    	cosvals.push({x : d["Int"], y: d['cos'],size: 5,shape: "circle",cy: 1})	
    	agrvals.push({x : d["Int"], y: d['agr'],size: 5,shape: "circle",cy: 1})
    });
    //alert(tvals);
    test_data = [{
    	key: "Agr",
    	values: agrvals, 	
    }, {key: "Cos",
	values: cosvals,
    }];

    nv.addGraph(function() {  
	var chart = nv.models.lineChart()
	    .forceY([0,20]);
    
	chart.xAxis
            .axisLabel('Value')
            .tickFormat(d3.format(',r'));
	
	chart.yAxis
            .axisLabel('Frequency');
	
	d3.select('#overview svg')
	.datum(test_data)
	    .transition().duration(500)
	    .call(chart);
	
	nv.utils.windowResize(function() { d3.select('#overview svg').call(chart) });
	
    }); 
    return chart;
 });



