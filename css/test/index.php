<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.4.3.min.js"></script>
<link rel="stylesheet" type="text/css" href="normalize.css" />
<link rel="stylesheet" type="text/css" href="result-light.css" />

  <style type="text/css">
    
  </style>
  <!-- TODO: Missing CoffeeScript 2 -->

  <script type="text/javascript">


    $(function(){
      
/**
 * Create a global getSVG method that takes an array of charts as an argument. The SVG is returned as an argument in the callback.
 */
Highcharts.getSVG = function(charts, options, callback) {
    var svgArr = [],
    		top = 0,
        width = 0,
        i,
        svgResult = function (svgres) {
            var svg = svgres.replace('<svg', '<g transform="translate(0,' + top + ')" ');
            svg = svg.replace('</svg>', '</g>');
            top += charts[i].chartHeight;
            width = Math.max(width, charts[i].chartWidth);
            svgArr.push(svg);
            if (svgArr.length === charts.length) {
              callback('<svg height="'+ top +'" width="' + width + '" version="1.1" xmlns="http://www.w3.org/2000/svg">' + svgArr.join('') + '</svg>');
            }
        };
		for (i = 0; i < charts.length; ++i) {
				charts[i].getSVGForLocalExport(options, {}, function () { 
        	console.log("Failed to get SVG");
       	}, svgResult);
		}
};

/**
 * Create a global exportCharts method that takes an array of charts as an argument,
 * and exporting options as the second argument
 */
Highcharts.exportCharts = function(charts, options) {
		// Merge the options
    options = Highcharts.merge(Highcharts.getOptions().exporting, options);    
		
    var imageType = options && options.type || 'image/png';
  
		// Get SVG asynchronously and then download the resulting SVG
    Highcharts.getSVG(charts, options, function (svg) {
      Highcharts.downloadSVGLocal(svg,
        (options.filename || 'chart')  + '.' + (imageType === 'image/svg+xml' ? 'svg' : imageType.split('/')[1]),
        imageType,
        options.scale || 2,
        function () {
          console.log("Failed to export on client side");
        });
    });
};

var chart1 = new Highcharts.Chart({

    chart: {
        renderTo: 'container1'
    },

    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },

    series: [{
        data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]
    }]

});

var chart2 = new Highcharts.Chart({

    chart: {
        renderTo: 'container2',
        type: 'column'
    },

    xAxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },

    series: [{
        data: [176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4, 29.9, 71.5, 106.4, 129.2, 144.0]
    }]

});

$('#export').click(function() {
    Highcharts.exportCharts([chart1, chart2]);
});

    });

</script>

</head>
<body>
  <script src="highcharts.js"></script>
<script src="exporting.js"></script>
<script src="offline-exporting.js"></script>

<div id="container1" style="height: 200px"></div>
<div id="container2" style="height: 200px"></div>

<button id="export">Export all</button>

  
  <script>
    // tell the embed parent frame the height of the content
    if (window.parent && window.parent.parent){
      window.parent.parent.postMessage(["resultsFrame", {
        height: document.body.getBoundingClientRect().height,
        slug: "eatqca8n"
      }], "*")
    }
  </script>
</body>
</html>