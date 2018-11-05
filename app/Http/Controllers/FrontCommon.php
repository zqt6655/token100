<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        return $this->returnData($model->get_publish());
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

    public function get_members(){
        $model = new \App\Member();
        return $this->returnData($model->get());
    }
}
