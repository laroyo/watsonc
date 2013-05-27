ids = [];
rels = ["D","S","C","M","L","AW","P","SE","IA","PO","T","CI"]; 
relations = []; 
for (var i = 0; i < rels.length; i++){  
    relations[rels[i]] = []; 
}    
sents.map( function(d){
    
    ids.push(d["sentence_id"]); 
    for (var i = 0; i < rels.length; i++){
	relations[rels[i]].push(parseInt(d[rels[i]])); 
    }
});  
r = []; 
for (var i = 0;i < rels.length; i++)
{   
    r[rels[i]] = []; 
    for (var j =0; j<ids.length; j++)
    {
	r[rels[i]].push({ x : parseInt(ids[j]), y : relations[rels[i]][j]})			  
    }
}	

var data = []; 
for (var i = 0; i < rels.length; i++){
    data.push({key : rels[i], values : r[rels[i]]}); 
}

nv.addGraph(function() {
    chart = nv.models.multiBarChart()
	.stacked(true)
	.showControls(false); //.barColor(d3.scale.category20().range());
    
    chart.multibar
	.hideable(true);	 
    
    d3.select('#chart1 svg')
        .datum(data)
	.transition().duration(500).call(chart);
    
    nv.utils.windowResize(chart.update);
    d3.selectAll(".nv-bar").on("click", function (d) {
	//alert(d.x+" "+d.y+" "+d.y0 + " "+ d.series+" "+d.size+" "+d.y1);
	window.open("sentenceAnalytics.php?sentence_id="+d.x,"_self");
    });
    return chart;
});