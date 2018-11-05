<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class DiscussionComment extends Token
{
    //
    protected function rule(){
        return [
            'id' => 'integer',
            'discussion_id' => 'required|integer',
            'comment' => 'required|string|max:255',
        ];
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
        $this->validate_all_input($data,$this->rule());
        $model = $this->getModel();
        return $this->returnData( $model->add($data) );
    }
    protected function getModel(){
        $model = new \App\DiscussionComment();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }
}
