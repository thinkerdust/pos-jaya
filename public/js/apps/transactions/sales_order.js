var grand_total = 0;
var disc = 0;
var ppn = 0;
var laminating = 0;
var proofing = 0;
var which;
var level = "{{Auth::user()->id_role}}";

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
    return number.replaceAll('.','').replaceAll(',','.');
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
                        $('#uid_company').val(header.uid_company);
                        $('#collection_date').val(header.collection_date);
                        $('#priority').val(header.priority);
                        $('#disc').val(thousandView(header.discount));
                        $('#ppn_value').val(thousandView(header.tax_value));
                        $('#disc_global').val(header.disc_rate);
                        $('#ppn').val(header.tax_rate);
                        $('#proofing').val(thousandView(header.proofing));
                        $('#keterangan').val(header.note);

                        disc = header.discount;
                        ppn = header.tax_value;
                        laminating = header.laminating;
                        proofing = header.proofing;
                        
                        $("#customer").empty().append(`<option value="${header.uid_customer}">${header.name}</option>`).val(header.uid_customer).trigger('change');

                        var html = '';
                        for (let i = 0; i < detail.length; i++) {
                            // let subtotal = detail[i].price * detail[i].qty;
                            var price = detail[i].price ? detail[i].price : 0 ;
                            var qty = detail[i].qty ? detail[i].qty : 0 ;
                            var width = detail[i].width ? detail[i].width : 0 ;
                            var length = detail[i].length ? detail[i].length : 0;
                            var packing = detail[i].packing ? detail[i].packing :0;
                            var cutting = detail[i].cutting ? detail[i].cutting :0;
                            var packing_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(packing);
                            var cutting_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(cutting);
                            if (length != 0 || width !=0) {
                               var subtotal = ((parseFloat(length) * parseFloat(width) * parseFloat(price) * parseFloat(qty))/10000) + parseFloat(cutting)+ parseFloat(packing);   
                        
                            }else{
                                var subtotal = (parseFloat(price) * parseFloat(qty)) + parseFloat(cutting)+ parseFloat(packing);   
                        
                            }

                            var subtotal_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(subtotal)
                            let price_formated =  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(detail[i].price);
                            let notes = detail[i].note ?? "";
                            html += '<tr>';
                            html += '<td class="text-center"><input type="hidden" name="details[products][]" value="'+detail[i].uid_product+'"/>'+detail[i].product_name+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[units][]" value="'+detail[i].uid_unit+'"/>'+detail[i].unit_name+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[qty][]" value="'+detail[i].qty+'"/>'+detail[i].stock+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[stock][]" value="'+detail[i].qty+'"/>'+detail[i].qty+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[length][]" value="'+length+'"/><input type="hidden" name="details[width][]" value="'+width+'"/>'+length+'x'+width+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[prices][]" value="'+price+'"/>'+price_formated+'</td>';
                            html += '<td class="text-center"><input class="subtotal" type="hidden" name="details[subtotal][]" value="'+subtotal+'"/>'+subtotal_formated+'</td>';
                            html += '<td class="text-center"><input class="notes" type="hidden" name="details[notes][]" value="'+notes+'"/>'+notes+'</td>';
                            html += '<td class="text-center">';
                            html += '<a class="btn btn-sm btn-dim btn-outline-secondary" type="button" onclick="delMaterial(this)"><em class="icon ni ni-trash"></em></a>';
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
        url: '/transaction/sales/datatable',
        data: function (d) {
            d.min = $('#filter_date_from').val();
            d.max = $('#filter_date_to').val();
            d.status = $('#filter_status').val();
        },
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'invoice_number', name:'so.invoice_number'},
        {data: 'name', name:'cus.name' },
        {data: 'transaction_date', name:'so.transaction_date'},
        {data: 'grand_total', name:'so.grand_total', className:'text-end'},
        {data: 'note', name:'so.note'},
        {data: 'paid_off', name:'so.paid_off'},
        {data: 'action', orderable: false, searchable: false},
    ],
    columnDefs: [
        {
            targets: 1,
            orderable: false,
            searchable: false,
            render: function(data, type, full, meta) {
                return '<a target="_blank" href="/transaction/sales/invoice/'+full['uid']+'">'+ full['invoice_number'] +'</a>';
            }

        },
        {
            targets: 4,
            orderable: false,
            render: function(data, type, full, meta) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(full['grand_total']);
            }
        },
        {
            targets: -2,
            orderable: false,
            searchable: false,
            render: function(data, type, full, meta) {
                
                var status = {
                    0: {'title': 'Blm Lunas', 'class': ' bg-danger'},
                    1: {'title': 'Lunas', 'class': ' bg-success'},
                };
                if (typeof status[full['paid_off']] === 'undefined') {
                    return data;
                }
                return '<span class="badge '+ status[full['paid_off']].class +'">'+ status[full['paid_off']].title +'</span>';
            }

        }
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
                url: '/transaction/sales/delete/'+uid,
                dataType: 'JSON',
                success: function(response) {
                    if(response.status){
                        $("#dt-table").DataTable().ajax.reload(null, false);
                        NioApp.Toast(response.message, 'success', {position: 'top-right'});
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

$("#disc_global").keyup(function(){
    let persen = $(this).val();
    let value_disc = Math.round((grand_total * persen) /100);
    $("#disc").val(thousandView(value_disc)).trigger('change');
    $("#ppn").trigger('change');
})

$("#ppn").keyup(function(){
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

$("#laminating").change(function(){
    laminating = originView($(this).val());
    setGrandTotal();
})


$("#proofing").keyup(function(){
    proofing = originView($(this).val());
    setGrandTotal();
})


$("#panjang").keyup(function(){
    setSubTotal()
})

$("#lebar").keyup(function(){
    setSubTotal()
})


$("#qty").keyup(function(){
    setSubTotal()
})

$("#cutting_price").keyup(function(){
    setSubTotal()
})

$("#packing_price").keyup(function(){
    setSubTotal()
})

$("#price").keyup(function(){
    setSubTotal()
})







function setSubTotal(){
    let length = originView($("#panjang").val()) ? originView($("#panjang").val()): 0;
    let width = originView($("#lebar").val()) ? originView($("#lebar").val()) : 0;
    let price = originView($("#price").val()) ? originView($("#price").val()) :  0;
    let qty = originView($("#qty").val()) ? originView($("#qty").val()) : 0;
    let cutting = originView($("#cutting_price").val()) ? originView($("#cutting_price").val()) : 0;
    let packing = originView($("#packing_price").val()) ? originView($("#packing_price").val()) : 0;
subtotal = 0;
    console.log(cutting);
    
    if (length != 0 || width !=0) {
        console.log(length);
        console.log(width);
        console.log(price);
        console.log(qty);
         st = ((parseFloat(length) * parseFloat(width) * parseFloat(price) * parseFloat(qty))/10000) + parseFloat(cutting)+ parseFloat(packing);     
         console.log(st);  
         subtotal_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(st);

    }else{
        console.log(length);
        console.log(width);
        console.log(price);
        console.log(qty);

         st = (price * qty)+parseFloat(cutting)+parseFloat(packing); 
         console.log(st);  
 
         subtotal_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(st);
  
    }

    $("#subtotal").val(subtotal_formated);
}

function setGrandTotal(){
    let grand_total_min_disc = grand_total - disc + parseFloat(ppn)  + parseFloat(proofing) ;
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
                    $('#price').val(thousandView(data.price)).trigger('keyup');
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
    var price = originView($("#price").val()) ? originView($("#price").val()) : 0;
    var qty = originView($("#qty").val()) ? originView($("#qty").val()) : 0;
    var notes = $("#notes").val();
    var length = originView($("#panjang").val()) ? originView($("#panjang").val()) :0;
    var width = originView($("#lebar").val()) ? originView($("#lebar").val()) :0 ;
    var packing = originView($("#packing_price").val()) ? originView($("#packing_price").val()) :0;
    var cutting = originView($("#cutting_price").val()) ? originView($("#cutting_price").val()) :0;

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
    var packing_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(packing);
    var cutting_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(cutting);
    if (length != 0 || width !=0) {
       var subtotal = ((parseFloat(length) * parseFloat(width) * parseFloat(price) * parseFloat(qty))/10000) + parseFloat(cutting)+ parseFloat(packing);   

    }else{
        var subtotal = (parseFloat(price) * parseFloat(qty)) + parseFloat(cutting)+ parseFloat(packing);   

    }
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

                if (parseFloat(data.stock) < parseFloat(qty)) {
                    NioApp.Toast('Stock produk '+material+' tidak mencukupi', 'warning', {position: 'top-right'});
                    class_stock = "table-danger";
                }

                html += '<tr class="'+class_stock+'">';
                html += '<td class="text-center"><input class="input_product" type="hidden" name="details[products][]" value="'+uid_material+'"/>'+material+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[units][]" value="'+uid_unit+'"/>'+unit+'</td>';
                html += '<td class="text-center"><input class="input_stock" type="hidden" name="details[stock][]" value="'+data.stock+'"/><p class="view_stock">'+data.stock+'</p></td>';
                html += '<td class="text-center"><input type="hidden" name="details[qty][]" value="'+qty+'"/>'+qty+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[length][]" value="'+length+'"/><input type="hidden" name="details[width][]" value="'+width+'"/>'+length+'x'+width+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[prices][]" value="'+price+'"/>'+price_formated+'</td>';
                html += '<td class="text-center"><input class="subtotal" type="hidden" name="details[subtotal][]" value="'+subtotal+'"/>'+subtotal_formated+'</td>';
                html += '<td class="text-center"><input class="notes" type="hidden" name="details[notes][]" value="'+notes+'"/>'+notes+'</td>';
                html += '<td class="text-center">';
                html += '<a class="btn btn-sm btn-dim btn-outline-secondary" type="button" onclick="delMaterial(this)"><em class="icon ni ni-trash"></em></a>';
                html += '</td></tr>';  

                $("#tbody_product").find("#nodata").closest("tr").remove();
                $("#tbody_product").append(html);
                NioApp.Toast("Produk berhasil di tambahkan", 'success', {position: 'top-right'});

                //reset form
                $("#product").val('').trigger('change');
                $("#price").val(0);
                $("#unit").val('').trigger('change');
                $("#qty").val(0);
                $("#notes").val('');
                $("#subtotal").val(0);
                $("#panjang").val(0);
                $("#lebar").val(0);
                $("#cutting_price").val(0);
                $("#packing_price").val(0);

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
        var emptyRow = '<tr><td class="text-center text-muted" id="nodata" colspan="8">Tidak ada produk</td></tr>';
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

function bayar(uid){
    $.ajax({
        url: '/transaction/receivable_payment/get_data_receipt/'+uid,
        dataType: 'json',
        success: function(response) {
            if(response.status) {
                let data = response.data.header;
                let receipt = response.data.receipt;
                let html = "";
                $("#modal_noinv").val(data.invoice_number);
                $("#modal_uid").val('');
                $("#modal_customer").val(data.name);
                $("#modal_tanggal").val(data.transaction_date);
                $("#modal_total").val(thousandView(data.grand_total));
                $("#modal_selisih").val(thousandView(data.selisih));
                $("#modal_payment_method").val('').trigger('change');
                $("#modal_amount").val('');
                $("#modal_changes").val(0);
                $("#title_modal_pembayaran").html('Form Pembayaran '+data.invoice_number);
                $("#modal_pembayaran").modal('show')

                if (receipt != '') {
                    let no =1;
                    for (let i = 0; i < receipt.length; i++) {
                        html += '<tr>';
                        html += '<td>'+no+'</td>';
                        html += '<td>'+receipt[i].transaction_date+'</td>';
                        html += '<td>'+receipt[i].payment_method+'</td>';
                        html += '<td class="text-end">'+thousandView(receipt[i].amount)+'</td>';
                        html += '<td><button class="btn btn-dim btn-outline-secondary btn-xs" onclick="edit_receipt(\''+receipt[i].uid+'\')"><em class="icon ni ni-edit"></em></button>';
                        html += '<button class="btn btn-dim btn-outline-secondary btn-xs" onclick="del_receipt(\''+receipt[i].uid+'\')"><em class="icon ni ni-trash"></em></button>';
                        html += '<a class="btn btn-dim btn-outline-secondary btn-xs" target="_blank" href="/transaction/receivable_payment/receipt/'+receipt[i].uid+'"><em class="icon ni ni-send"></em></a></td>';
                        html += '</tr>';
                        no++;
                    }

                }else{
                    html += "<tr>";
                    html += "<td class='text-center' colspan='5'>Tidak ada data</td>";
                    html += "</tr>";
                }

                $("#tbody_pembayaran").html(html);


                console.log(data)
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })

}


$('#modal_payment_method').select2({
    placeholder: 'Pilih Metode Pembayaran',
    allowClear: true,
    dropdownParent: $('#modal_pembayaran'),
    ajax: {
        url: '/data-payment-method',
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

$('#form-pembayaran').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);

    var btn = $('#modal_submit');

        $.ajax({
            url : "/transaction/receivable_payment/store",  
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
                btn.html('Bayar');
            },
            error: function(error) {
                console.log(error)
                btn.attr('disabled', false);
                btn.html('Bayar');
                NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
            }
        });
    
});

function del_receipt(uid) {
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


function edit_receipt(uid){
    $.ajax({
        url: '/transaction/receivable_payment/edit/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status){
                $("#modal_uid").val(uid);
                $("#modal_payment_method").empty().append(`<option value="${response.data[0].uid_payment_method}">${response.data[0].payment_method}</option>`).val(response.data[0].uid_payment_method).trigger('change');
                var amount = (response.data[0].pay!=0) ? thousandView(response.data[0].pay) :  thousandView(response.data[0].amount);
                console.log(amount);
                $("#modal_amount").val(amount);
                var selisih = parseFloat(originView($("#modal_selisih").val())) + parseFloat(response.data[0].amount);
                $("#modal_selisih").val(thousandView(selisih));
                $("#modal_changes").val(thousandView(response.data[0].changes));
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

function filter(){
    $("#modal_filter").modal('show');
}

function applyFilter(){
    $("#dt-table").DataTable().ajax.reload();
    $("#modal_filter").modal('hide');
}

function clearFilter(){
    $("#filter_date_from").val('');
    $("#filter_date_to").val('');
    $("#filter_status").val('');
    $("#dt-table").DataTable().ajax.reload();
    $("#modal_filter").modal('hide');
}


$("#modal_amount").keyup(function(){
    var bayar = $(this).val() ? originView($(this).val()) : 0;
    var tagihan = $("#modal_selisih").val() ? originView($("#modal_selisih").val()) : 0;
    console.log(bayar)
    console.log(tagihan)
    var kembalian = bayar - tagihan;
    kembalian = (kembalian < 0) ? 0 : kembalian;
    console.log(bayar);
    console.log(tagihan);
    console.log(kembalian);
    $("#modal_changes").val(thousandView(kembalian));
})