<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class Project extends Token
{
    //
    protected function rule(){
        return [
            'project_id' => 'integer',
            'industry_id' => 'required|integer',
            'name' => 'required|string|max:32',
//            'domain_from' => 'string|max:127',
            'token_symbol' => 'required|string|max:32',
        ];
    }
    //获取所有的项目
    public function get(){
        $model = $this->getModel();
        return $this->returnData($model->get());
    }
    //查询已经评级项目
    public function get_grade(){
        $model = $this->getModel();
        return $this->returnData($model->get_grade());
    }
    //搜索已经评级项目
    public function search_grade(){
        $keyword = Input::get('keyword');
        if(!$keyword){
            return $this->returnFail('keyword不能为空');
        }
        $model = $this->getModel();
        return $this->returnData($model->search_grade($keyword));
    }
    //搜索已经投资项目
    public function search_invest(){
        $keyword = Input::get('keyword');
        if(!$keyword){
            return $this->returnFail('keyword不能为空');
        }
        $model = $this->getModel();
        return $this->returnData($model->search_invest($keyword));
    }
    public function get_wait(){
        $model = $this->getModel();
        return $this->returnData($model->get_wait());
    }
    public function get_continue(){
        $model = $this->getModel();
        return $this->returnData($model->get_continue());
    }
    public function get_hatch(){
        $model = $this->getModel();
        return $this->returnData($model->get_hatch());
    }
    public function get_reject(){
        $model = $this->getModel();
        return $this->returnData($model->get_reject());
    }

    //筛选前端上传的项目
    public function get_front(){
        $model = $this->getModel();
        return $this->returnData($model->get_front());
    }
    //筛选后台上传的项目
    public function get_back(){
        $model = $this->getModel();
        return $this->returnData($model->get_back());
    }
    //筛选爬虫抓取的项目
    public function get_system(){
        $model = $this->getModel();
        return $this->returnData($model->get_system());
    }
    //查询即将或正在ico的项目
    public function get_ico(){
        $model = $this->getModel();
        return $this->returnData($model->get_ioc());
    }


    //项目池搜索
    public function search(){
        $keyword = Input::get('keyword');
        if(!$keyword){
            return $this->returnFail('keyword不能为空');
        }
        $model = $this->getModel();
        return $this->returnData($model->search($keyword));
    }
    //项目ico搜索
    public function search_ico(){
        $keyword = Input::get('keyword');
        if(!$keyword){
            return $this->returnFail('keyword不能为空');
        }
        $model = $this->getModel();
        return $this->returnData($model->search_ico($keyword));
    }
    //删除项目
    public function delete(){
        $id = Input::get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $model = $this->getModel();
        $model->delete_by_id($id);
        return $this->returnSuccess();
    }
    //查询已经转入投资的项目
    public function get_invest(){
        $model = $this->getModel();
        return $this->returnData($model->get_invest());
    }
    //撤出投资
    public function invest_off(){
        $id = Input::get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $model = $this->getModel();
        return $this->returnData( $model->invest_off($id) );
    }
    //转入投资
    public function invest_on(){
        $id = Input::get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $model = $this->getModel();
        return $this->returnData( $model->invest_on($id) );
    }
    public function add(Request $request){
        $data = $request->all();
        $this->validate_input($data);
        $model = $this->getModel();
        return $this->returnData( $model->add($data) );
    }
    public function update(Request $request){
        $data = $request->all();
        $this->validate_input($data);
        if(!isset($data['project_id'])){
            return $this->returnFail('id不能为空');
        }
        $model = $this->getModel();
        $model->update_by_id($data,$data['project_id']);
        return $this->returnSuccess();
    }

    protected function getModel(){
        $model = new \App\Project();
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
