<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Relationship extends Token
{
    protected function rule(){
        return [
            'name' => 'required|string',
            'phone' => 'phone',
//            'linkman' => 'string',
//            'link_phone' => 'string',
//            'link_wechat' => 'string',
//            'wechat' => 'string',
            'email' => 'email',
            'company' => 'required|string',
            'position' => 'required|string',
            'title' => 'required|string',
            'industry_id' => 'required|integer',
            'category_id' => 'required|integer',
            'category_name' => 'required|string',
//            'note' => 'string',
        ];
    }
    //分组查询
    public function group(Request $request){
        $category_id = $request->get('category_id');
        $perPage = $request->get('perPage',0);
        $model = $this->getModel();
        if(is_numeric($perPage) && $perPage>0){
            $model->perPage = $perPage;
        }
        if($category_id){
            if(!is_numeric($category_id)){
                return $this->returnFail('category_id必须为整数');
            }
            $data = $model->get_by_category_id($category_id);
        }else{
            $data = $model->get_all();
        }
        return $this->returnData($data);

    }
    public function detail(Request $request){
        $id = $request->get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $model = $this->getModel();
        $data = $model->get_detail($id);
        return $this->returnData($data);
    }
    public function add(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule());
        $model = $this->getModel();
        $model->add($data);
        return $this->returnSuccess();
    }
    public function update(Request $request){
        $id = $request->post('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $data = $request->all();
        $model = $this->getModel();
        $model->update_by_id($data,$id);
        return $this->returnSuccess();

    }
    public function delete(Request $request){
        $id = $request->get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $model = $this->getModel();
        $model->delete_by_id($id);
        return $this->returnSuccess();
    }
    public function search(Request $request){
        $key_word = $request->get('keyword');
        if(!$key_word){
            return $this->returnFail('关键字不能为空');
        }
        $model = $this->getModel();
        return $this->returnData($model->search($key_word));
    }

    protected function getModel(){
        $model = new \App\Relationship();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }
}
