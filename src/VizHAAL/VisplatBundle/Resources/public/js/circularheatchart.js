function createCircularHeatChart(labels, data) {

	var chart = circularHeatChart()
	    .innerRadius(window.innerHeight*0.6/(2*labels.length))
	    .range(["white", "red"])
	    .radialLabels(labels)
	    .segmentLabels(["Midnight", "1am", "2am", "3am", "4am", "5am", "6am", "7am", "8am", "9am", "10am",
	    "11am", "Midday", "1pm", "2pm", "3pm", "4pm", "5pm", "6pm", "7pm", "8pm", "9pm", "10pm", "11pm"]);

	var w = document.getElementById("circularHeatChart").offsetWidth;
	var h = window.innerHeight*0.8;
	d3.select('#circularHeatChart')
	    .selectAll('svg')
	    .data([data])
	    .enter()
	    .append('svg')
	    .attr("width", w)           //set the width and height of our visualization (these will be attributes of the <svg> tag
        .attr("height", h)
        //.attr("margin-left", w*0.2 + "%")
        //.attr("transform", "translate(" + (w * 0.3) + "," + 0 + ")")
	    //.attr("viewBox", "0 0 " + w * 0.6 + " " + h)
        //.attr("preserveAspectRatio", "xMidYMid")
	    .call(chart);

	//var circ_heat = document.getElementsByClassName("circularHeatChart");
	//circ_heat.width = document.getElementById("circularHeatChart").offsetWidth;
}