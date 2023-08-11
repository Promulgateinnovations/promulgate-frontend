
// Sample initial data for the donut chart
var chart = am4core.create("chartdiv", am4charts.PieChart);

// Add data
chart.data = [{
  "country": "D1",
  "value": 501.9
}, {
  "country": "D2",
  "value": 301.9
}, {
  "country": "D3",
  "value": 201.1
}, {
  "country": "D4",
  "value": 165.8
}, {
  "country": "D5",
  "value": 139.9
}, {
  "country": "D6",
  "value": 128.3
}];

// Add and configure Series
var pieSeries = chart.series.push(new am4charts.PieSeries());
pieSeries.dataFields.value = "value";
pieSeries.dataFields.category = "country";
pieSeries.labels.template.disabled = true;
pieSeries.ticks.template.disabled = true;

chart.legend = new am4charts.Legend();
chart.legend.position = "right";

chart.innerRadius = am4core.percent(60);

var label = pieSeries.createChild(am4core.Label);
label.text = "Japan";
label.horizontalCenter = "middle";
label.verticalCenter = "middle";
label.fontSize = 20;
if (chart.logo) { chart.logo.disabled = true; }


// Sample data for the line chart
var data = {
  labels: ['January', 'February', 'March', 'April', 'May', 'June'],
  datasets: [{
    label: 'Sample Data',
    data: [10, 15, 20, 18, 25, 30], // Data points for the chart
    borderColor: 'rgba(75, 192, 192, 1)', // Line color
    borderWidth: 2, // Line width
    fill: false // Disable area fill under the line
  }]

};

// Get the canvas element and create the line chart
var ctx = document.getElementById('lineChart').getContext('2d');
var lineChart = new Chart(ctx, {
  type: 'line',
  data: data,
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        display: true,
        title: {
          display: true,
          text: 'Month'
        }
      },
      y: {
        display: true,
        title: {
          display: true,
          text: 'Value'
        }
      }
    },
    plugins: {
      legend: {
        display: true,
        position: 'top'
      }
    }
  }
});
am4core.useTheme(am4themes_animated);
// Themes end

// create chart
var chart = am4core.create("chartdiv1", am4charts.GaugeChart);
chart.innerRadius = am4core.percent(80);

// Normal axis
var axis = chart.xAxes.push(new am4charts.ValueAxis());
axis.min = 0;
axis.max = 100;
axis.strictMinMax = true;
axis.renderer.grid.template.disabled = true;

// Axis for ranges
var colorSet = new am4core.ColorSet();

var range0 = axis.axisRanges.create();
range0.value = 0;
range0.endValue = 50;
range0.axisFill.fillOpacity = 1;
range0.axisFill.fill = colorSet.getIndex(15);
range0.fill = am4core.color("green");

var range1 = axis.axisRanges.create();
range1.value = 50;
range1.endValue = 100;
range1.axisFill.fillOpacity = 1;
range1.fill = am4core.color("red");
range1.axisFill.fill = colorSet.getIndex(9);


// Main label
var label = chart.radarContainer.createChild(am4core.Label);
label.isMeasured = false;
label.fontSize = 25;
label.x = am4core.percent(50);
label.y = am4core.percent(100);
label.horizontalCenter = "middle";
label.verticalCenter = "bottom";
label.text = "50%";

// Hand
var hand = chart.hands.push(new am4charts.ClockHand());
hand.axis = axis;
hand.innerRadius = am4core.percent(60);
hand.startWidth = 5;
hand.pin.disabled = true;
hand.value = 50;

hand.events.on("propertychanged", function (ev) {
  range0.endValue = ev.target.value;
  range1.value = ev.target.value;
  axis.invalidate();
});

setInterval(function () {
  var value = Math.round(Math.random() * 100);
  label.text = value + "%";
  var animation = new am4core.Animation(hand, {
    property: "value",
    to: value
  }, 1000, am4core.ease.cubicOut).start();
}, 2000);

// Axis labels
var label0 = chart.radarContainer.createChild(am4core.Label);
label0.isMeasured = false;
label0.y = 10;
label0.horizontalCenter = "middle";
label0.verticalCenter = "top";
label0.text = "Positive";
label0.fill = am4core.color("green");

label0.adapter.add("x", function (x, target) {
  return -(axis.renderer.pixelInnerRadius + (axis.renderer.pixelRadius - axis.renderer.pixelInnerRadius) / 2);
});

var label1 = chart.radarContainer.createChild(am4core.Label);
label1.isMeasured = false;
label1.y = 10;
label1.horizontalCenter = "middle";
label1.verticalCenter = "top";
label1.text = "Negative";
label1.fill = am4core.color("red");

label1.adapter.add("x", function (x, target) {
  return (axis.renderer.pixelInnerRadius + (axis.renderer.pixelRadius - axis.renderer.pixelInnerRadius) / 2);
});
if (chart.logo) { chart.logo.disabled = true; }


anychart.onDocumentReady(function () {

  // load the data
  anychart.data.loadJsonFile("https://static.anychart.com/git-storage/word-press/data/choropleth-map-tutorial/data.json", function (data) {

    // Variables
    // go into the records section of the data
    var geoData = data.records

    // sum of all cases per country
    var sumCases = 0;

    // convert cases to numbers
    var numC;

    // create a new array with the resulting data
    var data = [];

    // Go through the initial data
    for (var i = 0; i < geoData.length; i++) {
      // convert strings to numbers and save them to new variables
      numC = parseInt(geoData[i].cases);

      // check if we are in the same country by comparing the geoId. 
      // if the country is the same add the cases to the appropriate variables
      if ((geoData[i + 1]) != null && (geoData[i].geoId == geoData[i + 1].geoId)) {
        sumCases = sumCases + numC;
      }
      else {

        // add last day cases of the same country
        sumCases = sumCases + numC;

        // insert the resulting data in the array using the AnyChart keywords 
        data.push({ id: geoData[i].geoId, value: sumCases, title: geoData[i].countriesAndTerritories })

        // reset the variables to start over
        sumCases = 0;

      }
    };

    // connect the data with the map
    var chart = anychart.map(data);
    chart.geoData(anychart.maps.world);

    // specify the chart type and set the series 
    var series = chart.choropleth(data);

    // color scale ranges
    ocs = anychart.scales.ordinalColor([
      { less: 99 },
      { from: 100, to: 999 },
      { from: 1000, to: 9999 },
      { from: 10000, to: 29999 },
      { from: 30000, to: 39000 },
      { from: 40000, to: 59000 },
      { from: 60000, to: 99999 },
      { greater: 100000 }
    ]);

    // set scale colors
    ocs.colors(["rgb(252,245,245)", "rgb(241,219,216)", "rgb(229,190,185)", "rgb(211,152,145)", "rgb(192,117,109)", "rgb(178,93,86)", "rgb(152,50,48)", "rgb(150,33,31)"]);

    // tell the series what to use as a colorRange (colorScale)
    series.colorScale(ocs);

    // set the container id
    chart.container('container');
    var credits = chart.credits();
    credits.enabled(false);
    // draw the chart
    chart.legend(true);

    // set the source mode of the legend and add styles
    chart.legend()
      .itemsSourceMode("categories")
      .position('right')
      .align('top')
      .itemsLayout('vertical')
      .padding(20, 20, 20, 20)
      .paginator(false);
    chart.draw();
  });
});
