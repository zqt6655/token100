<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Mrgoon\AliSms\AliSms;

header("Access-Control-Allow-Origin: *");
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function returnData($data){
        $returnData['data'] = $data;
        $returnData['code'] = 0;
        $returnData['msg'] = 'success';
        return $returnData;
    }
    public function returnFail($msg='fail'){
        $returnData['data'] = [];
        $returnData['code'] = -1;
        $returnData['msg'] = $msg;
        return $returnData;
    }
    public function returnSuccess($msg='success'){
        $returnData['data'] = [];
        $returnData['code'] = 0;
        $returnData['msg'] = $msg;
        return $returnData;
    }
    public function returnApiError($message,$code=-1){
        throw new ApiException($message,$code);
    }
    public function set_cache($key,$value,$minute=60*24*30){
        return Cache::put($key,$value,$minute);
    }
    public function get_cache($key){
        return Cache::get($key);
    }
    public function validate_all_input($data,$rule){
        $validate = Validator::make($data, $rule);
        if($validate->fails())
        {
            $message = $validate->errors()->first();
            $this->returnApiError($message);
        }
    }
    public function sendSMS($phone){
        $code = rand(1000,9999);
        $alisms = new AliSms();
        $response = $alisms->sendSms($phone, config('aliyunsms.template_code'), ['code'=>$code ]);
        if($response->Code=='OK'){
            $this->set_cache($phone,$code,5);
            return;
        }
        $this->returnApiError('系统繁忙，请一分钟后重试');
    }
    protected  function generateToken(){
        //产生32个随机字符串
        $randChars = $this->generateRadomString(32);
        //系统时间戳
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        return md5($randChars . $timestamp);
    }
    protected function generateRadomString($length=32){
        $str = '';
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }

    /**
     * GET 请求
     * @param string $url
     */
    public function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);

        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }
}
