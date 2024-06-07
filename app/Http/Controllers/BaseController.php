<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'status' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404)
    {
    	$response = [
            'status'    => false,
            'data'      => [],
            'message'   => $error,
        ];

        return response()->json($response, $code);
    }

    public function ajaxResponse($status, $message, $result=[])
    {
    	$response = [
            'status' => $status,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response);
    }
}