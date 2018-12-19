<?php

namespace App;


use Illuminate\Support\Facades\DB;

class Found extends BaseModel
{
    //
    public $table='found';
    public $timestamps = false;
    public $perPage = 10;
    public function get(){
        $data_found = DB::table("$this->table as f")
            ->where('f.is_delete','=',0)
            ->get()
            ->toArray();
        $data_fp = DB::table("found_project as fp")
            ->where('fp.is_delete','=',0)
            ->where('fp.op_type','=',0)//买入
            ->select('fp.found_id','fp.total_price','fp.project_id')
            ->get()
            ->toArray();
        foreach ($data_found as $one){
            $one->invest = 0;
            $one->project_num = 0;
            $one->project_set = [];
            foreach ($data_fp as $key=>$two){
                if($two->found_id == $one->id){
                    //将数据从数组中去除，减少循环次数
                    unset($data_fp[$key]);
                    //如果属于当前的基金id
                    //如果project_id 不在该基金存在的项目集合中
                    //则投资过的项目数量加1，将该项目id放入到集合中
                    if(!in_array($two->project_id,$one->project_set)){
                        $one->project_num++;
                        array_push($one->project_set,$two->project_id);
                    }
                    $one->invest += $two->total_price;
                }
            }
            $one->rest = $one->account - $one->invest;
            $rate = ($one->account/$one->plan_account)*100;
            $one->rate = round($rate,2).'%';
            unset($one->project_set);
            unset($one->add_time);
            unset($one->is_delete);
        }
        return $data_found;


    }
    public function add($data){
        $data = $this->check_field($data);
        $data = $this->add_date_to_data($data);
        $data['user_id'] = $this->user_id;
        //说明字段没有空值，插入数据库即可。
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重试一次。');
        }
        $returnData['id'] = $id;
        return $returnData;
    }

    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        return DB::table($this->table)->where('id','=',$id)
            ->update($data);
    }
    public function delete_by_id($id){
        //删除基金之前，先查询该基金是否有在投项目
        //如果有，则提示得先去删除投资记录
        //如果没有，则删除
        $result = DB::table('found_project')->where('found_id','=',$id)->where('is_delete','=',0)->get()->toArray();
        if($result)
            return $this->returnApiError('请先删除该基金下的所有投资记录');
        return DB::table($this->table)->where('id','=',$id)
            ->update(['is_delete'=>1]);
    }
    protected function check_field($data){
        $field = [ 'name', 'account','unit','plan_account','start_time','end_time'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
