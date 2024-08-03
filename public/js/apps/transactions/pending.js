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
        {data: 'note', name:'so.note'},
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
