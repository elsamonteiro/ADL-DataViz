function createHeatMap(mapUrl, data, details) {

	var myElement = document.querySelector("#heatMapContainer");//.children[0];
	var datapoints = data;
  	//function processus() {
  	//var canvas = document.getElementById('heatMapContainer').children[0];
  	var canvas = document.getElementById('imageCanvas');
  	var wrapper = document.getElementById('heatMapContainerWrapper');
  	//<canvas id="imageCanvas" style="position:absolute; z-index:1; left:0px; top:0px"></canvas>
	context = canvas.getContext('2d');

	base_image = new Image();
	base_image.src = "../../../" + mapUrl.substring(9);
	//var imageData = canvas.toDataUrl();
	base_image.onload = function() {
		//if(base_image.width > 1000 || base_image.height > 1000 ) {
		//	alert("Error : Image is large");
		//	return;
		//}else if(base_image.width > wrapper.offsetWidth || base_image.height > myElement.innerHeight ) {
		//canvas.height = base_image.height/2;
		//alert(window.innerHeight);
		var ratio = Math.min(wrapper.offsetWidth/base_image.width, window.innerHeight*0.8/base_image.height)
		canvas.width = base_image.width*ratio;
		canvas.height = base_image.height*ratio;
		var width = canvas.width;
		var height = canvas.height;
		myElement.style.width = width + "px";
		myElement.style.height = height + "px";
		//var imageData = canvas.toDataUrl();
		//alert(imageData);
		//context.globalAlpha = 0.5;
		context.drawImage(base_image,0,0, width, height);
		for (var i = 0; i < data.length; i++) {
			datapoints[i]['x'] = datapoints[i]['x']*ratio;
			datapoints[i]['y'] = datapoints[i]['y']*ratio;
		}
			//var imageHeatMap = new Image();
			//imageHeatMap.src = imageData;
			//context.drawImage(imageHeatMap,0,0);
			//context.putImageData(imageData, 200, 200);
			
		//}else {
		//	canvas.height = base_image.height;
		//	canvas.width = base_image.width;
		//	var width = base_image.width;
		//	var height = base_image.height;
		//	myElement.style.width = width + "px";
		//	myElement.style.height = height + "px";
			//var imageData = canvas.toDataUrl();
			//alert(imageData);
			//context.globalAlpha = 0.5;
		//	context.drawImage(base_image,0,0);

			//var imageHeatMap = new Image();
			//imageHeatMap.src = imageData;
			//context.drawImage(imageHeatMap,0,0);
			//context.putImageData(imageData, 200, 200);
			
		//}
		process(ratio);
	}
	//}
	//setTimeout(function () {processus()}, 1000);
  	//context.drawImage(base_image, base_image.width, base_image.height);
  	function process(ratio) {

  		var heatmap = h337.create({
		  	container: document.getElementById('heatMapContainer'),
		  	radius: 50*ratio,
	  		maxOpacity: 1,
			minOpacity: 0.5,
		  	blur: .75,
		  	gradient: {
			// enter n keys between 0 and 1 here
			// for gradient color customization
			'.5': 'blue',
			'.8': 'yellow',
			'.95': 'red'
				}
	  		});

	  	var maxValue = 0;
	  	for (var i = 0; i < datapoints.length; i++) {
	  		if (datapoints[i]['value'] > maxValue) maxValue = datapoints[i]['value'];
	  	}

	  	heatmap.setData({
		    max: maxValue,
		    min: 0,
		    data: datapoints
			});
  	}
  	//setTimeout(function () {process()}, 1500);

  	   // -------------------------------------------------------------------------------------------------------------------------------
    // CREATION OF TABLE FOR DETAILS
    function tabulate(data, columns) {


    var sortFrequencyAscending = function (a, b) { return frequencyFunc(a) - frequencyFunc(b) }
    var sortFrequencyDescending = function (a, b) { return frequencyFunc(b) - frequencyFunc(a) }
	var sortDurationAscending = function (a, b) { return durationFunc(a) - durationFunc(b) }
    var sortDurationDescending = function (a, b) { return durationFunc(b) - durationFunc(a) }
    var sortEventAscending = function (a, b) { return eventFunc(a).localeCompare(eventFunc(b)); }
    var sortEventDescending = function (a, b) { return eventFunc(b).localeCompare(eventFunc(a)); }
    var frequencyAscending = true;
	var durationAscending = true;
    var eventAscending = true;
	
	var frequencyFunc = function(data) {
		var freqdetails = []
		for (var i = 0; i < data.length; i++) {
			freqdetails = detaildata[i]['Frequency'];
		} 
    return freqdetails;
	}
	
	var durationFunc = function(data) {
		var durationdetails = []
		for (var i = 0; i < data.length; i++) {
			durationdetails = detaildata[i]['AvgDuration'];
		} 
    return durationdetails;
	}
 
	var eventFunc = function(data) {
		var sensordetails = []
		for (var i = 0; i < data.length; i++) {
			sensordetails = detaildata[i]['Sensor'];
		} 
    return sensordetails;
	}
		var w = window.innerWidth,          //width
        h = window.innerHeight * 2 / 3;      		//height
	
        var table = d3.select("#heatmapTable")
                .append("table")
                .attr("width", document.getElementById("heatmapTable").offsetWidth)
                .attr("height", h)
                .style("max-height", '400px')
                // Make it responsive.
                .attr("viewBox", "0 0 " + w * 0.6 + " " + h)
                .attr("preserveAspectRatio", "xMidYMid")
                .attr("class", "resizeTable"),

            thead = table.append("thead"),
            tbody = table.append("tbody");


        // append the header row
        thead.append("tr")
            .selectAll("th")
            .data(columns)
            .enter()
            .append("th")
            .text(function (column) {
                return column;
            })
			.on("click", function (d) {
			var sort;
			
            // Choose appropriate sorting function.
            if (d === "Frequency") {
                if (frequencyAscending) sort = sortFrequencyAscending;
                else sort = sortFrequencyDescending;
                frequencyAscending = !frequencyAscending;
            } else if(d === "AvgDuration") {
                if (durationAscending) sort = sortDurationAscending;
                else sort = sortDurationDescending;
                durationAscending = !durationAscending;
            }else if(d === "Sensor") {
                if (eventAscending) sort = sortEventAscending;
                else sort = sortEventDescending;
                eventAscending = !eventAscending;
            }
			
			var rows = tbody.selectAll("tr").sort(sort);
        });

        // create a row for each object in the data
        var rows = tbody.selectAll("tr")
            .data(data)
            .enter()
            .append("tr");

        // create a cell in each row for each column
        var cells = rows.selectAll("td")
            .data(function (row) {
                return columns.map(function (column) {
                    return {column: column, value: row[column]};
                });
            })
            .enter()
            .append("td")
            .text(function (d) {
                if (d.column == "AvgDuration")return Math.round(d.value) + " sec.";
                else return d.value;
            });

        return table;
    }

    // render the table
    var activities = tabulate(details, ["Sensor", "Frequency", "AvgDuration"]);

    // uppercase the column headers
    activities.selectAll("thead th")
        .text(function (column) {
            return column.charAt(0).toUpperCase() + column.substr(1);
        });
}