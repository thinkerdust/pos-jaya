var table = NioApp.DataTable('#dt-table', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/transaction/product-damage/datatable'
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'name_product', name: 'p.name'},
        {data: 'stock', name: 'pd.stock', render: $.fn.dataTable.render.number( ',', '.', 0)},
        {data: 'note', name: 'pd.note'},
        {data: 'action', orderable: false, searchable: false},
    ],
    columnDefs: [] 
});

$('.format-number').on('keyup', (evt) => {
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

$('#product').select2({
    placeholder: 'Pilih Produk',
    allowClear: true,
    dropdownParent: $('#modalForm'),
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

function tambah() {
    $('#form-data')[0].reset();
    $('#uid').val('');
    $('#product').val('').change();
    $('#qty').val(0);
    $('#modalForm').modal('show');
}

$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit');

    $.ajax({
        url : "/transaction/product-damage/store",  
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
        url: '/transaction/product-damage/edit/'+uid,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                $('#modalForm').modal('show');
                let data = response.data;
                $('#uid').val(uid);
                $('#note').val(data.note);
                $('#qty').val(thousandView(data.stock));
                $("#product").empty().append(`<option value="${data.uid_product}">${data.name_product}</option>`).val(data.uid_product).trigger('change');
                $("#product").attr('disabled', true);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}

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
                url: '/transaction/product-damage/delete/'+uid,
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