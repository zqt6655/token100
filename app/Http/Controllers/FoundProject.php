<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FoundProject extends Token
{

    protected function rule()
    {
        $rule = [];
        $rule['get'] = [ 'project_id' => 'required|integer'];
        $rule['delete'] = [ 'id' => 'required|integer'];
        $rule['add_back'] = [ 'project_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer'];
        $rule['add_buy'] = [ 'project_id' => 'required|integer','found_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer','invest_stage'=>'required|string','total_price'=>'required|numeric'];
        $rule['add_sell'] = [ 'project_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer','info'=>'required|string'];
        return $rule;
    }
    //获取项目投资记录
    public function get_by_project_id(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['get']);
        $model = $this->getModel();
        return $this->returnData($model->get_by_project_id($data['project_id']));
    }
    public function add_back(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['add_back']);
        $model = $this->getModel();
        return $this->returnData($model->add_back($data));
    }
    public function get_back_info(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['get']);
        $model = $this->getModel();
        return $this->returnData($model->get_back_info($data['project_id']));
    }
    public function get_buy_info(){
        $model = $this->getModel();
        return $this->returnData($model->get_buy_info());
    }
    public function get_sell_info(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['get']);
        $model = $this->getModel();
        $data = $model->get_sell_info($data['project_id']);
        //由于需要额外返回一个字段，所以组装成这种格式
        $returnData['data'] = $data['data'];
        $returnData['num_info'] = $data['num_info'];
        $returnData['code'] = 0;
        $returnData['msg'] = 'success';
        return $returnData;
    }
    public function add_buy(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['add_buy']);
        $model = $this->getModel();
        return $this->returnData($model->add_buy($data));
    }

    public function add_sell(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['add_sell']);
        $model = $this->getModel();
        return $this->returnData($model->add_sell($data));
    }
    public function delete(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['delete']);
        $model = $this->getModel();
        return $this->returnData($model->delete_by_id($data['id']));
    }

    protected function getModel(){
        $model = new \App\FoundProject();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }
}
