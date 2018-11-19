<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class Member extends Token
{
    //
    protected function rule(){
        return [
            'id' => 'integer',
            'member_name' => 'required|string|max:127',
            'member_position' => 'required|string|max:255',
            'member_introduce' => 'string|max:255',
            'avatar_url' => 'string|max:255',
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
        $model = new \App\Member();
        return $model;
    }
}
