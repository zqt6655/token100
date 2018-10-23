<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2018/10/21
 * Time: 22:13
 */

namespace App\Http\Controllers;


class Api extends Controller
{
    public function get_industry_list(){
        $model =  new \App\Industry();
        $indus = $model->get_industry_list();
        $data['data'] = $indus;
        $data['code'] = 0;
        return $data;
    }
}