$(document).ready(function() {

    let uid = $('#uid').val();
    if(uid) {
        $.ajax({
            url: '/company/edit/'+uid,
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    let data = response.data;
                    if(data.photo) {
                        $('#preview_image').attr('src', 'storage/'+data.photo);
                    }
                    let filePath = data.photo;
                    filePath = filePath.replace("uploads/company/photo/", "");
                    $('#filename_photo').text(filePath);
                    $('#filename_photo').attr("href", 'storage/'+data.photo);

                    $('#uid').val(data.uid);
                    $('#name').val(data.name);
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
});


$('#preview_image').attr('src', "https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/600px-No_image_available.png");

$('#photo').on('change', function() {

    // The recommended plugin to animate custom file input: bs-custom-file-input, is what bootstrap using currently
    // bsCustomFileInput.init();

    // Set maximum filesize
    var maxSizeMb = 5;

    // Get the file by using JQuery's selector
    var file = $('#photo')[0].files[0];

    // Make sure that a file has been selected before attempting to get its size.
    if(file !== undefined) {

        // Get the filesize
        var totalSize = file.size;

        // Convert bytes into MB
        var totalSizeMb = totalSize  / Math.pow(1024,2);

        // Check to see if it is too large.
        if(totalSizeMb > maxSizeMb) {

            // Create an error message
            var errorMsg = 'File too large. Maximum file size is ' + maxSizeMb + ' MB. Selected file is ' + totalSizeMb.toFixed(2) + ' MB';
            NioApp.Toast(errorMsg, 'warning', {position: 'top-right'});

            // Clear the value
            $('#photo').val('');
            $('#preview_image').attr('src', "https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/600px-No_image_available.png");
            $('#photo').next('label').html('Choose file');
        }else{
        	readURL(this,'preview_image');
        }
    }

});

const readURL = (input,el) => {
	if (input.files && input.files[0]) {
		const reader = new FileReader()
		reader.onload = (e) => {
			$('#'+el).removeAttr('src')
			$('#'+el).attr('src', e.target.result)
		}
		reader.readAsDataURL(input.files[0])
	}
};

$('#form-data').submit(function(e) {
    e.preventDefault();
    formData = new FormData($(this)[0]);
    var btn = $('#btn-submit');

    $.ajax({
        url : "/company/store",  
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
                    window.location.href = '/company';
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
});

$('#phone').keyup(function() {
    $(this).val(function (index, value) {
      return value.replace(/\D/g, "");
    });
});
