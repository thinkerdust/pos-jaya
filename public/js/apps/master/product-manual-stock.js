var table = NioApp.DataTable('#dt-table', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/product-manual-stock/datatable'
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'name', name: 'p.name'},
        {data: 'name_categories', name: 'pc.name'},
        {data: 'name_unit', name: 'u.name'},
        {data: 'cost_price', name: 'p.cost_price', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' )},
        {data: 'sell_price', name: 'p.sell_price', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' )},
        {data: 'retail_member_price', name: 'p.retail_member_price', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' )},
        {data: 'stock', name: 'p.stock', render: $.fn.dataTable.render.number( ',', '.', 0)},
        {data: 'action', orderable: false, searchable: false},
    ],
    columnDefs: [] 
});

$('.number').on('keyup', (evt) => {
    keyUpThousandView(evt)
})

const keyUpThousandView = (evt) => {
    let currentValue = (evt.currentTarget.value != '') ? evt.currentTarget.value.replaceAll('.','') : '0';
    let iNumber = parseInt(currentValue);
    let result = isNaN(iNumber) == false ? thousandView(iNumber) : '0';
    evt.currentTarget.value = result;
}

const thousandView = (number = 0) => {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

$('#product_categories').select2({
    placeholder: 'Pilih Kategori',
    allowClear: true,
    dropdownParent: $('#modalForm'),
    ajax: {
        url: '/data-product-categories',
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
    placeholder: 'Pilih Satuan',
    allowClear: true,
    dropdownParent: $('#modalForm'),
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
                url: '/product/delete/'+uid,
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

function tambah() {
    $('#form-data')[0].reset();
    $('#uid').val('');
    $('#product_categories').val('').change();
    $('#unit').val('').change();
    $('#modalForm').modal('show');
}

$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit');

    $.ajax({
        url : "/product-manual-stock/store",  
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
                $('#form-data')[0].reset();
                $('#modalForm').modal('hide');
                $("#dt-table").DataTable().ajax.reload(null, false);
                NioApp.Toast(response.message, 'success', {position: 'top-right'});
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
});

function edit(uid) {
    $.ajax({
        url: '/product/edit/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                $('#modalForm').modal('show');
                let data = response.data;
                $('#uid').val(uid);
                $('#name').val(data.name);
                $('#description').val(data.description);
                $('#cost_price').val(thousandView(data.cost_price));
                $('#sell_price').val(thousandView(data.sell_price));
                $('#retail_member_price').val(thousandView(data.retail_member_price));
                $('#stock').val(thousandView(data.stock));
                $("#product_categories").empty().append(`<option value="${data.uid_product_categories}">${data.name_categories}</option>`).val(data.uid_product_categories).trigger('change');
                $("#unit").empty().append(`<option value="${data.uid_unit}">${data.name_unit}</option>`).val(data.uid_unit).trigger('change');
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

function addprice(uid) {
    $('#uid_product').val(uid);
    $('#modalFormPrice').modal('show');
    $("#dt-table-price").DataTable().ajax.reload();
}

var tablePrice = NioApp.DataTable('#dt-table-price', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/product/datatable-price',
        type: 'POST',
        data: function(d) {
            d._token = token;
            d.uid_product = $('#uid_product').val();
        }
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'first_quantity', className: 'text-end', render: $.fn.dataTable.render.number( ',', '.', 0)},
        {data: 'last_quantity', className: 'text-end', render: $.fn.dataTable.render.number( ',', '.', 0)},
        {data: 'price', className: 'text-end', render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' )},
        {data: 'status'},
        {data: 'action', orderable: false, searchable: false},
    ],
    columnDefs: [
        {
            targets: -2,
            orderable: false,
            searchable: false,
            render: function(data, type, full, meta) {
                
                var status = {
                    0: {'title': 'Non-Aktif', 'class': ' bg-danger'},
                    1: {'title': 'Aktif', 'class': ' bg-success'},
                };
                if (typeof status[full['status']] === 'undefined') {
                    return data;
                }
                return '<span class="badge '+ status[full['status']].class +'">'+ status[full['status']].title +'</span>';
            }
        },
    ] 
});

$('#form-data-price').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit-price');

    $.ajax({
        url : "/product/store-price",  
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
                $('#form-data-price')[0].reset();
                $('#uid_price').val('');
                $("#dt-table-price").DataTable().ajax.reload(null, false);
                NioApp.Toast(response.message, 'success', {position: 'top-right'});
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
});

function edit_price(uid) {
    $.ajax({
        url: '/product/edit-price/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                let data = response.data;
                $('#uid_price').val(uid);
                $('#uid_product').val(data.uid_product);
                $('#first_quantity').val(thousandView(data.first_quantity));
                $('#last_quantity').val(thousandView(data.last_quantity));
                $('#price').val(thousandView(data.price));
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

function hapus_price(uid) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/product/delete-price/'+uid,
                dataType: 'JSON',
                success: function(response) {
                    if(response.status){
                        $("#dt-table-price").DataTable().ajax.reload(null, false);
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

function addUnit()
{
    $('#modalFormUnit').modal('show');
    $('#form-data-unit')[0].reset();
    $('#name_unit').val('');
}

$('#btn-submit-unit').click(function(e) {
    e.preventDefault();
    let name = $('#name_unit').val();

    $.ajax({
        url: '/unit/store',
        type: 'POST',
        dataType: 'json',
        data: { _token: token, name: name },
        success: function(response) {
            if(response.status){
                $('#modalFormUnit').modal('hide');
                NioApp.Toast(response.message, 'success', {position: 'top-right'});
            }else{
                NioApp.Toast(response.message, 'warning', {position: 'top-right'});
            }
        },
        error: function(error) {
            console.log(error);
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
})

function addCategory()
{
    $('#modalFormCategory').modal('show');
    $('#form-data-category')[0].reset();
    $('#name_category').val('');
}

$('#btn-submit-category').click(function(e) {
    e.preventDefault();
    let name = $('#name_category').val();

    $.ajax({
        url: '/product-categories/store',
        type: 'POST',
        dataType: 'json',
        data: { _token: token, name: name },
        success: function(response) {
            if(response.status){
                $('#modalFormCategory').modal('hide');
                NioApp.Toast(response.message, 'success', {position: 'top-right'});
            }else{
                NioApp.Toast(response.message, 'warning', {position: 'top-right'});
            }
        },
        error: function(error) {
            console.log(error);
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
})

$('#first_quantity').keyup(function() {
    let qty = $(this).val();
    qty = qty.replaceAll('.', '');
    qty = parseInt(qty);
    let uid_product = $('#uid_product').val();

    if(qty >= 100){
        $.ajax({
            url: '/product/get-retail-price/'+uid_product,
            dataType: 'json',
            success: function(response) {
                if(response.status){
                    $('#price').val(thousandView(response.data.retail_member_price));
                }else{
                    $('#price').val(0);
                }
            },
            error: function(error) {
                $('#price').val(0);
                console.log(error);
                NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
            }
        })
    }else{
        $('#price').val(0);
    }
})