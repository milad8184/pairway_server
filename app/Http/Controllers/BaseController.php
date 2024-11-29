<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = null, $code = 200)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 200)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function validateCompany($id)
    {
        $companyid = Auth()->user()->companyid;
        return $companyid == $id;
    }

    public function hasUserAuthority($id)
    {
        $userid = Auth()->user()->id;
        $role = Auth()->user()->role;
        return $role == "admin" || $userid == $id;
    }

    public function getUser()
    {
        return Auth()->user();
    }
}