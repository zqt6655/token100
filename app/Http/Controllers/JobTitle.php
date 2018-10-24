<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobTitle extends Controller
{
    //
    public function get(){
        $model = new \App\JobTitle();
        return $this->returnData($model->get());
    }
}
