<?php

namespace App\Http\Controllers;

use App\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Login extends Controller
{
    //
    public function login(){
        $cacheValue['user_id'] = 0;
        $cacheValue['login_time'] = date('Y-m-d H:i:s');
        $data['token'] = $this->saveToCache($cacheValue);
    }
    //注册
    public function register(Request $request){
        $data = $request->all();

    }
    protected  function  saveToCache($cacheValue){
        $token = $this->generateToken();
        $value = json_encode($cacheValue);
        $this->set_cache($token,$value);
        //将token值也存在数据库中
        $data['key'] = $token;
        $data['value'] = $value;
        $data['add_time'] = date('Y-m-d H:i:s');
        $model = new Cache();
        $model->add($data);
        return $token;

    }
    protected  function generateToken(){
        //产生32个随机字符串
        $randChars = $this->generateRadomString(32);
        //系统时间戳
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        return md5($randChars . $timestamp);
    }
    protected function generateRadomString($length){
        $str = '';
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }
    protected function rule(){
        return [
            'id' => 'integer',
            'phone' => 'required|string|size:11',
            'code' => 'required|string',
        ];
    }


}
