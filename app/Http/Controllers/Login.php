<?php

namespace App\Http\Controllers;

use App\Adminuser;
use App\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class Login extends Controller
{
    //
    public function login(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->login_rule());
        $model = $this->getModel();
        $login_data = $model->login($data);
        $cacheValue['user_id'] = $login_data['id'];
        $cacheValue['login_time'] = date('Y-m-d H:i:s');
        $cacheValue['permission'] = $login_data['permission'];
        $return_data['token'] = $this->saveToCache($cacheValue);
        $return_data['user_id'] = $login_data['id'];
        $return_data['permission'] = $login_data['permission'];
        if(!$login_data['name'])
            $return_data['name']  = $login_data['phone'];
        $return_data['name'] = $login_data['name'];
        $return_data['phone'] = $login_data['phone'];
        $return_data['avatar_url'] = $login_data['avatar_url'];
        $return_data['email'] = $login_data['email'];
        return $this->returnData($return_data);
    }
    public function send_sms()
    {
        $phone = Input::get('phone');
        $sms_token = Input::get('sms_token');
        if(strlen($phone) !=11){
            return $this->returnFail('手机号必须为11位');
        }
        if(!$this->get_cache('sms_'.$sms_token)){
            return $this->returnFail('请先输入图形验证码');
        }
        $this->sendSMS($phone);
        return $this->returnSuccess('发送成功');
    }

    //绑定邮箱
    public function bind_email(Request $request){
            $data = $request->all();
            $this->validate_all_input($data,$this->bind_email_rule());
            $model = $this->getModel();
            $model->bind_emali($data['user_id'],$data['email']);
            return $this->returnSuccess();
    }

    //注册
    public function register(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->reg_rule());
        //验证手机验证码是否正确
        $model = $this->getModel();
        $this->checkPhoneCode($data['phone'],$data['code']);
        $model->checkPhoneIsAvailable($data['phone']);
        $user_id = $model->add($data);
        //注册默认权限是3
        $cacheValue['user_id'] = $user_id;
        $cacheValue['login_time'] = date('Y-m-d H:i:s');
        $cacheValue['permission'] = 3;
        $return_data['token'] = $this->saveToCache($cacheValue);
        $return_data['user_id'] = $user_id;
        return $this->returnData($return_data);
    }
    protected function checkPhoneCode($phone,$code){
        $cache_code = $this->get_cache($phone);
        if($cache_code !=$code){
            $this->returnApiError('手机验证码错误');
            return;
        }
        //通过验证，则销毁验证码
        $this->set_cache($phone,'');
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

    protected function reg_rule(){
        return [
            'phone' => 'required|string|size:11',
            'code' => 'required|string|min:4',
            'password' => 'required|string|min:6',
        ];
    }
    protected function login_rule(){
        return [
            'user' => 'required',
            'password' => 'required|string|min:6',
        ];
    }
    protected function bind_email_rule(){
        return [
            'user_id' => 'required|integer',
            'email' => 'required|email',
        ];
    }
    protected function getModel(){
        return new Adminuser();
    }


}
