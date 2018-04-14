<?php

require 'db.php';

$instrument_id = $_GET['instrument_id'];
$d = (isset($_GET['d']) ? $_GET['d'] : 5 );

$sql = "SELECT i.id, i.symbol, c.id as company_id, c.name, e.id as exchange_id, e.name as exchange_name, e.currency
        FROM instrument as i
        inner join company as c on i.company_id = c.id
        inner join exchange as e on i.exchange_id = e.id
        where i.id = " . $instrument_id . " order by e.id, c.name";

$result = db::getInstance()->get_result($sql);

print "Symbol: ".$result['symbol'] . "<BR>";
print "Company: ".$result['name']. "<BR>";

?>

<!DOCTYPE html>
<meta charset="utf-8">
<style>

    body {
        font: 10px sans-serif;
    }

    text {
        fill: #000;
    }

    path.candle {
        stroke: #000000;
    }

    path.candle.body {
        stroke-width: 0;
    }

    path.candle.up {
        fill: #00AA00;
        stroke: #00AA00;
    }

    path.candle.down {
        fill: #FF0000;
        stroke: #FF0000;
    }

    rect.pane {
        cursor: move;
        fill: none;
        pointer-events: all;
    }

   .grid line {
      stroke: lightgrey;
      stroke-opacity: 0.7;
      shape-rendering: crispEdges;
   }

   .grid path {
      stroke-width: 0;
   }

</style>
<body>
<script src="http://d3js.org/d3.v4.min.js"></script>
<script src="http://techanjs.org/techan.min.js"></script>
<script>

    var margin = {top: 20, right: 20, bottom: 30, left: 50},
            width = 960 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;

    //var parseDate = d3.timeParse("%d-%b-%y");
    var parseDate = d3.timeParse("%Y-%m-%d %H:%M:%S");


    var x = techan.scale.financetime()
            .range([0, width]);

    var y = d3.scaleLinear()
            .range([height, 0]);

    var zoom = d3.zoom()
            .on("zoom", zoomed);

    var zoomableInit;

    var candlestick = techan.plot.candlestick()
            .xScale(x)
            .yScale(y);

    var xAxis = d3.axisBottom(x);

    var yAxis = d3.axisLeft(y);

    var svg = d3.select("body").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    // gridlines in x axis function
    function make_x_gridlines() {		
    	return d3.axisBottom(x)
		.ticks(5)
    }

    // gridlines in y axis function
    function make_y_gridlines() {		
        return d3.axisLeft(y)
		.ticks(5)
    }

    svg.append("clipPath")
            .attr("id", "clip")
        .append("rect")
            .attr("x", 0)
            .attr("y", y(1))
            .attr("width", width)
            .attr("height", y(0) - y(1));

    svg.append("g")
            .attr("class", "candlestick")
            .attr("clip-path", "url(#clip)");

    svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")");

    svg.append("g")
            .attr("class", "y axis")
        .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("Price (<?=$result['currency'];?>)");

    svg.append("rect")
            .attr("class", "pane")
            .attr("width", width)
            .attr("height", height)
            .call(zoom);

    //var result = d3.csv("data.csv", function(error, data) {
    var result = d3.json("quentin.php?instrument_id=<?=$instrument_id;?>&d=<?=$d;?>", function(error, data) {
        var accessor = candlestick.accessor();

	data = data.map(function(d) {
    indicatorSelection.append("g")
            .attr("class", "indicator-plot")
            .attr("clip-path", function(d, i) { return "url(#indicatorClip-" + i + ")"; });

    // Add trendlines and other interactions last to be above zoom pane
    svg.append('g')
            .attr("class", "crosshair ohlc");

    svg.append("g")
            .attr("class", "tradearrow")
            .attr("clip-path", "url(#ohlcClip)");

    svg.append('g')
            .attr("class", "crosshair macd");

    svg.append('g')
            .attr("class", "crosshair rsi");

    svg.append("g")
            .attr("class", "trendlines analysis")
            .attr("clip-path", "url(#ohlcClip)");
    svg.append("g")
            .attr("class", "supstances analysis")
            .attr("clip-path", "url(#ohlcClip)");

    d3.select("button").on("click", reset);

    /*d3.csv("data.csv", function(error, data) {*/
    d3.json("quentin.php?instrument_id=<?echo $instrument_id;?>&t=<?=$t;?>", function(error, data) {
        var accessor = candlestick.accessor(),
            indicatorPreRoll = 33;  // Don't show where indicators don't have data

        data = data.map(function(d) {
            return {
                date: parseDate(d.price_date),
                open: +d.open_price,
                high: +d.high_price,
                low: +d.low_price,
                close: +d.close_price,
                volume: +d.volume
            };
        }).sort(function(a, b) { return d3.ascending(accessor.d(a), accessor.d(b)); });

        /* 
	data = data.slice(0, 200).map(function(d) {
            return {
                date: parseDate(d.Date),
                open: +d.Open,
                high: +d.High,
                low: +d.Low,
                close: +d.Close,
                volume: +d.Volume
            };
        }).sort(function(a, b) { return d3.ascending(accessor.d(a), accessor.d(b)); });
	*/

        x.domain(data.map(accessor.d));
        y.domain(techan.scale.plot.ohlc(data, accessor).domain());

	// add the X gridlines
	svg.append("g")			
        .attr("class", "grid")
        .attr("transform", "translate(0," + height + ")")
        .call(make_x_gridlines()
          .tickSize(-height)
          .tickFormat("")
        )

        // add the Y gridlines
        svg.append("g")			
        .attr("class", "grid")
        .call(make_y_gridlines()
          .tickSize(-width)
          .tickFormat("")
        )

        svg.select("g.candlestick").datum(data);
        draw();

        // Associate the zoom with the scale after a domain has been applied
        // Stash initial settings to store as baseline for zooming
        zoomableInit = x.zoomable().clamp(true).copy();
    });

    function zoomed() {
        var rescaledY = d3.event.transform.rescaleY(y);
        yAxis.scale(rescaledY);
        candlestick.yScale(rescaledY);

        // Emulates D3 behaviour, required for financetime due to secondary zoomable scale
        x.zoomable().domain(d3.event.transform.rescaleX(zoomableInit).domain());

        draw();
    }

    function draw() {
        svg.select("g.candlestick").call(candlestick);
        // using refresh method is more efficient as it does not perform any data joins
        // Use this if underlying data is not changing
//        svg.select("g.candlestick").call(candlestick.refresh);
        svg.select("g.x.axis").call(xAxis);
        svg.select("g.y.axis").call(yAxis)
    }

</script>
