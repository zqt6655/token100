<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectTeam extends Controller
{
    //
    protected function rule()
    {
        $rule = [];
        $rule['get'] = [ 'project_id' => 'required|integer'];
        $rule['add'] = [ 'project_id' => 'required|integer','introduce' => 'required|string'];
        $rule['update'] = [ 'team_id' => 'required|integer','introduce' => 'required|string'];
        $rule['delete'] = [ 'team_id' => 'required|integer'];
        return $rule;
    }

    public function get(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['get']);
        $model = $this->get_model();
        return $this->returnData($model->get_by_project_id($data['project_id']));
    }
    public function update(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['update']);
        $model = $this->get_model();
        return $this->returnData($model->update_by_team_id($data['team_id'],$data['introduce']));
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
        return $this->returnData($model->delete_by_team_id($data['team_id']));
    }

    protected function get_model(){
        $model = new \App\ProjectTeam();
        return $model;
    }
}
