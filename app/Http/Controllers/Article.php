<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class Article extends Token
{
    //
    protected function rule(){
        return [
            'id' => 'integer',
            'title' => 'required|string',
            'author' => 'required|string',
            'img' => 'required|string',
            'summary' => 'required|string',
            'publish_time' => 'required|string',
            'content' => 'required|string',
        ];
    }
    public function get(){
        $model = $this->getModel();
        return $this->returnData($model->get());
    }
    public function detail(){
        $id = Input::get('id');
        if(!is_numeric($id)){
           return $this->returnFail('id必须为整数');
        }
        $model = $this->getModel();
        return $this->returnData($model->detail($id));
    }
    public function pub_cancel(){
        $id = Input::get('id');
        $status = Input::get('status');
        if(is_numeric($id) && is_numeric($status)){
           $model = $this->getModel();
           $model->publish_or_cancel($id,$status);
           return $this->returnSuccess();
        }
        return $this->returnFail('id或者status必须为整数');

    }
    public function delete(){
        $id = Input::get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $model = $this->getModel();
        $model->delete_by_id($id);
        return $this->returnSuccess();
    }
    public function add(Request $request){
        $data = $request->all();
        $result = $this->validate_input($data);
        if($result !='ok'){
            return $this->returnFail($result);
        }
        $model = $this->getModel();
        $result = $model->add($data);
        if($result){
            return $this->returnSuccess();
        }else{
            return $this->returnFail('系统繁忙，请重试一次');
        }
    }
    public function update(Request $request){
        $data = $request->all();
        $result = $this->validate_input($data);
        if($result !='ok'){
            return $this->returnFail($result);
        }
        $model = $this->getModel();
        $result = $model->update_by_id($data,$data['id']);
        if($result){
            return $this->returnSuccess();
        }else{
            return $this->returnFail('系统繁忙，请重试一次');
        }
    }

    protected function getModel(){
        $model = new \App\Article();
        return $model;
    }
    public function validate_input($data){
        $validate = Validator::make($data, $this->rule());
        if($validate->fails())
        {
            $message = $validate->errors()->first();
            return $message;
        }
        return 'ok';
    }
}
