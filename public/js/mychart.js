class MyChart {

    static DOUGHNUT = 0;

	constructor(canvas, type) {

		this.type = "";
        this.data = {};
        this.options = {};
        this.plugins = [];
        this.chart = null;

        // this.backgroundColor = ["#0074D9", "#FF4136", "#2ECC40", "#FF851B", "#7FDBFF", "#B10DC9", "#FFDC00", "#001f3f", "#39CCCC", "#01FF70", "#85144b", "#F012BE", "#3D9970", "#111111", "#AAAAAA"];

        // this.COLORS = [
        //     '#4dc9f6',
        //     '#f67019',
        //     '#f53794',
        //     '#537bc4',
        //     '#acc236',
        //     '#166a8f',
        //     '#00a950',
        //     '#58595b',
        //     '#8549ba'
        //   ];

        // this.CHART_COLORS_TRANSPARENT = [
        //     'rgba(54, 162, 235, 0.5)',
        //     'rgb(153, 102, 255, 0.5)',
        //     'rgb(201, 203, 207, 0.5)',
        //     'rgb(255, 99, 132, 0.5)',
        //     'rgb(255, 159, 64, 0.5)',
        //     'rgb(255, 205, 86, 0.5)',
        //     'rgb(75, 192, 192, 0.5)',
        // ];

        // this.CHART_COLORS = [
        //     'rgb(54, 162, 235)',
        //     'rgb(153, 102, 255)',
        //     'rgb(201, 203, 207)',
        //     'rgb(255, 99, 132)',
        //     'rgb(255, 159, 64)',
        //     'rgb(255, 205, 86)',
        //     'rgb(75, 192, 192)',
        // ];

        switch (type) {

                case MyChart.DOUGHNUT:

                    this.getDoughnut(canvas);
                break;
        }
	}

    getBar(data) {

        // const data = {
        //     labels: data.labels,
        //     datasets: [
        //         {
        //             label: 'Small Radius',
        //             data: data.datasets[0].data,
        //             borderColor: this.backgroundColor,
        //             backgroundColor: this.backgroundColor,
        //             borderWidth: 2,
        //             borderRadius: 5,
        //             borderSkipped: false,
        //         }
        //     ]
        // };

        data.datasets.forEach((element, key) => {

            // element.backgroundColor = this.CHART_COLORS_TRANSPARENT[key % this.CHART_COLORS_TRANSPARENT.length];
            // element.borderColor = this.CHART_COLORS[key % this.CHART_COLORS.length];
            element.borderWidth = 2;
            element.borderRadius = 5;// Number.MAX_VALUE;
            element.borderSkipped = false;
        });

        // data.datasets[0].backgroundColor = this.CHART_COLORS_TRANSPARENT.blue;
        // data.datasets[0].borderColor = this.CHART_COLORS.blue;
        // data.datasets[0].borderWidth = 2;
        // data.datasets[0].borderRadius = 5;// Number.MAX_VALUE;
        // data.datasets[0].borderSkipped = false;

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    colors: {
                        forceOverride: true
                    },
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: data.title
                    }
                }
            },
        };

        let canvas = document.createElement('canvas');
//        canvas.width = 300;
        //canvas.height = 200;

        this.chart = new Chart(canvas, config);

        return canvas;
    }

    getDoughnut(canvas) {

        let overallStatschartOptions = {
            locale: "pt-BR",
            responsive: true,

            plugins: {
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            let label = " ";

                            if (context.parsed !== null) {

                                label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed);
                            }

                            return label;
                        }
                    }
                },

                colors: {
                    forceOverride: true
                },

                legend: {
                    display: true,
                    align: 'center',
                    position: 'bottom',
                    labels: {

                        fontColor: '#474B4F',
                        usePointStyle: true,
                    }
                }
            },
        };

        let data = {
            labels: [],
            datasets: [
                {
                backgroundColor: this.backgroundColor
                }
            ],
            total: 0
        }
        // data.labels = ["teste"];
        // data.datasets[0].backgroundColor = this.backgroundColor;
        // data.total = 0;

        this.chart = new Chart(canvas, {
            type: 'doughnut',
            data: data,
            options: overallStatschartOptions,
            plugins: [{
                id: 'CentralLegend',
                beforeDraw: function(chart, a, b) {
                    let width = chart.width,
                    height = chart.height,
                    ctx = chart.ctx;

                    ctx.restore();

                    let text = "R$ " + data.total;

                    let fontSize = (height / 300).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";

                    let textX = Math.round((width - ctx.measureText(text).width) / 2),
                    textY = (height + - chart.legend.height) / 2;

                    ctx.fillStyle = "#0f74a6";
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                },
            }]
        });
    }
}