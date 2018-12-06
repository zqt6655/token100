<?php
/**
 * Created by PhpStorm.
 * User: collin
 * Date: 2018/12/5
 * Time: 16:42
 */

namespace App\Http\Controllers;



use Illuminate\Http\Request;

class ProjectSurvey extends Token
{
    protected function rule()
    {
        $rule = [];
        $rule['get'] = [ 'project_id' => 'required|integer'];
        $rule['add'] = [ 'project_id' => 'required|integer','title' => 'required|string','url'=>'required|string'];
        $rule['update'] = [ 'id' => 'required|integer','title' => 'required|string','url'=>'required|string'];
        $rule['delete'] = [ 'id' => 'required|integer'];
        return $rule;
    }

    public function get(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['get']);
        $model = $this->get_model();
        return $this->returnData($model->get($data['project_id']));
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
        $model = new \App\ProjectSurvey();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }

}