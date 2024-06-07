<?php 

    if(!function_exists('js_tree')){
        function js_tree() {
            return '<script src="'. asset('assets/js/libs/jstree.js?ver=3.1.0') .'"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jstreegrid/3.10.2/jstreegrid.js"
            integrity="sha512-X6Gxkg/DfpLDVkviLz0tOU9sUECOVif8FTDKX4IJi6vbCNQlqWZ2dwRvCqetOJlDzijiLWfH286XYsmBDejkwQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jstreegrid/3.10.2/jstreegrid.min.js"
            integrity="sha512-984rgpiU2asdjWnDK870ho0raSWqYVU9yAK/Uc5dPE22zZPChgf/jOEpCbM2TXRmBy6vCoCh39EWziAno1XKNQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
        }
    }

    if(!function_exists('css_tree')) {
        function css_tree() {
            return '<link rel="stylesheet" href="'. asset('assets/css/libs/jstree.css?ver=3.1.0') .'">';
        }
    }
    
    if(!function_exists('js_summernote')) {
        function js_summernote() {
            return '<script src="'. asset('assets/js/libs/editors/summernote.js?ver=3.1.0').'"></script>';
        }
    }

    if(!function_exists('css_summernote')) {
        function css_summernote() {
            return '<link rel="stylesheet" href="'. asset('assets/css/editors/summernote.css?ver=3.1.0') .'">';
        }
    }

    if(!function_exists('js_qrcode')) {
        function js_qrcode() {
            return '<script src="'. asset('assets/js/libs/html5qrcode.js').'"></script>';
        }
    }

    if(!function_exists('js_webcam')) {
        function js_webcam() {
            return '<script src="'. asset('assets/js/libs/webcam.min.js').'"></script>';
        }
    }

    if(!function_exists('js_datatable_button')) {
        function js_datatable_button() {
            return '<script src="'. asset('assets/js/libs/datatable-btns.js') .'"></script>';
        }
    }

    function counter($kode)
	{
		$data = DB::table('ms_counter')->where('kode', $kode)->first();

		if(!empty($data)){
			$set_no = (int) $data->counter + 1;
			if(strlen($set_no) <= 3) {
				if (strlen($set_no) == 1) {
	                $counter = "000" . $set_no;
	            } elseif (strlen($set_no) == 2) {
	                $counter = "00" . $set_no;
	            } elseif (strlen($set_no) == 3) {
	                $counter = "0" . $set_no;
				}
            } else {
                $counter = $set_no;
            }
		}else{
			$counter = '0001';
		}

        // update counter
        DB::table('ms_counter')->where('kode', $kode)->update(['counter' => $counter]);

		return $counter;
	}

?>