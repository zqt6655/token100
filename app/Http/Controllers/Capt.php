<?php

namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;

class Capt extends Controller
{
    //
    public function get(){
        $cap = (string)rand(1000,9999);
        $builder = new CaptchaBuilder($cap);
        $builder->setMaxBehindLines(1);
        $builder->setMaxOffset(3);
        //可以设置图片宽高及字体
        $builder->build($width = 250, $height = 70, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();
        //把内容存入cache
        $cap_token = $this->generateToken();
        $this->set_cache($cap_token,$phrase,3);
        //生成图片
//        header("Cache-Control: no-cache, must-revalidate");
//        header('Content-Type: image/jpeg');
//        header("cap_token: $cap_token");
        $data['img_base64'] = $builder->inline();
        $data['capt_token'] = $cap_token;
        $data['capt'] = $cap;
        return $this->returnData($data);
    }
    public function validate_capt(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule());
        $cache_capt = $this->get_cache($data['capt_token']);
        if($data['capt']==$cache_capt){
            //清空验证码缓存
            $this->set_cache($data['capt_token'],'',1);
            //写入短信验证码缓存,5分钟有效期
            $sms_token = $this->generateToken();
            $this->set_cache($sms_token,'12',4);
            $return_data['sms_token'] = $sms_token;
            return $this->returnData($return_data);
        }else{
            return $this->returnFail('验证码错误');
        }

    }
    protected function rule(){
        return [
            'capt' => 'required|string|size:4',
            'capt_token' => 'required|string',
        ];
    }
}
