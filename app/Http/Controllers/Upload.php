<?php
/**
 * Created by PhpStorm.
 * User: 郑庆添
 * Date: 2018/10/22
 * Time: 18:06
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class Upload extends Controller
{
    public $size=1024*1024*5;
    public $appid='';
    public $access_token='';
    public function upload(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $rule = ['jpg', 'png',  'jpeg', 'pdf'];
            $file = $request->file('file');
            $orig_name = $file->getClientOriginalName();
            $extension = $file->extension(); //$store_result = $file->store('photo');
            if (!in_array($extension, $rule)) {
                return $this->returnFail('格式不正确');
            }
            $size = $file->getSize();
            if($size>$this->size){
                return $this->returnFail('文件不能超过5M');
            }
            $houzui = time() . rand(100, 999).'__collinstar__';
            $new_name = $houzui.str_replace('.' . $extension, '', $orig_name) . '.' . $extension;
            if ($extension == 'pdf') {
                $store_result = $file->storeAs('pdf', $new_name);
            } elseif ($extension == 'jpeg') {
                $new_name = $houzui .str_replace('.jpg', '', $orig_name) .  '.jpg';
                $store_result = $file->storeAs('photo', $new_name);
            } else {
                $store_result = $file->storeAs('photo', $new_name);
            }
            $data['file_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/uploads/' . $store_result;
            return $this->returnData($data);
        }
        return $this->returnFail('未获取到上传文件或上传过程出错');
    }
    public function preshow(){
        return response()->file('uploads/pdf/1541753828463__collinstar__154164368606317381.pdf');
    }
    public function download(){
        return response()->download('uploads/pdf/1541753828463__collinstar__154164368606317381.pdf');
    }

    //获取微信接口注入权限验证配置
    public function get_wx_auth_config(){
        $this->appid = config('wx_config.wx_appid');
        $jsapi_ticket = $this->getJsTicket();
        $timestamp = time();
        $noncestr = $this->generateRadomString(16);
        $arrdata = array("timestamp" => $timestamp, "noncestr" => $noncestr, "jsapi_ticket" => $jsapi_ticket);
        $sign = $this->getSignature($arrdata);
        if (!$sign)
            return $this->returnFail('与微信服务器交互超时，请重试一次');
        $signPackage = array(
            "appId"     => $this->appid,
            "nonceStr"  => $noncestr,
            "timestamp" => $timestamp,
            "signature" => $sign,
            "jsApiList" => ['updateAppMessageShareData','updateTimelineShareData','onMenuShareTimeline','onMenuShareAppMessage']
        );
        return $this->returnData($signPackage);
    }

    public function getJsTicket(){
        $cache_name = 'wechat_jsapi_ticket'.$this->appid;
        if( $js_ticket = $this->get_cache($cache_name)){
            return $js_ticket;
        }
        $this->access_token = $this->get_wx_access_token();
        $js_ticket_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->access_token.'&type=jsapi';
        $result = $this->http_get($js_ticket_url);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $errCode = $json['errcode'];
                $errMsg = $json['errmsg'];
                $this->returnApiError('错误码:'.$errCode.'错误信息:'.$errMsg);
            }
            $jsapi_ticket = $json['ticket'];
            $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
            $expire = intval($expire/60);
            $this->set_cache($cache_name,$jsapi_ticket,$expire);
            return $jsapi_ticket;
        }
        $this->returnApiError('获取jsapi_ticket失败');
    }

    /**
     * 获取签名
     * @param array $arrdata 签名数组
     * @param string $method 签名方法
     * @return boolean|string 签名值
     */
    public function getSignature($arrdata,$method="sha1") {
        if (!function_exists($method)) return false;
        ksort($arrdata);
        $paramstring = "";
        foreach($arrdata as $key => $value)
        {
            if(strlen($paramstring) == 0)
                $paramstring .= $key . "=" . $value;
            else
                $paramstring .= "&" . $key . "=" . $value;
        }
        $Sign = $method($paramstring);
        return $Sign;
    }
    protected function get_wx_access_token(){
        $cachename = 'wechat_access_token'.$this->appid;
        if($this->access_token = $this->get_cache($cachename)){
            return $this->access_token;
        }
        $appsecret = config('wx_config.wx_secret');
        $access_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$appsecret;
        $result = $this->http_get($access_token_url);
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $errCode = $json['errcode'];
                $errMsg = $json['errmsg'];
                $this->returnApiError('错误码:'.$errCode.'错误信息:'.$errMsg);
            }
            $this->access_token = $json['access_token'];
            $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
            $expire = intval($expire/60);
            $this->set_cache($cachename,$this->access_token,$expire);
            return $this->access_token;
        }
        $this->returnApiError('获取access_token失败');
    }

}