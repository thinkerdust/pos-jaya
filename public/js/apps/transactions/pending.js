var table = NioApp.DataTable('#dt-table', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/transaction/pending/datatable',
        data: function (d) {
            d.min = $('#filter_date_from').val();
            d.max = $('#filter_date_to').val();
        },
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'invoice_number', name:'so.invoice_number'},
        {data: 'name', name:'cus.name' },
        {data: 'transaction_date', name:'so.transaction_date'},
        {data: 'grand_total', name:'so.grand_total', className:'text-end'},
        {data: 'note', name:'so.note', orderable: false},
        {data: 'action', orderable: false, searchable: false},
    ],
    columnDefs: [
        {
            targets: 4,
            render: function(data, type, full, meta) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(full['grand_total']);
            }
        },
    ] 
});

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


