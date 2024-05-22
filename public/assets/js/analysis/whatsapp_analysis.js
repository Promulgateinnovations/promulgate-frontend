var chart = new CanvasJS.Chart("whatsappAnalysisChart", {
	animationEnabled: true,
	title:{
		text: ""
	},
	data: [{
		type: "funnel",
		indexLabel: "{label}",
		toolTipContent: "<b>{label}</b>: {y} <b>({percentage}%)</b>",
		neckWidth: 20,
		neckHeight: 0,
		valueRepresents: "area",
		dataPoints: [
			{ y: 95000, label: "Sent 95000/100000", color: "#1998bf" },
			{ y: 80000, label: "Received 80000/95000", color: "#4469e2"},
			{ y: 10000, label: "Read 10000/90000", color: "#f6941c"},
			{ y: 5000, label: "Replied 5000/90000", color: "#f6bd6e"},
		]
	}]
});
calculatePercentage();
chart.render();

function calculatePercentage() {
	var dataPoint = chart.options.data[0].dataPoints;
	var total = dataPoint[0].y;
    chart.options.data[0].dataPoints[0].percentage = 95;
    chart.options.data[0].dataPoints[1].percentage = 80;
    chart.options.data[0].dataPoints[2].percentage = 10;
    chart.options.data[0].dataPoints[3].percentage = 5 //((dataPoint[i].y / total) * 100).toFixed(2);
}