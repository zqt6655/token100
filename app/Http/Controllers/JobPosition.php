<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobPosition extends Controller
{
    //
    public function get(){
        $model = new \App\JobPosition();
        return $this->returnData($model->get());
    }
}
