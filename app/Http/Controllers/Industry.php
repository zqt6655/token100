<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2018/10/22
 * Time: 16:17
 */

namespace App\Http\Controllers;

class Industry extends Controller
{

    public function get_industry_list(){
        $model =  new \App\Industry();
        $indus = $model->get_industry_list();
        return $this->returnData($indus);
    }
    /**
     * 返回某个行业下的人脉资源
     */
    public function get_industry_relationship($id){
        //如果没有id，说明查找的所有行业下的人脉
        if(empty($id)){

        }

    }
}