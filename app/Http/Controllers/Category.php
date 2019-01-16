<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Category extends Controller
{
    //
    public function get(){
        $model = new \App\Category();
        $data = $model->get_list();
        $data2 = $data;
        $new_data=[];
        foreach ($data as $key=>$one){
            if($one['parent_id']==0){
                $one['sub']=[];
                foreach ($data2 as $k=>$v){
                    if($v['parent_id']==$one['id']){
                        $v['parent_name'] = $one['name'];
                        $one['sub'][] = $v;
                        unset($data2[$k]);
                    }
                }
                $new_data[] = $one;
            }
        }

        return $this->returnData($new_data);
    }
}
