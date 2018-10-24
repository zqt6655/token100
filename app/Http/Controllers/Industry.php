<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2018/10/22
 * Time: 16:17
 */

namespace App\Http\Controllers;
header("Access-Control-Allow-Origin: *");
class Industry extends Controller
{

    public function get(){
        $model =  new \App\Industry();
        $indus = $model->get_list();
        return $this->returnData($indus);
    }



}