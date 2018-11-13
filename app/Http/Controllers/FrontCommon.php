<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InvestmentLayout;
use App\Article;
use Illuminate\Support\Facades\Input;

class FrontCommon extends Controller
{
    //获取投资布局所有内容
    public function inv_layout_get(){
        $model = $this->getInvLayoutModel();
        return $this->returnData($model->get());
    }
    //通过类目获取投资布局
    public function inv_layout_get_by_category(){
        $model = $this->getInvLayoutModel();
        return $this->returnData($model->get_by_category());
    }
    //通过首字母获取投资布局
    public function inv_layout_get_by_alp(){
        $model = $this->getInvLayoutModel();
        return $this->returnData($model->get_by_alp());
    }
    //获取投资布局model
    protected function getInvLayoutModel(){
       return new InvestmentLayout();
    }

    //获取已经发布的文章
    public function article_get_publish(){
        $model = $this->getArticleModel();
        return $this->returnData($model->get_publish_front());
    }
    //获取单篇文章的详情
    public function article_detail(){
        $id = Input::get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $model = $this->getArticleModel();
        return $this->returnData($model->detail($id));
    }
    //获取文章model
    protected function getArticleModel(){
        return new Article();
    }

    //获取项目成员
    public function get_members(){
        $model = new \App\Member();
        return $this->returnData($model->get());
    }
    //前台上传项目
    public function add_project(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->project_rule());
        $model = new \App\Project();
        return $this->returnData( $model->add_front($data) );
    }
    //project校验规则
    protected function project_rule(){
        return [
            'id' => 'integer',
            'name' => 'required|string|max:32',
            'token_symbol' => 'required|string|max:32',
        ];
    }
    public function update_project(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->project_detail_rule());
        if(!isset($data['id'])){
            return $this->returnFail('id不能为空');
        }

        $model = new \App\ProjectDetail();
        $model->update_front_by_id($data,$data['id']);
        return $this->returnSuccess();
    }
    //project_detail校验规则
    protected function project_detail_rule(){
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
}
