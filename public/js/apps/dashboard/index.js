$(document).ready(function() {
    $.ajax({
        url: '/user-authenticate',
        dataType: 'json',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                $("#mall").empty().append(`<option value="${data.uid_mall}">${data.nama_mall}</option>`).val(data.uid_mall).trigger('change');
            }
        }
    });
});

$('#filter_date').datepicker({
    autoclose: true,
    format: "MM-yyyy",
    startView: "months", 
    minViewMode: "months"
});

$("#mall").select2({
    placeholder: "Select Mall",
    ajax: {
        url: '/data-mall',
        dataType: "json",
        type: "get",
        delay: 250,
        data: function (params) {
            return { q: params.term };
        },
        processResults: function (data, params) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.nama,
                        id: item.uid
                    }
                })
            };
        },
        cache: true
    }
});

$('#mall').change(function() {
    today_orders();
    month_orders();
    today_customer();
    month_customer();
    total_points();
    member_points();
    member_vouchers();
    vouchers_expired();
    $("#dt-table-top-tenants").DataTable().ajax.reload();
    $("#dt-table-top-spender").DataTable().ajax.reload();
    $("#dt-table-top-referral").DataTable().ajax.reload();
    sales_statistics();
    customer_statistics();
})

$('#filter_date').change(function() {
    today_orders();
    month_orders();
    today_customer();
    month_customer();
    total_points();
    member_points();
    member_vouchers();
    vouchers_expired();
    $("#dt-table-top-tenants").DataTable().ajax.reload();
    $("#dt-table-top-spender").DataTable().ajax.reload();
    $("#dt-table-top-referral").DataTable().ajax.reload();
    sales_statistics();
    customer_statistics();
})

const thousandView = (number = 0) => {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function today_orders() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/today-orders',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#today-orders').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

today_orders();

function month_orders() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/month-orders',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#month-orders').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

month_orders();

function today_customer() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/today-customer',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#today-customers').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

today_customer();

function month_customer() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/month-customer',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#month-customer').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

month_customer();

function total_points() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/total-points',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#total-points').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

total_points();

function member_points() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/member-points',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#member-points').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

member_points();

function member_vouchers() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/member-vouchers',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#member-vouchers').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

member_vouchers();

function vouchers_expired() {
    let filter_date = $('#filter_date').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/vouchers-expired',
        type: 'post',
        dataType: 'json',
        data: { '_token': token, 'filter_date': filter_date, 'mall': mall },
        success: function(data) {
            $('#vouchers-expired').text(thousandView(data));
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

vouchers_expired();

var tableTopTenants = NioApp.DataTable('#dt-table-top-tenants', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/dashboard/datatable-top-tenants',
        type: 'POST',
        data: function(d) {
            d._token = token;
            d.filter_date = $('#filter_date').val();
            d.mall = $('#mall').val();
        }
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'nama_tenant'},
        {data: 'nominal'}
    ],
    columnDefs: [
        {
            targets: -1,
            className: 'text-right',
            render: function(data, typ, full, meta) {
                return `Rp. ${thousandView(full['nominal'])}`;
            }
        },
        {
            targets: 1,
            render: function(data, typ, full, meta) {
                return `<a href="/transaction?uid_tenant=${full['uid']}">${full['nama_tenant']}</a>`;
            }
        }
    ],
    lengthMenu: [ [5, 10, 15, -1], [5, 10, 15, "All"] ]
});

var tableTopSpender = NioApp.DataTable('#dt-table-top-spender', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/dashboard/datatable-top-spender',
        type: 'POST',
        data: function(d) {
            d._token = token;
            d.filter_date = $('#filter_date').val();
            d.mall = $('#mall').val();
        }
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'nama_member'},
        {data: 'nominal'}
    ],
    columnDefs: [
        {
            targets: -1,
            className: 'text-right',
            render: function(data, typ, full, meta) {
                return `Rp. ${thousandView(full['nominal'])}`;
            }
        },
        {
            targets: 1,
            render: function(data, typ, full, meta) {
                return `<a href="/member/detail/${full['id']}">${full['nama_member']}</a>`;
            }
        }
    ],
    lengthMenu: [ [5, 10, 15, -1], [5, 10, 15, "All"] ]
});

var tableTopReferral = NioApp.DataTable('#dt-table-top-referral', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/dashboard/datatable-top-referral',
        type: 'POST',
        data: function(d) {
            d._token = token;
            d.filter_date = $('#filter_date').val();
            d.mall = $('#mall').val();
        }
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'nama_member'},
        {data: 'poin'}
    ],
    columnDefs: [
        {
            targets: -1,
            className: 'text-right',
            render: function(data, typ, full, meta) {
                return thousandView(full['poin']);
            }
        },
        {
            targets: 1,
            render: function(data, typ, full, meta) {
                return `<a href="/member/detail/${full['id']}">${full['nama_member']}</a>`;
            }
        }
    ],
    lengthMenu: [ [5, 10, 15, -1], [5, 10, 15, "All"] ]
});

function sales_statistics() {
    let filter_date = $('#filter_date').val();
    let period = $('#period_sales').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/sales-statistics',
        dataType: 'json',
        type: 'POST',
        data: {'_token': token, 'filter_date': filter_date, 'period': period, 'mall': mall},
        success: function (data) {
            if(data) {
                let salesStatisticsData = {
                    labels : data.label,
                    dataUnit : 'Rp',
                    datasets : [{
                        label : "All",
                        color : "#6576ff",
                        data: data.total_all
                    },{
                        label : "Dine",
                        color : "#eb6459",
                        data: data.total_food
                    },{
                        label : "Shop",
                        color : "#04bf42",
                        data: data.total_shop
                    }]
                };

                let selector = $('.sales-statistics');
                salesStatisticsChart(selector, salesStatisticsData);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

sales_statistics();
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

$('#period_sales').change(function() {
    sales_statistics();
})

function customer_statistics() {
    let filter_date = $('#filter_date').val();
    let period = $('#period_customer').val();
    let mall = $('#mall').val();
    $.ajax({
        url: '/dashboard/customer-statistics',
        dataType: 'json',
        type: 'POST',
        data: {'_token': token, 'filter_date': filter_date, 'period': period, 'mall': mall},
        success: function (data) {
            if(data) {
                let customerStatisticsData = {
                    labels : data.label,
                    dataUnit : '',
                    datasets : [{
                        label : "Active Users",
                        color : "#1f64c4",
                        data: data.total_active
                    },{
                        label : "New Users",
                        color : "#142133",
                        data: data.total_new
                    }]
                };

                let selector = $('.customer-statistics');
                customerStatisticsChart(selector, customerStatisticsData);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

customer_statistics();
var chartCustomerStatistics;

function customerStatisticsChart(selector, set_data){
    var $selector = (selector) ? $(selector) : $('.customer-statistics');
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
        if (chartCustomerStatistics) {
            chartCustomerStatistics.destroy()
        }
        chartCustomerStatistics = new Chart(selectCanvas, {
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
                            return thousandView(data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']]);
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

$('#period_customer').change(function() {
    customer_statistics();
})

function print_report() {
    window.print();
}