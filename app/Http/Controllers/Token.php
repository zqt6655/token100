<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Token extends Controller
{
    //用于检测用户是否携带token
    public function __construct(Request $request)
    {
        $token = $request->header('token');

        dd($token);
    }
    public function get(){
        echo 1;
    }

}
