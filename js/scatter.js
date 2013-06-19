var mdata = [{key : "group 1", 
	     values : [{x: 265614932, y: 1, size: 5},
		       {x: 265614932, y: 2, size: 5},
		       {x: 265614933, y: 3, size: 5},
		       {x: 265614933, y: 4, size: 5},
		      ]
	    }];

nv.addGraph(function() {
   var chart = nv.models.scatterChart()
                  .showDistX(true)
                  .showDistY(true)
                  .color(d3.scale.category10().range());
  
   
  
   d3.select('#chart1 svg')
       .datum(mdata)
    .transition().duration(500)
       .call(chart);

   nv.utils.windowResize(chart.update);
 
   return chart;
});
