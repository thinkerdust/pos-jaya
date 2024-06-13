var table = NioApp.DataTable('#dt-table', {
    serverSide: true,
    processing: true,
    responsive: true,
    searchDelay: 500,
    ajax: {
        url: '/datatable-role'
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'name'},
        {data: 'status'},
        {data: 'id'},
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
        {
            targets: -1,
            orderable: false,
            searchable: false,
            render: function(data, type, full, meta) {
                return `<div class="drodown">
                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul class="link-list-opt no-bdr">
                                <li><a class="btn" onclick="edit(${full['id']})"><em class="icon ni ni-edit"></em><span>Edit</span></a></li>
                            </ul>
                        </div>
                    </div>`;
            }
        },
    ] 
});

function tree_menu() {
    $.getJSON("/list-permissions-menu", function (data) {
        let menu_json = data.menu;
        $("#tree_menu").jstree("destroy");

        $("#tree_menu").jstree({
            // include grid as a plugin
            plugins: ["wholerow", "types", "grid"],
            grid: {
                columns: [
                    { width: 350, header: "Menu" },
                    { width: 300, header: "Akses", value: "action" },
                ],
            },
            core: {
                themes: {
                    responsive: !1,
                },
                data: menu_json,
            },
            types: {
                default: {
                    icon: "far fa-folder icon-state-warning",
                },
                file: {
                    icon: "far fa-file icon-state-warning",
                },
            },
        });
    }).fail(function () {
        NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
    });
}

function edit_tree_menu(id) {
    $.getJSON("/list-permissions-menu?id=" + id,
        function (data) {
            let menu_json = data.menu;
            $("#tree_menu").jstree("destroy");

            $("#tree_menu").jstree({
                // include grid as a plugin
                plugins: ["wholerow", "types", "grid"],
                grid: {
                    columns: [
                        { width: 350, header: "Menu" },
                        { width: 300, header: "Akses", value: "action" },
                    ],
                },
                core: {
                    themes: {
                        responsive: !1,
                    },
                    data: menu_json,
                },
                types: {
                    default: {
                        icon: "far fa-folder icon-state-warning",
                        // icon: "ni ni-folder-fill icon-state-warning"
                    },
                    file: {
                        icon: "far fa-file icon-state-warning",
                        // icon: "ni ni-file-fill icon-state-warning"
                    },
                },
            });
        }
    ).fail(function () {
        NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
    });
}

function tambah() {
    $('#form-data')[0].reset();
    $('#modalForm').modal('show');
    tree_menu();
    $('#id_role').val('');
}

$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit');

    $.ajax({
        url : "/store-role",  
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

function edit(id) {
    $.ajax({
        url: '/edit-role/'+id,
        dataType: 'JSON',
        success: function(response) {
            if(response.status) {
                $('#modalForm').modal('show');
                let data = response.data;
                $('#id_role').val(id);
                $('#role').val(data.name)
                edit_tree_menu(id);
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}
