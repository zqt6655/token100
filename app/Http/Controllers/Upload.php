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
    public function upload_img(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $rule = ['jpg', 'png', 'gif', 'jpeg', 'pdf'];
            $photo = $request->file('file');
            $orig_name = $photo->getClientOriginalName();
            $extension = $photo->extension(); //$store_result = $photo->store('photo');
            if (!in_array($extension, $rule)) {
                return $this->returnFail('格式不正确');
            }
            $houzui = '__abc__' . time() . rand(100, 999);
            $new_name = str_replace('.' . $extension, '', $orig_name) . $houzui . '.' . $extension;
            if ($extension == 'pdf') {
                $store_result = $photo->storeAs('pdf', $new_name);
            } elseif ($extension == 'jpeg') {
                $new_name = str_replace('.jpg', '', $orig_name) . $houzui . '.jpg';
                $store_result = $photo->storeAs('photo', $new_name);
            } else {
                $store_result = $photo->storeAs('photo', $new_name);
            }
            $data['file_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/uploads' . $store_result;
            return $this->returnData($data);
        }
        return $this->returnFail('未获取到上传文件或上传过程出错');
    }

}