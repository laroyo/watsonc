<body>
<style>
    body{
	padding-left: 10px;
    }
</style>

<h3>Sentence <?= $sentence_id ?></h3>

<div class='row'>
<div class='span1'>
</div>
<div class='span8'>
    <pre><?= $sentence_text ?></pre>
</div>
</div> <!-- /row-->

The sentence has been annotated in the following jobs: 


<div class='row'>
    <div class='span8' id='container' style='border: 1px solid;'>
    </div>
</div>
<div class='row'>
    <div class='span4'>
    <button class='btn' id='normButton'>Normalize</button>
    </div>
</div>


<h4>Overall Metrics</h4>

Overall values, computed across all jobs. 

<p><span alt=" is the core crowd truth metric for relation extraction.  It is measured for each relation on each sentence as the cosine of the unit vector for the relation with the sentence vector.  The relation score is used for training and evaluation of the relation extraction system, it is viewed as the probability that the sentence expresses the relation.  This is a fundamental shift from the traditional approach, in which sentences are simply labelled as expressing, or not, the relation, and presents new challenges for the evaluation metric and especially for training"><b>Sentence-relation score</b></span> 0.0</p>
<p><span alt="is defined for each sentence as the max relation score for that sentence. If all the workers selected the same relation for a sentence, the max relation score will be 1, indicating a clear sentence.   In Figure \ref{fig:crowd-annotations-final}, sentence 735 has a clarity score of 1, whereas sentence 736 has a clarity score of 0.61, indicating a confusing or ambiguous sentence. Sentence clarity is used to weight sentences in training and evaluation of the relation extraction system, since annotators have a hard time classifying them, the machine should not be penalized as much for getting it wrong in evaluation, nor should it treat such training examples as exemplars."><b>Sentence clarity</b> 0.0</span></p>



<script>

/*modified from Mike Bostock at http://bl.ocks.org/3943967 */

var data = [    
    {'unit_id' : 8421, 'D' : 4,'S' : 12,'C': 4,'M' : 10 ,'L': 1,'AW': 1,'P':2,'SE': 3,'IA':2,'PO':1,'T':1,'CI':2,'OTH':2,"NONE":1, 'numAnnotators': 15, 'sum': 46, 'sentClarity' : 0.2},
    {'unit_id' : 8422, 'D' : 4,'S' : 12,'C': 4,'M' : 10 ,'L': 1,'AW': 1,'P':2,'SE': 3,'IA':0,'PO':0,'T':0,'CI':0,'OTH':0,"NONE":0, 'numAnnotators': 12, 'sum': 37, 'sentClarity' : 0.3},
    {'unit_id' : 8423, 'D' : 6,'S' : 14,'C': 14,'M' : 14 ,'L': 20,'AW': 4,'P':8,'SE': 14,'IA':0,'PO':0,'T':0,'CI':0,'OTH':0,"NONE":0, 'numAnnotators': 15, 'sum': 94, 'sentClarity' : 0.4}
    //{'unit_id' : 8424, 'D' : 4,'S' : 12,'C': 4,'M' : 10 ,'L': 1,'AW': 1,'P':2,'SE': 3,'IA':2,'PO':1,'T':1,'CI':2,'OTH':2,"NONE":1, 'sum': 46, 'sentClarity': 0.5},
    //{'unit_id' : 8425, 'D' : 4,'S' : 12,'C': 4,'M' : 10 ,'L': 1,'AW': 1,'P':2,'SE': 3,'IA':2,'PO':1,'T':1,'CI':2,'OTH':2,"NONE":1, 'sum': 46, 'sentClarity': 0.6}
];

var max = 0; 

for(var i = 0; i < data.length; i++){
    if (data[i].sum > max)
	max = data[i].sum;         
}

var relations = ['D','S','C','M','L','AW','P','SE','IA','PO','T','CI','OTH',"NONE"]

//var relations = ["D","C","AW","P","SE","T","CI","OTH"]; 

var margin = {top: 40, right: 25, bottom: 5, left: 50},
width = 470 - margin.left - margin.right,
height = 100 - margin.top - margin.bottom;

// Initially, the graph won't be normalized. 
var normalize = false;  

var n = relations.length, // number of layers
m = data.length, // number of samples per layer
stack = d3.layout.stack(),
labels = data.map(function(d) {return d.unit_id;}),

//go through each layer (elem11, elem2 etc, that's the range(n) part)
//then go through each object in data and pull out that objects's population data
//and put it into an array where x is the index and y is the number
layers = stack(d3.range(n).map(function(d) { 
    var a = [];
    for (var i = 0; i < m; ++i) {
        a[i] = {x: i, y: data[i][relations[d]], rel: relations[d]};  
    }
    
    return a;
}));

//the largest single layer
yGroupMax = d3.max(layers, function(layer) { return d3.max(layer, function(d) { return d.y; }); }),
//the largest stack
yStackMax = d3.max(layers, function(layer) { return d3.max(layer, function(d) { return d.y0 + d.y; }); });


var y = d3.scale.ordinal()
    .domain(d3.range(m))
    .rangeRoundBands([2, height], .08);

var x = d3.scale.linear()
    .domain([0, yStackMax])
    .range([0, width]);



function normalizeX(value, dom){    
    var norm = d3.scale.linear().domain([0,dom]).range([0,width]); 
    return norm(value);
}

var color = d3.scale.category20b();
 
var chart = d3.select('#container').append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
 
var layer = chart.selectAll(".layer")
    .data(layers)
  .enter().append("g")
    .attr("class", "layer")
    .style("fill", function(d, i) { return color(i); });


layer.selectAll("rect")
    .data(function(d) { return d; })
    .enter().append("rect")
    .attr("y", function(d) {return y(d.x); })
    .attr("x", function(d) { return (normalize ? normalizeX(d.y0, data[d.x].sum) :  x(d.y0)) })
    .attr("height", y.rangeBand())
    .attr("width", function(d) { return (normalize ? normalizeX(d.y, data[d.x].sum) : x(d.y))})
    .attr("data-toggle", "tooltip")
    .attr('class','rectooltip')
    .attr('title', function(d) {return d.rel})
    .on('click', function(d) { loadAnalyticsPage('relation', d.rel); }); 

var yAxis = d3.svg.axis()
    .scale(y)
    .tickSize(1)
    .tickPadding(6)
    .tickValues(labels)
    .orient("left");

 chart.append("g")
    .attr("class", "yaxis")
    .call(yAxis);

// Make the job ids in the y axis clickable (so it redirects to the job page). 
d3.select('.yaxis').selectAll("text")
    .attr("data-toggle", "tooltip")
    .attr('class','axistooltip')
    .attr('title', function(d) {return "Annotated by "+ (data[getByValue(data, 'unit_id', d)].numAnnotators) +' workers'})
    .on('click',function(d){ loadAnalyticsPage('job',d)});

function sentClarity (i, normalized){
    if(normalized)
	return ({x: width + 27, y: y(width * (i+1)), sentClarity : data[i].sentClarity});      	
    else
	return ({x: (data[i].sum / max) * width +27, y: y(width * (i+1)), sentClarity : data[i].sentClarity});      		
}

var sClarity = []; 

for(var i = 0; i < data.length; i++){
    sClarity[i] = sentClarity(i,normalize);
}

chart.selectAll(".bar")
    .data(sClarity)
    .enter().append("text")
    .attr("class", "bar")
    .attr("x", function(d) {return d.x- 5})
    .attr("y", function(d) {return d.y})
    .attr("dx", -3) // padding-right
    .attr("dy", ".9em") // vertical-align: middle
    .attr("text-anchor", "end") // text-align: right
    .attr("font-size", "11px") // text-align: right
    .attr('fill', 'black')
    .text(function(d) {return d.sentClarity})
    .attr("data-toggle", "tooltip")
    .attr('class','sclaritytooltip')
    .attr('title', "Sentence Clarity");

</script>
<script type="text/javascript">

function normalizeGraph(norm){
    if(norm) {
	
	d3.selectAll("rect").transition()    
	    .duration(500)
            .attr("x", function(d) {return normalizeX(d.y0, data[d.x].sum); })
            .attr("width", function(d) { return normalizeX(d.y, data[d.x].sum);})
    } else  {	      
	d3.selectAll("rect").transition()    
	    .duration(500)
	    .attr("x", function(d) { return x(d.y0) })
	    .attr("width", function(d) { return x(d.y); });
	
    }
    d3.selectAll('.sclaritytooltip').transition()
	.attr("x", function(d,i) {return sentClarity(i,norm).x});
}

$('#normButton').click(function(){
    
    if($('#normButton').text() == 'Normalize'){
	$('#normButton').text('(De) - Normalize ');
	normalizeGraph(true);
    } else {
	$('#normButton').text('Normalize');
	normalizeGraph(false);
    }
});

$('.rectooltip').tooltip({'container': 'body', 'placement': 'bottom'});
$('.sclaritytooltip').tooltip({'container': 'body', 'placement': 'bottom'});
$('.axistooltip').tooltip({'container': 'body', 'placement': 'bottom'});


</script>
</body>

