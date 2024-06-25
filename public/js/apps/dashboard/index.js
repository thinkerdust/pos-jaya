$('#filter_date').datepicker({
    autoclose: true,
    format: "MM-yyyy",
    startView: "months", 
    minViewMode: "months"
});

const thousandView = (number = 0) => {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

$('#filter_date').change(function() {
    totalProducts();
    totalPurchase();
    totalSales();
    purchaseStatistics();
    salesStatistics();
})

function totalProducts() {
    let filter_date = $('#filter_date').val();
    $.ajax({
        url: '/dashboard/total-products',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date},
        success: function(data) {
            $('#total-products').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

totalProducts();

function totalPurchase() {
    let filter_date = $('#filter_date').val();
    $.ajax({
        url: '/dashboard/total-purchase',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date},
        success: function(data) {
            $('#total-purchase').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

totalPurchase();

function totalSales() {
    let filter_date = $('#filter_date').val();
    $.ajax({
        url: '/dashboard/total-sales',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date},
        success: function(data) {
            $('#total-sales').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

totalSales();

function purchaseStatistics() {
    let filter_date = $('#filter_date').val();
    $.ajax({
        url: '/dashboard/purchase-statistics',
        dataType: 'json',
        type: 'POST',
        data: {'_token': token, 'filter_date': filter_date},
        success: function (data) {
            if(data) {
                let dataPurchaseStatistics = {
                    labels : data.label,
                    dataUnit : 'Rp',
                    datasets : [{
                        label : "Purchase Transaction",
                        color : "#6576ff",
                        data: data.total
                    }]
                };

                let selector = $('.purchase-statistics');
                purchaseStatisticsChart(selector, dataPurchaseStatistics);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

purchaseStatistics();
var chartPurchaseStatistics;

function purchaseStatisticsChart(selector, set_data){
    var $selector = (selector) ? $(selector) : $('.purchase-statistics');
    $selector.each(function(){
        var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data,
        _d_legend = (typeof _get_data.legend === 'undefined') ? false : _get_data.legend;

        var selectCanvas = document.getElementById(_self_id).getContext("2d");
        var chart_data = [];
        for (var i = 0; i < _get_data.datasets.length; i++) {
            chart_data.push({
                label: _get_data.datasets[i].label,
                data: _get_data.datasets[i].data,
                // Styles
                backgroundColor: _get_data.datasets[i].color,
                borderWidth:2,
                borderColor: 'transparent',
                hoverBorderColor : 'transparent',
                borderSkipped : 'bottom',
                barPercentage : .6,
                categoryPercentage : .7
            });
        } 
        // destroy previous created graph
        if (chartPurchaseStatistics) {
            chartPurchaseStatistics.destroy()
        }
        chartPurchaseStatistics = new Chart(selectCanvas, {
            type: 'bar',
            data: {
                labels: _get_data.labels,
                datasets: chart_data,
            },
            options: {
                legend: {
                    display: (_get_data.legend) ? _get_data.legend : false,
                    rtl: NioApp.State.isRTL,
                    labels: {
                        boxWidth:30,
                        padding:20,
                        fontColor: '#6783b8',
                    }
                },
                maintainAspectRatio: false,
                tooltips: {
                    enabled: true,
                    rtl: NioApp.State.isRTL,
                    callbacks: {
                        title: function(tooltipItem, data) {
                            return data.datasets[tooltipItem[0].datasetIndex].label;
                        },
                        label: function(tooltipItem, data) {
                            return _get_data.dataUnit + ' ' + thousandView(data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']]);
                        }
                    },
                    backgroundColor: '#eff6ff',
                    titleFontSize: 13,
                    titleFontColor: '#6783b8',
                    titleMarginBottom: 6,
                    bodyFontColor: '#9eaecf',
                    bodyFontSize: 12,
                    bodySpacing:4,
                    yPadding: 10,
                    xPadding: 10,
                    footerMarginTop: 0,
                    displayColors: false
                },
                scales: {
                    yAxes: [{
                        display: true,
                        stacked: (_get_data.stacked) ? _get_data.stacked : false,
                        position : NioApp.State.isRTL ? "right" : "left",
                        ticks: {
                            beginAtZero:true,
                            fontSize:12,
                            fontColor:'#9eaecf',
                        },
                        gridLines: { 
                            color: NioApp.hexRGB("#526484",.2),
                            tickMarkLength:0,
                            zeroLineColor: NioApp.hexRGB("#526484",.2)
                        },
                        
                    }],
                    xAxes: [{
                        display: true,
                        stacked: (_get_data.stacked) ? _get_data.stacked : false,
                        ticks: {
                            fontSize:12,
                            fontColor:'#9eaecf',
                            source: 'auto',
                            reverse: NioApp.State.isRTL
                        },
                        gridLines: {
                            color: "transparent",
                            tickMarkLength: 10,
                            zeroLineColor: 'transparent',
                        },
                    }]
                }
            }
        });
    })
}

function salesStatistics() {
    let filter_date = $('#filter_date').val();
    $.ajax({
        url: '/dashboard/sales-statistics',
        dataType: 'json',
        type: 'POST',
        data: {'_token': token, 'filter_date': filter_date},
        success: function (data) {
            if(data) {
                let dataSalesStatistics = {
                    labels : data.label,
                    dataUnit : 'Rp',
                    datasets : [{
                        label : "Sales Transaction",
                        color : "#eb6459",
                        data: data.total
                    }]
                };

                let selector = $('.sales-statistics');
                salesStatisticsChart(selector, dataSalesStatistics);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

salesStatistics();
var chartSalesStatistics;

function salesStatisticsChart(selector, set_data){
    var $selector = (selector) ? $(selector) : $('.sales-statistics');
    $selector.each(function(){
        var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data,
        _d_legend = (typeof _get_data.legend === 'undefined') ? false : _get_data.legend;

        var selectCanvas = document.getElementById(_self_id).getContext("2d");
        var chart_data = [];
        for (var i = 0; i < _get_data.datasets.length; i++) {
            chart_data.push({
                label: _get_data.datasets[i].label,
                data: _get_data.datasets[i].data,
                // Styles
                backgroundColor: _get_data.datasets[i].color,
                borderWidth:2,
                borderColor: 'transparent',
                hoverBorderColor : 'transparent',
                borderSkipped : 'bottom',
                barPercentage : .6,
                categoryPercentage : .7
            });
        } 
        // destroy previous created graph
        if (chartSalesStatistics) {
            chartSalesStatistics.destroy()
        }
        chartSalesStatistics = new Chart(selectCanvas, {
            type: 'bar',
            data: {
                labels: _get_data.labels,
                datasets: chart_data,
            },
            options: {
                legend: {
                    display: (_get_data.legend) ? _get_data.legend : false,
                    rtl: NioApp.State.isRTL,
                    labels: {
                        boxWidth:30,
                        padding:20,
                        fontColor: '#6783b8',
                    }
                },
                maintainAspectRatio: false,
                tooltips: {
                    enabled: true,
                    rtl: NioApp.State.isRTL,
                    callbacks: {
                        title: function(tooltipItem, data) {
                            return data.datasets[tooltipItem[0].datasetIndex].label;
                        },
                        label: function(tooltipItem, data) {
                            return _get_data.dataUnit + ' ' + thousandView(data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']]);
                        }
                    },
                    backgroundColor: '#eff6ff',
                    titleFontSize: 13,
                    titleFontColor: '#6783b8',
                    titleMarginBottom: 6,
                    bodyFontColor: '#9eaecf',
                    bodyFontSize: 12,
                    bodySpacing:4,
                    yPadding: 10,
                    xPadding: 10,
                    footerMarginTop: 0,
                    displayColors: false
                },
                scales: {
                    yAxes: [{
                        display: true,
                        stacked: (_get_data.stacked) ? _get_data.stacked : false,
                        position : NioApp.State.isRTL ? "right" : "left",
                        ticks: {
                            beginAtZero:true,
                            fontSize:12,
                            fontColor:'#9eaecf',
                        },
                        gridLines: { 
                            color: NioApp.hexRGB("#526484",.2),
                            tickMarkLength:0,
                            zeroLineColor: NioApp.hexRGB("#526484",.2)
                        },
                        
                    }],
                    xAxes: [{
                        display: true,
                        stacked: (_get_data.stacked) ? _get_data.stacked : false,
                        ticks: {
                            fontSize:12,
                            fontColor:'#9eaecf',
                            source: 'auto',
                            reverse: NioApp.State.isRTL
                        },
                        gridLines: {
                            color: "transparent",
                            tickMarkLength: 10,
                            zeroLineColor: 'transparent',
                        },
                    }]
                }
            }
        });
    })
}