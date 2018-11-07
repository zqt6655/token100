<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
header('Access-Control-Allow-Headers:Content-Type,Content-Length,Accept-Encoding,X-Requested-with,Origin,token,Authorization');//自定义其他的头
class Token extends Controller
{
    public $user_id=0;
    public $permission=0;
    //用于检测用户是否携带token
    public function __construct(Request $request)
    {
        $token = $request->header('token');
        if(!$token){
            $token = $request->get('token');
            if(!$token){
                $token = $request->post('token');
            }
        }

        $this->check($token);
    }
    protected function check($token){
        $value = $this->get_cache($token);
        if(!$value){
            $this->returnApiError('Token值不存在，请重新登录',-99);
        }
        $this->user_id = $this->get_value_from_token($value,'user_id');
        $this->permission = $this->get_value_from_token($value,'permission');
    }
    protected function get_value_from_token($value,$key){
        if (!is_array($value)) {
            $value = json_decode($value, true);
        }
        if (array_key_exists($key, $value)) {
            return $value[$key];
        }
       return 0;
    }

}
