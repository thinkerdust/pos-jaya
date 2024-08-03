var grand_total = 0;
var disc = 0;

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
    let result = isNaN(iNumber) == false ? typeThousandView(iNumber) : '0';
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
    let uid = $('#uid_purchase_order').val();
    if(uid) {
            $.ajax({
                url: '/transaction/purchase/edit/'+uid,
                dataType: 'json',
                success: function(response) {
                    if(response.status) {
                        let header = response.data.header;
                        let detail = response.data.detail;
                        $('#uid_purchase_order').val(header.uid);
                        $('#po_number').val(header.po_number);
                        if (header.discount != 0) {
                            $('#en_disc').attr('checked',true);
                            $('#disc').attr('disabled',false);
                            $('#disc').val(thousandView(header.discount));
                            disc = header.discount;
                        }
                        $("#supplier").empty().append(`<option value="${header.uid_supplier}">${header.name}</option>`).val(header.uid_supplier).trigger('change');

                        var html = '';
                        for (let i = 0; i < detail.length; i++) {
                            let subtotal = detail[i].price * detail[i].qty;
                            let subtotal_formated =  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(subtotal);
                            let price_formated =  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(detail[i].price);
                            html += '<tr>';
                            html += '<td class="text-center"><input class="input_product" type="hidden" name="details[products][]" value="'+detail[i].uid_product+'"/>'+detail[i].product_name+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[units][]" value="'+detail[i].uid_unit+'"/>'+detail[i].unit_name+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[prices][]" value="'+detail[i].price+'"/>'+price_formated+'</td>';
                            html += '<td class="text-center"><input type="hidden" name="details[qty][]" value="'+detail[i].qty+'"/>'+detail[i].qty+'</td>';
                            html += '<td class="text-center"><input class="subtotal" type="hidden" name="details[subtotal][]" value="'+subtotal+'"/>'+subtotal_formated+'</td>';
                            html += '<td class="text-center">';
                            html += '<a class="btn btn-sm btn-dim btn-outline-secondary" type="button" onclick="delMaterial(this)"><em class="icon ni ni-trash"></em>Delete</a>';
                            html += '</td></tr>';  
                            grand_total += subtotal;

                        }
            
                        $("#tbody_material").find("#nodata").closest("tr").remove();
                        $("#tbody_material").append(html);
                        NioApp.Toast("Material Added", 'success', {position: 'top-right'});
                        
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
        url: '/transaction/purchase/datatable',
        data: function (d) {
            d.min = $('#filter_date_from').val();
            d.max = $('#filter_date_to').val();
        },
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'po_number', name:'po.po_number'},
        {data: 'name', name:'sup.name' },
        {data: 'transaction_date', name:'po.transaction_date'},
        {data: 'grand_total', name:'po.grand_total', className:'text-end'},
        {data: 'action', orderable: false, searchable: false},
    ],
    columnDefs: [
        {
            targets: 4,
            orderable: false,
            render: function(data, type, full, meta) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(full['grand_total']);
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
                url: '/transaction/purchase/delete/'+uid,
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


$("#disc").keyup(function(){
    disc = originView($(this).val());
    setGrandTotal();
})

function setGrandTotal(){
    let grand_total_min_disc = grand_total - disc;
    let total = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(grand_total_min_disc);
    console.log(total);
    $("#grand_total").html(total);
}

$(function () {
    $("#disc").attr("disabled", true);
})

$("#en_disc").click(function () {
    if ($("#en_disc").is(':checked')) {
        $("#disc").attr("disabled", false);
    } else {
        // grand_total +=  $("#disc").val()*1;
        $("#disc").val(0).trigger("change");
        $("#disc").attr("disabled", true);
    }
})

$('#supplier').select2({
    placeholder: 'Pilih Supplier',
    allowClear: true,
    ajax: {
        url: '/data-supplier',
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


$('#material').select2({
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


$('#material').change(function(){
    getMaterial($(this).val());
});

function getMaterial(uid) {
    $.ajax({
        url: '/product/edit/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                $('#price').val(thousandView(data.cost_price));
                $("#unit").empty().append(`<option value="${data.uid_unit}">${data.name_unit}</option>`).val(data.uid_unit).trigger('change');
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

$('#supplier').change(function(){
    getSupplier($(this).val());
});


function getSupplier(uid) {
    $.ajax({
        url: '/supplier/edit/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                $('#phone').val(data.phone);
                $('#address').val(data.address);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

$("#add_material").click(function(){
    var uid_material = $("#material").val();
    var price = originView($("#price").val());
    var qty = $("#qty").val();

    console.log(uid_material);
    //validate
    if (uid_material == null) {
        NioApp.Toast("Material is required", 'warning', {position: 'top-right'});
        $("#material").focus();
        return false;
    }

    if (qty == 0) {
        NioApp.Toast("Qty cannot be 0", 'warning', {position: 'top-right'});
        $("#qty").focus();
        return false;
    }

    var price_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price);
    var subtotal = qty * price;
    var subtotal_formated = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(subtotal)
    var material = '';
    var uid_unit = '';
    var html = '';

    $.ajax({
        url: '/product/edit/'+uid_material,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                material = data.name;
                uid_unit = data.uid_unit;
                unit = data.name_unit;

                html += '<tr>';
                html += '<td class="text-center"><input class="input_product" type="hidden" name="details[products][]" value="'+uid_material+'"/>'+material+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[units][]" value="'+uid_unit+'"/>'+unit+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[prices][]" value="'+price+'"/>'+price_formated+'</td>';
                html += '<td class="text-center"><input type="hidden" name="details[qty][]" value="'+qty+'"/>'+qty+'</td>';
                html += '<td class="text-center"><input class="subtotal" type="hidden" name="details[subtotal][]" value="'+subtotal+'"/>'+subtotal_formated+'</td>';
                html += '<td class="text-center">';
                html += '<a class="btn btn-sm btn-dim btn-outline-secondary" type="button" onclick="delMaterial(this)"><em class="icon ni ni-trash"></em>Delete</a>';
                html += '</td></tr>';  

                $("#tbody_material").find("#nodata").closest("tr").remove();
                $("#tbody_material").append(html);
                NioApp.Toast("Material Added", 'success', {position: 'top-right'});

                //reset form
                $("#material").val('').trigger('change');
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
    NioApp.Toast("Material Removed", 'error', {position: 'top-right'});
    var rowCount = $("#tbody_material tr").length;
    if (rowCount == 0) {
        var emptyRow = '<tr><td class="text-center text-muted" id="nodata" colspan="6">Tidak ada produk</td></tr>';
        $("#tbody_material").append(emptyRow);
    }
}



$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit');

    var check = checkStock(formData);
    if (check) {
        $.ajax({
            url : "/transaction/purchase/store",  
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
                        window.location.href = '/transaction/purchase';
                    }, 2000)
                }else{
                    NioApp.Toast(response.message, 'warning', {position: 'top-right'});
                }
                btn.attr('disabled', false);
                btn.html('Save');
            },
            error: function(error) {
                console.log(error)
                btn.attr('disabled', false);
                btn.html('Save');
                NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
            }
        });
    }
});


function checkStock(formData){
    let btn = $('#btn-submit');
    var status_stock;

    $.ajax({
        url : "/transaction/purchase/check_stock",  
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
                    // closestTr.find(".input_stock").val(stock)
                    // closestTr.find(".view_stock").html(stock)
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
    $("#dt-table").DataTable().ajax.reload();
    $("#modal_filter").modal('hide');
}

