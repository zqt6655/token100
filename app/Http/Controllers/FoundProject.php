<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FoundProject extends Token
{

    //不同的接口，验证规则不同
    protected function rule()
    {
        $rule = [];
        $rule['get'] = [ 'project_id' => 'required|integer'];
        $rule['rate_of_return'] = [ 'project_id' => 'required|integer','days'=>'required|integer'];
        $rule['found_detail'] = [ 'id' => 'required|integer'];
        $rule['delete'] = [ 'id' => 'required|integer'];
        $rule['add_back'] = [ 'project_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer'];
        $rule['update_back'] = [ 'id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer'];
        $rule['add_buy'] = [ 'project_id' => 'required|integer','found_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer','invest_stage'=>'required|string','total_price'=>'required|numeric'];
        $rule['update_buy'] = [ 'id' => 'required|integer','found_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer','invest_stage'=>'required|string','total_price'=>'required|numeric'];
        $rule['add_sell'] = [ 'project_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer','info'=>'required|string'];
        $rule['update_sell'] = [ 'id' => 'required|integer','found_id' => 'required|integer','pay_coin_time' => 'required|string','num'=>'required|integer','total_price'=>'required|numeric'];
        return $rule;
    }
    //获取项目投资记录
    public function get_by_project_id(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['get']);
        $model = $this->getModel();
        return $this->returnData($model->get_by_project_id($data['project_id']));
    }
    //添加回币记录
    public function add_back(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['add_back']);
        $model = $this->getModel();
        return $this->returnData($model->add_back($data));
    }
    public function update_back(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['update_back']);
        $model = $this->getModel();
        return $this->returnData($model->update_back($data,$data['id']));
    }
    //当用户点击回币时，查询当前项目应剩多少回币
    public function get_back_info(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['get']);
        $model = $this->getModel();
        return $this->returnData($model->get_back_info($data['project_id']));
    }
    //当用户点击买入时，查询当前项目对应的基金以及剩余可买
    public function get_buy_info(){
        $model = $this->getModel();
        return $this->returnData($model->get_buy_info());
    }
    //当用户点击卖出时，查询当前项目应该返回的基金列表
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
    //添加买入记录
    public function add_buy(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['add_buy']);
        $model = $this->getModel();
        return $this->returnData($model->add_buy($data));
    }
    public function update_buy(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['update_buy']);
        $model = $this->getModel();
        return $this->returnData($model->update_buy($data,$data['id']));
    }

    //添加卖出记录
    public function add_sell(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['add_sell']);
        $model = $this->getModel();
        return $this->returnData($model->add_sell($data));
    }
    //修改卖出记录
    public function update_sell(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['update_sell']);
        $model = $this->getModel();
        return $this->returnData($model->update_sell($data,$data['id']));
    }
    public function delete(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['delete']);
        $model = $this->getModel();
        return $this->returnData($model->delete_by_id($data['id']));
    }
    //查询基金所投资过的所有的项目的列表
    public function found_detail(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['found_detail']);
        $model = $this->getModel();
        return $this->returnData($model->found_detail($data['id']));
    }
    //查询项目的投资回报率
    public function rate_of_return(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule()['rate_of_return']);
        $model = $this->getModel();
        return $this->returnData($model->rate_of_return($data['project_id'],$data['days']));
    }

    protected function getModel(){
        $model = new \App\FoundProject();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }
}
