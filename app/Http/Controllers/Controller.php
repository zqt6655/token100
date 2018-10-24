<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function returnData($data){
        $returnData['data'] = $data;
        $returnData['code'] = 0;
        $returnData['msg'] = 'success';
        return $returnData;
    }
    public function returnFail($msg='fail'){
        $returnData['data'] = [];
        $returnData['code'] = -1;
        $returnData['msg'] = $msg;
        return $returnData;
    }
    public function returnSuccess($msg='success'){
        $returnData['data'] = [];
        $returnData['code'] = 0;
        $returnData['msg'] = $msg;
        return $returnData;
    }
}
