// The annotations of the job are retrieved from the database in the variable 'sents' as an array of {'sentence_id': id, <relation> : <numOccurencesRelation>}
// This structure is adapted to fit the one used by nvd3.js by default to build the barchart. 

ids = [];
relLabels = ["D","S","C","M","L","AW","P","SE","IA","PO","T","CI"]; 
relOccurences = []; 
for (var i = 0; i < relLabels.length; i++){  
    relOccurences[relLabels[i]] = []; 
}    

sents.map( function(d){
    
    ids.push(d["sentence_id"]); 
    for (var i = 0; i < relLabels.length; i++){
	relOccurences[relLabels[i]].push(parseInt(d[relLabels[i]])); 
    }
});  

//Build an array of {x (sentence_id), y: [rel : numLabelsForRelation]}
var xyValues = []; 
for (var i = 0;i < relLabels.length; i++)
{   
    xyValues[relLabels[i]] = []; 
    for (var j =0; j<ids.length; j++)
    {
	xyValues[relLabels[i]].push({ x : parseInt(ids[j]), y : relOccurences[relLabels[i]][j]})			  
    }
}	

var data = []; 
for (var i = 0; i < relLabels.length; i++){
    data.push({key : relLabels[i], values : xyValues[relLabels[i]]}); 
}

var int = d3.format("d");


nv.addGraph(function() {
    chart = nv.models.multiBarChart()
	.stacked(true)
        .tooltipContent(function(key, y,e,graph) { return '<h4>' + abbr[key] + '</h4>' +
						   '<p>' +  int(e)+' worker(s) in sentence '+y + '</p>';})
	.showControls(false); //.barColor(d3.scale.category20().range());
    
    
    chart.multibar
	.hideable(true);	 

    chart.yAxis
	.tickFormat(d3.format('d'));

    
    //chart.xAxis.staggerLabels(true);    
    
    d3.select('#chart1 svg')
        .datum(data)
	.transition().duration(500).call(chart);
    
    nv.utils.windowResize(chart.update);
    d3.selectAll(".nv-bar").on("click", function (d) {
	window.open("/wcs/analytics/sentence.php?sentence_id="+d.x,"_self");
    });
    return chart;
});