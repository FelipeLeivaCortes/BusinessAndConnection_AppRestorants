(function ($) {
    "use strict";

    $(window).on('load', function() {
        setTimeout(function() {
            //Cashflow Charts
            if (document.getElementById("cashFlow")) {
                var chartCurrency = _currency_symbol;
                const cashFlow = document
                    .getElementById("cashFlow")
                    .getContext("2d");
                var cashFlowChart = new Chart(cashFlow, {
                    type: "line",
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: $lang_sales,
                                data: [],
                                backgroundColor: ["rgba(46, 204, 113, 0.4)"],
                                borderColor: ["rgba(46, 204, 113, 1.0)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                            {
                                label: $lang_expense,
                                data: [],
                                backgroundColor: ["rgba(255, 99, 132, 0.4)"],
                                borderColor: ["rgba(255, 99, 132, 1.0)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                            {
                                label: $lang_satff_salary,
                                data: [],
                                backgroundColor: ["rgba(243, 156, 18, 0.4)"],
                                borderColor: ["rgba(243, 156, 18, 1.0)"],
                                yAxisID: "y",
                                borderWidth: 2,
                                tension: 0.4,
                            },
                        ],
                    },
                    options: {
                        interaction: {
                            mode: "index",
                            intersect: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        stacked: true,
                        scales: {
                            y: {
                                type: "linear",
                                display: true,
                                position: "left",
                                ticks: {
                                    callback: function (value, index, values) {
                                        return chartCurrency + " " + value;
                                    },
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: "rectRounded",
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var label = context.dataset.label || "";

                                        if (
                                            context.parsed.y !== null &&
                                            context.dataset.yAxisID == "y"
                                        ) {
                                            label +=
                                                ": " +
                                                chartCurrency +
                                                " " +
                                                context.parsed.y;
                                        } else {
                                            label += ": " + context.parsed.y;
                                        }

                                        return label;
                                    },
                                },
                            },
                        },
                    },
                });
            }

            //Sales By Category
            if (document.getElementById("salesOverview")) {
                var link2 = _url + "/dashboard/json_sales_by_category";
                $.ajax({
                    url: link2,
                    success: function (data2) {
                        var json2 = JSON.parse(data2);

                        const ctx = document
                            .getElementById("salesOverview")
                            .getContext("2d");
                        const salesOverviewChart = new Chart(ctx, {
                            type: "doughnut",
                            data: {
                                labels: json2["category"],
                                datasets: [
                                    {
                                        data: json2["amounts"],
                                        backgroundColor: json2["colors"],
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        labels: {
                                            usePointStyle: true,
                                            pointStyle: "rectRounded",
                                        },
                                    },
                                    title: {
                                        display: false,
                                        text: $lang_sales_overview,
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return (
                                                    " " +
                                                    context.label +
                                                    ": " +
                                                    _currency_symbol +
                                                    " " +
                                                    context.parsed
                                                );
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    },
                });
            }

            //Expense By Category Chart
            if (document.getElementById("expenseOverview")) {
                var link2 = _url + "/dashboard/json_expense_by_category";
                $.ajax({
                    url: link2,
                    success: function (data2) {
                        var json2 = JSON.parse(data2);

                        const ctx = document
                            .getElementById("expenseOverview")
                            .getContext("2d");
                        const expenseOverviewChart = new Chart(ctx, {
                            type: "doughnut",
                            data: {
                                labels: json2["category"],
                                datasets: [
                                    {
                                        data: json2["amounts"],
                                        backgroundColor: json2["colors"],
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        labels: {
                                            usePointStyle: true,
                                            pointStyle: "rectRounded",
                                        },
                                    },
                                    title: {
                                        display: false,
                                        text: $lang_expense_overview,
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                return (
                                                    " " +
                                                    context.label +
                                                    ": " +
                                                    _currency_symbol +
                                                    " " +
                                                    context.parsed
                                                );
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    },
                });
            }

            if (document.getElementById("cashFlow")) {
                $.ajax({
                    url: _url + "/dashboard/json_cashflow",
                    success: function (data) {
                        var json = JSON.parse(data);

                        cashFlowChart.data.labels = json["month"];
                        cashFlowChart.data.datasets[0].data = [
                            typeof json["sales"][1] !== "undefined"
                                ? json["sales"][1]
                                : 0,
                            typeof json["sales"][2] !== "undefined"
                                ? json["sales"][2]
                                : 0,
                            typeof json["sales"][3] !== "undefined"
                                ? json["sales"][3]
                                : 0,
                            typeof json["sales"][4] !== "undefined"
                                ? json["sales"][4]
                                : 0,
                            typeof json["sales"][5] !== "undefined"
                                ? json["sales"][5]
                                : 0,
                            typeof json["sales"][6] !== "undefined"
                                ? json["sales"][6]
                                : 0,
                            typeof json["sales"][7] !== "undefined"
                                ? json["sales"][7]
                                : 0,
                            typeof json["sales"][8] !== "undefined"
                                ? json["sales"][8]
                                : 0,
                            typeof json["sales"][9] !== "undefined"
                                ? json["sales"][9]
                                : 0,
                            typeof json["sales"][10] !== "undefined"
                                ? json["sales"][10]
                                : 0,
                            typeof json["sales"][11] !== "undefined"
                                ? json["sales"][11]
                                : 0,
                            typeof json["sales"][12] !== "undefined"
                                ? json["sales"][12]
                                : 0,
                        ];
                        cashFlowChart.data.datasets[1].data = [
                            typeof json["expenses"][1] !== "undefined"
                                ? json["expenses"][1]
                                : 0,
                            typeof json["expenses"][2] !== "undefined"
                                ? json["expenses"][2]
                                : 0,
                            typeof json["expenses"][3] !== "undefined"
                                ? json["expenses"][3]
                                : 0,
                            typeof json["expenses"][4] !== "undefined"
                                ? json["expenses"][4]
                                : 0,
                            typeof json["expenses"][5] !== "undefined"
                                ? json["expenses"][5]
                                : 0,
                            typeof json["expenses"][6] !== "undefined"
                                ? json["expenses"][6]
                                : 0,
                            typeof json["expenses"][7] !== "undefined"
                                ? json["expenses"][7]
                                : 0,
                            typeof json["expenses"][8] !== "undefined"
                                ? json["expenses"][8]
                                : 0,
                            typeof json["expenses"][9] !== "undefined"
                                ? json["expenses"][9]
                                : 0,
                            typeof json["expenses"][10] !== "undefined"
                                ? json["expenses"][10]
                                : 0,
                            typeof json["expenses"][11] !== "undefined"
                                ? json["expenses"][11]
                                : 0,
                            typeof json["expenses"][12] !== "undefined"
                                ? json["expenses"][12]
                                : 0,
                        ];
                        cashFlowChart.data.datasets[2].data = [
                            typeof json["staff_salary"][1] !== "undefined"
                                ? json["staff_salary"][1]
                                : 0,
                            typeof json["staff_salary"][2] !== "undefined"
                                ? json["staff_salary"][2]
                                : 0,
                            typeof json["staff_salary"][3] !== "undefined"
                                ? json["staff_salary"][3]
                                : 0,
                            typeof json["staff_salary"][4] !== "undefined"
                                ? json["staff_salary"][4]
                                : 0,
                            typeof json["staff_salary"][5] !== "undefined"
                                ? json["staff_salary"][5]
                                : 0,
                            typeof json["staff_salary"][6] !== "undefined"
                                ? json["staff_salary"][6]
                                : 0,
                            typeof json["staff_salary"][7] !== "undefined"
                                ? json["staff_salary"][7]
                                : 0,
                            typeof json["staff_salary"][8] !== "undefined"
                                ? json["staff_salary"][8]
                                : 0,
                            typeof json["staff_salary"][9] !== "undefined"
                                ? json["staff_salary"][9]
                                : 0,
                            typeof json["staff_salary"][10] !== "undefined"
                                ? json["staff_salary"][10]
                                : 0,
                            typeof json["staff_salary"][11] !== "undefined"
                                ? json["staff_salary"][11]
                                : 0,
                            typeof json["staff_salary"][12] !== "undefined"
                                ? json["staff_salary"][12]
                                : 0,
                        ];
                        cashFlowChart.update();
                    },
                });
            }
            $(".loading-chart").remove();
        }, 2000);
    });
})(jQuery);
