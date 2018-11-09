<?php
/**
 * Created by PhpStorm.
 * User: collin
 * Date: 2018/11/8
 * Time: 14:15
 */

namespace App\Http\Controllers;


use App\ProjectGrap;

class Data extends Controller
{
    public function add(){
        $model = new ProjectGrap();
        $data = $model->add();
        return $this->returnData($data);
        print_r($data);
    }

}