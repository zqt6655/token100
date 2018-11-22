<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Found extends Token
{
    //
    protected function rule(){
        return [
            'project_id' => 'required|integer',
        ];
    }
    public function get(){
        $model = $this->getModel();
        return $this->returnData($model->get());
    }

    protected function getModel(){
        $model = new \App\Found();
        $model->user_id = $this->user_id;
        $model->permission = $this->permission;
        return $model;
    }

}
