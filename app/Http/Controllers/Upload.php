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
//        echo 123;
//        die;
//        dd($_POST);
//        dd($_FILES);
//        $img = $request->file('files');
//        dd($img);
//        dd( $photo = $request->file('photo'));
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $rule = ['jpg', 'png', 'gif','jpeg','pdf'];
            $photo = $request->file('photo');
            $orig_name = $photo->getClientOriginalName();
            $extension = $photo->extension(); //$store_result = $photo->store('photo');
            if (!in_array($extension, $rule)) {
                return '格式不正确';
            }
            $houzui='__#__'.time().rand(100,999);
            $new_name = str_replace('.'.$extension,'',$orig_name).$houzui.'.'.$extension;

            $store_result = $photo->storeAs('photo', $new_name);
//            if($extension=='pdf'){
//                $store_result = $photo->storeAs('photo', 'test1.pdf');
//            }
//            $store_result = $photo->storeAs('photo', 'test1.jpg');
            $output = [ 'extension' => $extension, 'store_result' => $store_result ];
            print_r($output);
            exit();
        }
            exit('未获取到上传文件或上传过程出错');
//        $url_path = 'uploads/cover';
//        $rule = ['jpg', 'png', 'gif'];
//        if ($file->isValid()) {
//            $clientName = $file->getClientOriginalName();
//            $tmpName = $file->getFileName();
//            $realPath = $file->getRealPath();
//            $entension = $file->getClientOriginalExtension();
//            if (!in_array($entension, $rule)) {
//                return '图片格式为jpg,png,gif';
//            }
//            $newName = md5(date("Y-m-d H:i:s") . $clientName) . "." . $entension;
//            $path = $file->move($url_path, $newName);
//            $namePath = $url_path . '/' . $newName;
//            return $path;
//        }
    }

}