<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ProjectLab extends Token
{
    //
    protected function rule(){
        return [
            'project_id' => 'integer',
            'lab_id' => 'required|string',
        ];
    }
    public function update(Request $request){
        $data = $request->all();
        $this->validate_input($data);
        if(!isset($data['project_id'])){
            return $this->returnFail('project_id不能为空');
        }
        $model = $this->getModel();
        $model->add($data['lab_id'],$data['project_id']);
        return $this->returnSuccess();
    }
    public function get(){
        $project_id = Input::get('project_id');

        if(!is_numeric($project_id)){
            return $this->returnFail('project_id必须为整数');
        }
        $model = $this->getModel();
        return $this->returnData($model->get($project_id));
    }

    protected function getModel(){
        $model = new \App\ProjectLab();
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
