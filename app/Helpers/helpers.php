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

    function counter($kode) {

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

    function cleanTag($tag = '') {
        //add dashes
        $tag = str_replace(' ', '-', $tag);
    
        //change to lowercase
        $tag = strtolower($tag);
    
        //remove none alpha-numeric characters - not sure how good this will be with none latin languages
        //$tag = preg_replace('/[^a-zA-Z0-9.]+/', '', $tag);
        return $tag;
    }

    function hourMinuteSeconds($duration = 0, $type = '') {

        $hours = 0;
        $minutes = 0;
        $seconds = 0;
    
        //avoid divisiob by zero
        if ($duration > 0) {
            $hours = floor($duration / 3600);
            $minutes = floor(($duration / 60) % 60);
            $seconds = $duration % 60;
        }
        switch ($type) {
        case 'hours':
            return $hours;
        case 'minutes':
            return $minutes;
        case 'seconds':
            return $seconds;
        }
    
    }

    function runtimeDate($date = '', $date_format = 'Y-m-d') {

        $alternative = '---';

        if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == '---') {
            return $alternative;
        }
    
        if ($date != '') {
            return \Carbon\Carbon::parse($date)->format($date_format);
        }
    
        return $alternative;
    }

    function listMonth() {

        $month = array(
            1 => 'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            );

        return $month;
    }

    function listYear() {
        
        $start = '2020';
        $end = date('Y');
        $array = array();
        for($start; $start <= $end; $start++) {
            $array[$start] = $start;
        }

        return $array;
    }

?>