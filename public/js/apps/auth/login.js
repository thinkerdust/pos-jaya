$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit');

    $.ajax({
        url : "/authenticate",  
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
                setTimeout(function() {
                    window.location.href = '/dashboard';
                }, 2000)
            }else{
                btn.attr('disabled', false);
                btn.html('Sign in');
                NioApp.Toast(response.message, 'warning', {position: 'top-right'});
            }
        },
        error: function(error) {
            console.log(error)
            btn.attr('disabled', false);
            btn.html('Sign in');
            NioApp.Toast('Error while fetching data', 'error', {position: 'top-right'});
        }
    });
});