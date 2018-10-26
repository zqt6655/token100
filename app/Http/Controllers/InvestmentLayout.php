<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class InvestmentLayout extends Token
{

    public function get(){
        $model = $this->getModel();
        return $this->returnData($model->get());
    }
    public function get_by_industry(){
        $model = $this->getModel();
        return $this->returnData($model->get_by_industry());
    }
    public function get_by_alp(){
        $model = $this->getModel();
        return $this->returnData($model->get_by_alp());
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
        $model->add($data);
        return $this->returnSuccess();
    }
    public function update(Request $request){
        $data = $request->all();
        $this->validate_input($data);
        $model = $this->getModel();
        $model->update_by_id($data,$data['id']);
        return $this->returnSuccess();
    }

    protected function getModel(){
        $model = new \App\InvestmentLayout();
        return $model;
    }
    protected function validate_input($data){
        $validate = Validator::make($data, $this->rule());
        if($validate->fails())
        {
            $message = $validate->errors()->first();
            $this->returnApiError($message);
        }
    }
    protected function rule(){
        return [
            'id' => 'integer',
            'title' => 'required|string',
            'link' => 'required|string',
            'img' => 'required|string',
            'summary' => 'required|string',
            'industry_id' => 'required|integer',
        ];
    }
}
