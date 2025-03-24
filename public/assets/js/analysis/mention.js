
////
am4core.useTheme(am4themes_animated);
      // Themes end
  
      // create chart
      var chart = am4core.create("chartdiv1", am4charts.GaugeChart);
      chart.innerRadius = am4core.percent(50);
  
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
      range1.fill = am4core.color("gray");
      range1.axisFill.fill = colorSet.getIndex(9);
   
      // Main label
      var label = chart.radarContainer.createChild(am4core.Label);
      label.isMeasured = false;
      label.fontSize = 0;
      label.x = am4core.percent(50);
      label.y = am4core.percent(100);
      label.horizontalCenter = "middle";
      label.text = "50%";
  
      // Hand
      var hand = chart.hands.push(new am4charts.ClockHand());
      hand.axis = axis;
      hand.innerRadius = am4core.percent(0);
      hand.startWidth = 20;
      hand.pin.disabled = true;
      hand.value = 50;
  
      hand.events.on("propertychanged", function (ev) {
        range0.endValue = ev.target.value;
        range1.value = ev.target.value;
        axis.invalidate();
      });
  
      var totalWidth = 100;
      var value = Math.random() * totalWidth;
  
      // setInterval(function () {
      //   value = Math.round(Math.random() * totalWidth);
      //   label.text = value + "%";
      //   var animation = new am4core.Animation(hand, {
      //     property: "value",
      //     to: value
      //   }, 1000, am4core.ease.cubicOut).start();
      // }, 2000);
      value = Math.round(Math.random() * totalWidth);
        label.text = value + "%";
        var animation = new am4core.Animation(hand, {
          property: "value",
          to: value
        }, 1000, am4core.ease.cubicOut).start();
      if (chart.logo) { chart.logo.disabled = true; }



 //const totalWidth = 100; // Total width of the progress bar
  
      // Update the widths of the sections dynamically to sum up to the total width
      function updateProgress(section1Width, section2Width) {
        const section3Width = totalWidth - section1Width - section2Width;
        
        document.getElementById("section1").style.width = section1Width + "%";
        document.getElementById("section2").style.width = section2Width + "%";
        document.getElementById("section3").style.width = section3Width + "%";
        
        document.getElementById("section2").style.left = section1Width + "%";
        document.getElementById("section3").style.left = (section1Width + section2Width) + "%";
  
        // setting text 
        document.getElementById("text1").innerHTML =Math.floor(section1Width) +"%"+"<br>Positive"
        document.getElementById("text2").innerHTML =Math.floor(section2Width) +"%"+"<br>Neutral"
        document.getElementById("text3").innerHTML =Math.floor(section3Width) +"%"+"<br>Negative"
  
        //sentiment status
        var statusText = "";
        var textColor = "";
        if (section1Width < 30) {
          statusText = "Slightly Positive";
          textColor = "#15dd57";
        } else if (section1Width < 60) {
          statusText = "Positive";
          textColor = "#1ab4c9";
        } else {
          statusText = "Highly Positive";
          textColor = "#dc6967";
        }
        
        // Update text and color
        var textElement = document.querySelector(".sentiment-status");
        textElement.textContent = statusText;
        textElement.style.color = textColor;
      }
      
      // Example usage to update progress with random values every 2 seconds
      // setInterval(() => {
      //   var section1Width = value
      //   const section2Width = Math.random() * (totalWidth - section1Width);
      //   updateProgress(section1Width, section2Width);
      // }, 2000);
      var section1Width = value
        const section2Width = Math.random() * (totalWidth - section1Width);
        updateProgress(section1Width, section2Width);


var myChart = document.getElementById("myChart");

      // Chart.defaults.global.defaultFontFamily = "Lato";
      // Chart.defaults.global.defaultFontSize = 18;

      var dataFirst = {
          label: "Japan",
          data: [0, 15, 25],
          lineTension: 0.2,
          fill: false,
          borderColor: 'red'
        };

      var dataSecond = {
          label: "USA",
          data: [2, 18, 28],
          lineTension: 0.2,
          fill: true,
          borderColor: 'blue'
        };

        var dataThird = {
          label: "UK",
          data: [3, 22, 33],
          lineTension: 0.2,
          fill: true,
          borderColor: 'green'
        }; 

      var myData = {
        labels: ["0", "18 Sep", "19 Sep"],
        datasets: [dataFirst, dataSecond,dataThird]
      };

      var chartOptions = {
        legend: {
          display: true,
          position: 'bottom',
          labels: {
            boxWidth: 80,
            fontColor: 'black',
            usePointStyle: true,
            pointStyle:'circle',
            fontSize:9
          }
        },
        plugins: {
          legend: {
            position: "bottom"
          }
        }
      };

      var lineChart = new Chart(myChart, {
        type: 'line',
        data: myData,
        options: chartOptions
      });


 // Sample initial data for the donut chart
//  var chart = am4core.create("chartdiv", am4charts.PieChart);
  
//  // Add data
//  chart.data = [{
//    "country": "Japan",
//    "value": 501.9,
//    "color": am4core.color("#ff9300")
//  }, {
//    "country": "United States",
//    "value": 301.9,
//    "color": am4core.color("#29cdcd")
//  }, {
//    "country": "Italy",
//    "value": 201.1,
//    "color": am4core.color("#1fe28d")
//  }, {
//    "country": "United Kingdom",
//    "value": 165.8,
//    "color": am4core.color("#ffcc00")
//  }, {
//    "country": "France",
//    "value": 139.9,
//    "color": am4core.color("#800080")
//  }, {
//    "country": "Other",
//    "value": 128.3,
//    "color": am4core.color("#008000")
//  }];

//  // Add and configure Series
//  var pieSeries = chart.series.push(new am4charts.PieSeries());
//  pieSeries.dataFields.value = "value";
//  pieSeries.dataFields.category = "country";
//  pieSeries.labels.template.disabled = true;
//  pieSeries.ticks.template.disabled = true;
//  pieSeries.slices.template.propertyFields.fill = "color";

//  chart.legend = new am4charts.Legend();
//  chart.legend.position = "bottom";

//  chart.radius = 80;
//  chart.innerRadius =40;

//  chart.legend.layout = "vertical";
//  chart.legend.marginTop = 120;

//  chart.labels.template.fill = am4core.color("#ff9300");


//  var label1 = pieSeries.createChild(am4core.Label);
//  label1.text = "Japan\n34%";
//  label1.horizontalCenter = "middle";
//  label1.verticalCenter = "middle";
//  label1.fontSize = 20;
//  if (chart.logo) { chart.logo.disabled = true; }

var chart = am4core.create("chartdiv", am4charts.PieChart);

// Add data
chart.data = [{
  "country": "Japan",
  "value": 501.9,
  "color": am4core.color("#ff9300")
}, {
  "country": "United States",
  "value": 301.9,
  "color": am4core.color("#29cdcd")
}, {
  "country": "Italy",
  "value": 201.1,
  "color": am4core.color("#1fe28d")
}, {
  "country": "United Kingdom",
  "value": 165.8,
  "color": am4core.color("#ffcc00")
}, {
  "country": "France",
  "value": 139.9,
  "color": am4core.color("#800080")
}, {
  "country": "Other",
  "value": 128.3,
  "color": am4core.color("#008000")
}];

// Add and configure Series
var pieSeries = chart.series.push(new am4charts.PieSeries());
pieSeries.dataFields.value = "value";
pieSeries.dataFields.category = "country";
pieSeries.labels.template.disabled = true;
pieSeries.ticks.template.disabled = true;

chart.legend = new am4charts.Legend();

console.log(chart.legend);

chart.legend.labels.disabled = true;

chart.legend.position = "bottom";

chart.innerRadius = am4core.percent(60);

var label = pieSeries.createChild(am4core.Label);
label.text = "Country";
label.horizontalCenter = "middle";
label.verticalCenter = "middle";
label.fontSize = 16;
if (chart.logo) { chart.logo.disabled = true; }


//<!-- geo distribution map -->
    

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
        { less: 99,name:"Others 17%" },
        { from: 100, to: 999,name:"Brazil 5%" },
        { from: 1000, to: 9999,name:"Germany 5%" },
        { from: 10000, to: 29999,name:"France 3%" },
        { from: 30000, to: 39000,name:"Italy 3%" },
        { from: 40000, to: 59000,name:"United Kingdom 3%"},
        { from: 60000, to: 99999,name:"United States 24%" },
        { greater: 100000,name:"Japan 40%" }
      ]);

      // set scale colors
      ocs.colors([ "rgb(143,188,143)", "rgb(152,251,152)", "rgb(144,238,144)", "rgb(50,205,50)", "rgb(0,255,0)", "rgb(34,139,34)","rgb(0,128,0)", "rgb(0,100,0)"]);

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
        .padding(0, 0, 0, 0)
        .paginator(false);
      chart.draw();
    });
  });
