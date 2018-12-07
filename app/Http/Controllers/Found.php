<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Found extends Token
{

    protected function rule()
    {
        $rule = [];
        $rule['get'] = [];
        $rule['add'] = ['name' => 'required|string','account'=>'required|numeric','unit' => 'required|string','plan_account'=>'required|numeric','start_time'=>'required|string','end_time'=>'required|string'];
        $rule['update'] = [ 'id' => 'required|integer','name' => 'required|string','account'=>'required|numeric','unit' => 'required|string','plan_account'=>'required|numeric','start_time'=>'required|string','end_time'=>'required|string'];
        $rule['delete'] = [ 'id' => 'required|integer'];
        return $rule;
    }

    public function get(){
        $model = $this->get_model();
        return $this->returnData($model->get());
    }
    public function update(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['update']);
        $model = $this->get_model();
        return $this->returnData($model->update_by_id($data,$data['id']));
    }
    public function add(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['add']);
        $model = $this->get_model();
        return $this->returnData($model->add($data));
    }
    public function delete(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['delete']);
        $model = $this->get_model();
        return $this->returnData($model->delete_by_id($data['id']));
    }

    protected function get_model(){
        $model = new \App\Found();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }

}
