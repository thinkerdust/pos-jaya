var grand_total = 0;
var disc = 0;
var ppn = 0;
var which;

$(".submit").click(function () {
    which = $(this).attr("id");
});

$('.formated_number').on('keyup', (evt) => {
    keyUpThousandView(evt)
})

$('#price').change(function(){
    let value = $(this).val()
    $(this).val(typeThousandView(value));
})


const keyUpThousandView = (evt) => {
    let currentValue = (evt.currentTarget.value != '') ? evt.currentTarget.value.replaceAll('.','') : '0';
    let iNumber = parseInt(currentValue);
    let result = isNaN(iNumber) == false ? thousandView(iNumber) : '0';
    evt.currentTarget.value = result;
}

const typeThousandView = (number = 0) => {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

const thousandView = (number = 0) => {
    return number.toString().replace(".",",").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

const originView = (number = 0) => {
    return number.replaceAll('.','');
}

$(document).ready(function() {    
    let uid = $('#uid_sales_order').val();
    if(uid) {
            $.ajax({
                url: '/transaction/sales/edit/'+uid,
                dataType: 'json',
                success: function(response) {
                    if(response.status) {
                        let header = response.data.header;
                        let detail = response.data.detail;
                        $('#uid_sales_order').val(header.uid);
                        $('#invoice_number').val(header.invoice_number);
                        $('#collection_date').val(header.collection_date);
                        $('#priority').val(header.priority);
                        $('#disc').val(thousandView(header.discount));
                        $('#ppn_value').val(thousandView(header.tax_value));
                        $('#disc_global').val(header.disc_rate);
                        $('#ppn').val(header.tax_rate);

                        disc = header.discount;
                        ppn = header.tax_value;
                        
                        $("#customer").empty().append(`<option value="${header.uid_customer}">${header.name}</option>`).val(header.uid_customer).trigger('change');

                        var html = '';
                        for (let i = 0; i < detail.length; i++) {
                            let subtotal = detail[i].price * detail[i].qty;
                            let subtotal_formated =  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(subtotal);
                            let price_formated =  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(detail[i].price);
                            html += '<tr>';
                            html += '<td><input type="hidden" name="details[products][]" value="'+detail[i].uid_product+'"/>'+detail[i].product_name+'</td>';
                            html += '<td><input type="hidden" name="details[units][]" value="'+detail[i].uid_unit+'"/>'+detail[i].unit_name+'</td>';
                            html += '<td class="text-end"><input type="hidden" name="details[qty][]" value="'+detail[i].qty+'"/>'+detail[i].stock+'</td>';
                            html += '<td class="text-end"><input type="hidden" name="details[stock][]" value="'+detail[i].qty+'"/>'+detail[i].qty+'</td>';
                            html += '<td class="text-end"><input type="hidden" name="details[prices][]" value="'+detail[i].price+'"/>'+price_formated+'</td>';
                            html += '<td class="text-end"><input class="subtotal" type="hidden" name="details[subtotal][]" value="'+subtotal+'"/>'+subtotal_formated+'</td>';
                            html += '<td class="text-center">';
                            html += '<a class="btn btn-sm btn-dim btn-outline-secondary" type="button" onclick="delMaterial(this)"><em class="icon ni ni-trash"></em>Delete</a>';
                            html += '</td></tr>';  
                            grand_total += subtotal;

                        }
            
                        $("#tbody_product").find("#nodata").closest("tr").remove();
                        $("#tbody_product").append(html);
                        NioApp.Toast("Produk berhasil ditambahkan", 'success', {position: 'top-right'});
                        
                        //set grand total
                        console.log(grand_total);
                        setGrandTotal();
            
                    }
                },
                error: function(error) {
                    console.log(error)
                    NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
                }
            })
    
        }
    
});

var table = NioApp.DataTable('#dt-table', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/transaction/receivable_payment/datatable'
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'invoice_number', name:'rp.invoice_number'},
        {data: 'customer_name', name:'c.name' },
        {data: 'transaction_date', name:'rp.transaction_date'},
        {data: 'amount', name:'rp.amount', className:'text-end'},
        {data: 'term', name:'rp.term', className:'text-end'},
        {data: 'payment_method', name:'pm.name', className:'text-end'},
        {data: 'action', orderable: false, searchable: false},
    ],
    columnDefs: [
        {
            targets: 4,
            orderable: false,
            render: function(data, type, full, meta) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(full['amount']);
            }
        },
    ] 
});

function hapus(uid) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/transaction/receivable_payment/delete/'+uid,
                dataType: 'JSON',
                success: function(response) {
                    if(response.status){
                        NioApp.Toast(response.message, 'success', {position: 'top-right'});
                        setTimeout(function(){
                            window.location.href = '/transaction/sales';
                        }, 2000)
                    }else{
                        NioApp.Toast(response.message, 'warning', {position: 'top-right'});
                    }
                    },
                error: function(error) {
                    console.log(error)
                    NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
                }
            })
        }
    });
}

$("#disc_global").change(function(){
    let persen = $(this).val();
    let value_disc = Math.round((grand_total * persen) /100);
    $("#disc").val(thousandView(value_disc)).trigger('change');
    $("#ppn").trigger('change');
})

$("#ppn").change(function(){
    let persen = $(this).val();
    let value_ppn= Math.round(((grand_total-disc) * persen) /100);
    $("#ppn_value").val(thousandView(value_ppn)).trigger('change');
})



$("#disc").change(function(){
    disc = originView($(this).val());
    setGrandTotal();
})


$("#ppn_value").change(function(){
    ppn = originView($(this).val());
    setGrandTotal();
})


function setGrandTotal(){
    let grand_total_min_disc = grand_total - disc + parseFloat(ppn);
    let total = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(grand_total_min_disc);
    console.log(total);
    $("#grand_total").html(total);
}

$(function () {
    $("#disc").attr("readonly", true);
    $("#ppn_value").attr("readonly", true);
})

$("#en_disc").click(function () {
    if ($("#en_disc").is(':checked')) {
        $("#disc").attr("readonly", false);
    } else {
        // grand_total +=  $("#disc").val()*1;
        $("#disc").val(0).trigger("change");
        $("#disc").attr("readonly", true);
    }
})

$('#customer').select2({
    placeholder: 'Pilih Customer',
    allowClear: true,
    ajax: {
        url: '/data-customer',
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
                        text: item.name,
                        id: item.uid
                    }
                })
            };
        },
        cache: true,
    }
})


$('#product').select2({
    placeholder: 'Pilih Produk',
    allowClear: true,
    ajax: {
        url: '/data-product',
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
                        text: item.name,
                        id: item.uid
                    }
                })
            };
        },
        cache: true
    }
})

$('#unit').select2({
    placeholder: 'Pilih Unit',
    allowClear: true,
    ajax: {
        url: '/data-unit',
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
                        text: item.name,
                        id: item.uid
                    }
                })
            };
        },
        cache: true
    }
})


$('#product').change(function(){
    let customer_type = $("#customer_type").val();
    if (customer_type == '') {
        NioApp.Toast('Select customer First', 'warning', {position: 'top-right'});
        $("#customer").focus();
        $(this).val('');
    }else{
        getMaterial($(this).val(),customer_type);
    }
});

function getMaterial(uid,customer_type) {
    $.ajax({
        url: '/product/edit/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                if (customer_type == 'Retail') {
                    $('#price').val(thousandView(data.retail_member_price));
                }else{
                    $('#price').val(thousandView(data.sell_price));
                }
                $("#unit").empty().append(`<option value="${data.uid_unit}">${data.name_unit}</option>`).val(data.uid_unit).trigger('change');
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

$("#qty").change(function(){
    let product = $("#product").val();
    let qty = $(this).val();
    if (product == '') {
        NioApp.Toast('Select product first', 'warning', {position: 'top-right'});
        $("#product").focus();
        $(this).val(0);
    }else{
        $.ajax({
            url: '/product/get-price/'+product,
            dataType: 'JSON',
            data : {
                qty : qty
            },
            success: function(response) {
                if(response.status) {
                    let data = response.data;
                    $('#price').val(thousandView(data.price));
                    NioApp.Toast('Harga grosir diterapkan', 'success', {position: 'top-right'});
                }
            },
            error: function(error) {
                console.log(error)
                NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
            }
        })
    
    }

})

function getGrosirPrice(){
    
}

$('#customer').change(function(){
    getCustomer($(this).val());
});


function getCustomer(uid) {
    $.ajax({
        url: '/customer/edit/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#customer_type').val(data.type);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

$("#add_product").click(function(){
    var uid_material = $("#product").val();
    var price = originView($("#price").val());
    var qty = $("#qty").val();

    console.log(uid_material);
    //validate
    if (uid_material == null) {
        NioApp.Toast("Produk harus di isi", 'warning', {position: 'top-right'});
        $("#product").focus();
        return false;
    }

    if (qty == 0) {
        NioApp.Toast("Quantity harus di isi", 'warning', {position: 'top-right'});
        $("#qty").focus();
        return false;
    }

    var price_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price);
    var subtotal = qty * price;
    var subtotal_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(subtotal)
    var material = '';
    var uid_unit = '';
    var html = '';
    var class_stock = '';

    $.ajax({
        url: '/product/edit/'+uid_material,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                material = data.name;
                uid_unit = data.uid_unit;
                unit = data.name_unit;

                if (data.stock < qty) {
                    NioApp.Toast('Stock produk '+material+' tidak mencukupi', 'warning', {position: 'top-right'});
                    class_stock = "table-danger";
                }

                html += '<tr class="'+class_stock+'">';
                html += '<td class="text-center"><input class="input_product" type="hidden" name="details[products][]" value="'+uid_material+'"/>'+material+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[units][]" value="'+uid_unit+'"/>'+unit+'</td>';
                html += '<td class="text-center"><input class="input_stock" type="hidden" name="details[stock][]" value="'+data.stock+'"/><p class="view_stock">'+data.stock+'</p></td>';
                html += '<td class="text-center"><input type="hidden" name="details[qty][]" value="'+qty+'"/>'+qty+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[prices][]" value="'+price+'"/>'+price_formated+'</td>';
                html += '<td class="text-center"><input class="subtotal" type="hidden" name="details[subtotal][]" value="'+subtotal+'"/>'+subtotal_formated+'</td>';
                html += '<td class="text-center">';
                html += '<a class="btn btn-sm btn-dim btn-outline-secondary" type="button" onclick="delMaterial(this)"><em class="icon ni ni-trash"></em>Delete</a>';
                html += '</td></tr>';  

                $("#tbody_product").find("#nodata").closest("tr").remove();
                $("#tbody_product").append(html);
                NioApp.Toast("Produk berhasil di tambahkan", 'success', {position: 'top-right'});

                //reset form
                $("#product").val('').trigger('change');
                $("#price").val(0);
                $("#unit").val('').trigger('change');
                $("#qty").val(0);

                //set grand total
                grand_total += subtotal;
                setGrandTotal();
            
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })

})


function delMaterial(th) {
    var deleted_subtotal = $(th).closest('tr').find('.subtotal').val();
    grand_total = grand_total - deleted_subtotal;
    setGrandTotal();

    $(th).closest('tr').remove();
    NioApp.Toast("Produk berhasil dihapus", 'error', {position: 'top-right'});
    var rowCount = $("#tbody_product tr").length;
    $("#disc_global").trigger("change");
    if (rowCount == 0) {
        var emptyRow = '<tr><td class="text-center text-muted" id="nodata" colspan="7">Tidak ada produk</td></tr>';
        $("#tbody_product").append(emptyRow);
        $("#disc_global").val(0).trigger("change");
    }
}



$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#'+which);

    if (which == 'btn-submit') {
        formData.append('pending',0);
    }else{
        formData.append('pending',1);
    }

    var check = checkStock(formData);
    if (check) {
        $.ajax({
            url : "/transaction/sales/store",  
            data : formData,
            type : "POST",
            dataType : "JSON",
            cache:false,
            async : true,
            contentType: false,
            processData: false,
            beforeSend: function() {
                btn.attr('disabled', true);
                btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span>Loading ...</span>`);
            },
            success: function(response) {
                if(response.status){
                    NioApp.Toast(response.message, 'success', {position: 'top-right'});
                    setTimeout(function(){
                        window.location.href = '/transaction/sales';
                    }, 2000)
                }else{
                    NioApp.Toast(response.message, 'warning', {position: 'top-right'});
                }
                btn.attr('disabled', false);
                // btn.html('Save');
            },
            error: function(error) {
                console.log(error)
                btn.attr('disabled', false);
                // btn.html('Save');
                NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
            }
        });
    }
    
});

function checkStock(formData){
    let btn = $('#'+which);
    var status_stock;

    $.ajax({
        url : "/transaction/sales/check_stock",  
        data : formData,
        type : "POST",
        dataType : "JSON",
        cache:false,
        async : false,
        contentType: false,
        processData: false,
        beforeSend: function() {
            btn.attr('disabled', true);
            btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span>Loading ...</span>`);
        },
        success: function(response) {
            status_stock = true;
            if(!response.status){
                NioApp.Toast(response.message, 'error', {position: 'top-right'});
                for (let i = 0; i < response.data.length; i++) {
                    var valueToFind = response.data[i].product;
                    var stock = response.data[i].stock;
                    var closestTr = $('.input_product').filter(function() {
                        return $(this).val() === valueToFind;
                    }).parent().closest('tr');
                    closestTr.addClass('table-danger');
                    closestTr.find(".input_stock").val(stock)
                    closestTr.find(".view_stock").html(stock)
                }

                btn.attr('disabled', false);
                btn.html(`Simpan`);
                status_stock = false;
            }
        },
        error: function(error) {
            console.log(error)
            btn.attr('disabled', false);
            // btn.html('Save');
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    });

    return status_stock;

}

