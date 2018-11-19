<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ProjectDetail extends Token
{
    //
    protected function rule(){
        return [
            'project_id' => 'required|integer',
//            'project_contacts' => 'string',
//            'project_introduce' => 'string',
//            'problem' => 'string',
//            'framework' => 'string',
//            'strength' => 'string',
//            'tokenmodel' => 'string',
//            'project_strategy' => 'string|max:255',
//            'project_community' => 'string|max:255',
//            'investplan' => 'string|max:255',
//            'project_otherinfo' => 'string|max:255',
        ];
    }
    public function get(){
        $project_id = Input::get('project_id');
        if(!is_numeric($project_id)){
            return $this->returnFail('project_id必须为整数');
        }
        $model = $this->getModel();
        return $this->returnData($model->get($project_id));
    }
    public function get_price(){
        $symbol = strtoupper(Input::get('symbol'));
        if (!$symbol)
            return $this->returnFail('symbol 不能为空');
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?convert=CNY&symbol='.$symbol;
        $header = array('X-CMC_PRO_API_KEY:ec1b6600-6c2d-46cf-8859-9ba678b57a29');
        $price = $this->http_get($url,$header);
        if($price)
            return $this->returnData(json_decode($price,true)['data'][$symbol]['quote']['CNY']['price']);
        return $this->returnFail('暂无');
    }

    public function update(Request $request){
        $data = $request->all();
        $this->validate_input($data);
        if(!isset($data['project_id'])){
            return $this->returnFail('project_id不能为空');
        }

        $model = $this->getModel();
        $model->update_by_id($data,$data['project_id']);
        return $this->returnSuccess();
    }

    protected function getModel(){
        $model = new \App\ProjectDetail();
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
