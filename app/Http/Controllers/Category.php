<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Category extends Controller
{
    //
    public function get(){
        $model = new \App\Category();
        $data = $model->get_list();
        return $this->returnData($data);
    }
}
