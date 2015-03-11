function createTreeMap(jsondata) {

	var margin = {top: 40, right: 10, bottom: 10, left: 10},
	    width = 960 - margin.left - margin.right,
	    height = 500 - margin.top - margin.bottom;

	var color = d3.scale.category20c();
	var margin = {top: 5, right: 5, bottom: 5, left: 5};
	var width = document.getElementById('treeMap').offsetWidth;
	var height = window.innerHeight*0.8;


	var treemap = d3.layout.treemap()
	    .size([width, height])
	    .sticky(true)
	    .value(function(d) { return d.value; });

	var div = d3.select("#treeMap").append("div")
	    .style("position", "relative")
	    .style("width", width + "px")
	    .style("height", height + "px")
	    .style("left", margin.left + "%")
	    .style("top", margin.top + "%");

	    
	   jsondata = JSON.parse( jsondata );
	  
	    var node = div.datum(jsondata).selectAll(".node")
      .data(treemap.nodes)
  	  .enter().append("div")
      .attr("class", "node")
      .call(position)
      .style("background", function(d) { return d.children ? color(d.name) : null; })
      .text(function(d) { return d.children ? null : d.name + ";\n occurrence : " + d.value ; });


	function position() {
	  this.style("left", function(d) { return d.x + "px"; })
	      .style("top", function(d) { return d.y + "px"; })
	      .style("width", function(d) { return Math.max(0, d.dx - 1) + "px"; })
	      .style("height", function(d) { return Math.max(0, d.dy - 1) + "px"; });
	}

}