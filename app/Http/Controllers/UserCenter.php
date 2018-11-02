<?php

namespace App\Http\Controllers;

use App\Adminuser;
use Illuminate\Http\Request;


class UserCenter extends Token
{
    protected function rule(){
        return [
            'name' => 'required|string',
            'email' => 'email',
            'avatar_url' => 'string',
        ];
    }
    protected function password_rule(){
        return [
            'old_password' => 'required|string|min:6',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
    //
    public function get(){
        $model = $this->getModel();
        return $this->returnData($model->get_user_info());
    }
    public function update_info(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->rule());
        $model = $this->getModel();
        return $this->returnData($model->update_by_id($data));
    }
    public function update_password(Request $request){
        $data = $request->all();
        $this->validate_all_input($data,$this->password_rule());
        $model = $this->getModel();
        return $this->returnData($model->update_password($data));
    }

    protected function getModel(){
        $model = new Adminuser();
        $model->user_id =17;
//        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }
}
