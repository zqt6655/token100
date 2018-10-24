<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
header("Access-Control-Allow-Origin: *");
class Category extends Controller
{
    //
    public function get(){
        $model = new \App\Category();
        $data = $model->get_list();
        return $this->returnData($data);
    }
}
