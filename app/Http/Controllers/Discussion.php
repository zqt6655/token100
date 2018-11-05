<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class Discussion extends Token
{
    //
    protected function rule(){
        return [
            'id' => 'integer',
            'title' => 'required|string|max:64',
            'content' => 'required|string|max:1024',
            'pics' => 'string|max:1024',
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
        $this->validate_all_input($data,$this->rule());
        $model = $this->getModel();
        return $this->returnData( $model->add($data) );
    }
    public function update(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule());
        if(!isset($data['id'])){
            return $this->returnFail('id不能为空');
        }
        $model = $this->getModel();
        $model->update_by_id($data,$data['id']);
        return $this->returnSuccess();
    }

    protected function getModel(){
        $model = new \App\Discussion();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }
}
