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
    /**
     * 上报尚未建立联系之时用户发送的信息
     * @url ims/api/v1/formsave
     * @method POST
     * @param int $openid openid
     * @param int $form_id formid
     * @return int $code 状态码
     * @return string $msg 描述信息
     */
    public function formsave(){
            return [
                'code' => -1,
                'msg'  => "插入失败"
            ];
    }
}