<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class Token extends Controller
{
    public $user_id=0;
    public $permission=0;
    //用于检测用户是否携带token
    public function __construct(Request $request)
    {
        $token = $request->header('token');
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
