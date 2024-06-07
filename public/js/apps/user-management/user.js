var table = NioApp.DataTable('#dt-table', {
    serverSide: true,
    processing: true,
    responsive: false,
    searchDelay: 500,
    scrollX: true,
    ajax: {
        url: '/datatable-user-management'
    },
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'name', name: 'u.name'},
        {data: 'nama_mall', name: 'mm.nama'},
        {data: 'username', name: 'u.username'},
        {data: 'email', name: 'u.email'},
        {data: 'telp', name: 'u.telp'},
        {data: 'roles', name: 'r.nama'},
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
                                <li><a class="btn" onclick="aktivasi(${full['id']})"><em class="icon ni ni-lock-alt"></em><span>Activation</span></a></li>
                                <li><a class="btn" onclick="reset_password(${full['id']})"><em class="icon ni ni-security"></em><span>Reset Password</span></a></li>
                            </ul>
                        </div>
                    </div>`;
            }
        },
    ] 
});

function reset_password(id) {
    $.ajax({
        url: '/reset-password/'+id,
        dataType: 'json',
        success: function(response) {
            if(response.status) {
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

function aktivasi(id) {
    $.ajax({
        url: '/user-activation/'+id,
        dataType: 'json',
        success: function(response) {
            if(response.status) {
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

$('#mall').select2({
    placeholder: 'Select Mall',
    allowClear: true,
    dropdownParent: $('#modalForm'),
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
})

$('#role').select2({
    placeholder: 'Select Role',
    allowClear: true,
    dropdownParent: $('#modalForm'),
    ajax: {
        url: '/data-role-user',
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
                        id: item.id
                    }
                })
            };
        },
        cache: true
    }
})

$('#level').select2({
    placeholder: 'Select Level',
    allowClear: true,
    dropdownParent: $('#modalForm'),
    ajax: {
        url: '/data-level-user',
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
                        id: item.id
                    }
                })
            };
        },
        cache: true
    }
})

function tambah() {
    $('#form-data')[0].reset();
    $('#id_user').val('');
    $('#username').val('');
    $('#mall').val('').change();
    $('#role').val('').change();
    $('#level').val('').change();
    $("#role").attr('disabled', false);
    $("#level").attr('disabled', false);
    $('#modalForm').modal('show');
}

$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit');

    $.ajax({
        url : "/register",  
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
                $('#mall').val('').change();
                $('#role').val('').change();
                $('#level').val('').change();
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
        url: '/edit-user/'+id,
        dataType: 'json',
        success: function(response) {
            if(response.status) {
                $('#modalForm').modal('show');
                let data = response.data;
                $('#id_user').val(id);
                $('#nama').val(data.name);
                $('#email').val(data.email);
                $('#telp').val(data.telp);
                $('#username').val(data.username);

                $("#mall").empty().append(`<option value="${data.uid_mall}">${data.nama_mall}</option>`).val(data.uid_mall).trigger('change');
                $("#role").empty().append(`<option value="${data.id_role}">${data.nama_role}</option>`).val(data.id_role).trigger('change');
                $("#level").empty().append(`<option value="${data.id_level}">${data.nama_level}</option>`).val(data.id_level).trigger('change');
            }
        },
        error: function(error) {
            console.log(error)
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    })
}