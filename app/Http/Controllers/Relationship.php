<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Relationship extends Controller
{
    //
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
//        $validate = Validator::make($request->all(), [
//            'id' => 'required|integer|between:1,10',
//            'title' => 'required|string'
//        ]);
//        if($validate->fails())
//        {
//            $message = $validate->errors()->first();
//            return $this->returnFail($message);
//        }
        $data = $request->all();
        $model = $this->getModel();
        $result = $model->add($data);
        if($result==='必填字段中存在空值'){
            return $this->returnFail($result);
        }elseif ($result){
            return $this->returnSuccess();
        }else{
            return $this->returnFail('系统繁忙，请重试一次');
        }
    }
    public function update(Request $request){
        $id = $request->get('id');
        if(!is_numeric($id)){
            return $this->returnFail('id必须为整数');
        }
        $data = $request->all();
        $model = $this->getModel();
        $result = $model->update_by_id($data,$id);
        if($result==='必填字段中存在空值'){
            return $this->returnFail($result);
        }
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

    protected function getModel(){
        $model = new \App\Relationship();
        return $model;
    }
}
