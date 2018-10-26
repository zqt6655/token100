<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class Lab extends Token
{
    //
    protected function rule(){
        return [
            'id' => 'integer',
            'parent_id' => 'integer',
            'user_id' => 'integer',
            'lab_name' => 'required|string|max:64',
        ];
    }
    public function get(){
        $model = $this->getModel();
        return $this->returnData($model->get());
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
        $model = new \App\Lab();
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
