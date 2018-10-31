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
        $this->validate_input($data);
        $model = $this->getModel();
        return $this->returnData( $model->add($data) );
    }
    public function update(Request $request){
        $data = $request->all();
        $this->validate_input($data);
        if(!isset($data['id'])){
            return $this->returnFail('id不能为空');
        }
        $model = $this->getModel();
        $model->update_by_id($data,$data['id']);
        return $this->returnSuccess();
    }

    protected function getModel(){
        $model = new \App\Lab();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }
    public function validate_input($data){
        $validate = Validator::make($data, $this->rule());
        if($validate->fails())
        {
            $message = $validate->errors()->first();
            $this->returnApiError($message);
        }
    }
}
