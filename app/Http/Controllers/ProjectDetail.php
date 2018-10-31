<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ProjectDetail extends Token
{
    //
    protected function rule(){
        return [
            'id' => 'integer',
            'project_contacts' => 'string|max:255',
            'project_introduce' => 'string|max:255',
            'problem' => 'string|max:255',
            'framework' => 'string|max:255',
            'strength' => 'string|max:255',
            'tokenmodel' => 'string|max:255',
            'project_strategy' => 'string|max:255',
            'project_community' => 'string|max:255',
            'investplan' => 'string|max:255',
            'project_otherinfo' => 'string|max:255',
        ];
    }
    public function get(){
        $project_id = Input::get('project_id');
        if(!is_numeric($project_id)){
            return $this->returnFail('project_id必须为整数');
        }
        $model = $this->getModel();
        return $this->returnData($model->get($project_id));
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
        $model = new \App\ProjectDetail();
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
