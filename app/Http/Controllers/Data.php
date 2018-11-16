<?php
/**
 * Created by PhpStorm.
 * User: collin
 * Date: 2018/11/8
 * Time: 14:15
 */

namespace App\Http\Controllers;


use App\ProjectGrap;
use Illuminate\Support\Facades\DB;

class Data extends Controller
{
    protected $time='';
    public function add(){
        $model = new ProjectGrap();
        $data = $model->add();
        return $this->returnData($data);
    }
    public function add_ratingToken(){
        $data =  DB::connection('mysql_local')->table("project_ratingtoken as pg")
            ->leftJoin('project_detail_ratingtoken as pd','pg.name','=','pd.project_name')
            ->where('is_on','=',0)
            ->get()
            ->toArray();
        $this->time = date('Y-m-d H:i:s');
        foreach ($data as $one){
            if(!$one->id)
                continue;
            $project_id = $this->insertIntoProjectReturnId($one);
            $this->insertToDetail($project_id,$one);
        }
        DB::connection('mysql_local')->table('project_ratingtoken')->update(['is_on'=>1]);
        return $this->returnSuccess();
    }
    protected function insertIntoProjectReturnId($one){
        $project['name'] = $one->name;
        $project['token_symbol'] = $one->token;
        $project['website'] = $one->website;
        $project['logo'] = $one->logo;
        $project['rate'] = $one->rate;
        $project['domain_from'] = $one->domain;
        $project['upload_time'] = $this->time;
        $project['from'] = 0;
        $project['is_market'] = $one->is_market;
        $project['show_name'] = '系统';
        $project['white_book'] = $one->white_paper;
        return DB::table('project')->insertGetId($project);
    }
    protected function insertToDetail($project_id,$one){
        $detail['project_id'] = $project_id;
        $detail['team_introduce'] = $one->team_introduce;
        $detail['project_introduce'] = $one->about;
        if($one->start_time =='0000-00-00'){
            $detail['start_time'] = '2018-01-01 11:11:11';
            $detail['end_time'] = '2018-01-02 11:11:11';
        }else{
            $detail['start_time'] = $one->start_time;
            $detail['end_time'] = $one->end_time;
        }
        $detail['platform'] = $one->platform;
        $detail['accept_coin'] = $one->accept_coin;
        $detail['sorf_cap'] = $one->sorf_cap;
        $detail['hard_cap'] = $one->hard_cap;
        $detail['circulate_num'] = $one->circulate_num;
        $detail['ratio'] = '';
        $detail['upload_time'] = $this->time;
        $detail['market_time'] = $one->market_time;
        $detail['coin_total'] = $one->total_num;
        $detail['market_num'] = $one->deal_num;
        DB::table('project_details')->insert($detail);
    }

}