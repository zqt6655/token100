<?php
/**
 * Created by PhpStorm.
 * User: collin
 * Date: 2018/11/8
 * Time: 14:17
 */

namespace App;


use Illuminate\Support\Facades\DB;

class ProjectGrap extends BaseModel
{
    //
    public $table='project_grap';
//    public $connection = 'mysql_local';
    public $timestamps = false;
    private $time='';
    public function add(){

        $data =  DB::connection('mysql_local')->table("$this->table as pg")
            ->leftJoin('project_detail_grap as pd','pg.name','=','pd.project_name')
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
        DB::connection('mysql_local')->table($this->table)->update(['is_on'=>1]);
        return [];
    }
    protected function insertIntoProjectReturnId($one){
        $project['name'] = $one->name;
        $project['token_symbol'] = $one->token;
        $project['website'] = $one->url;
        $project['logo'] = $one->logo;
        $project['country'] = $one->country;
        $project['domain_from'] = $one->url;
        $project['upload_time'] = $this->time;
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


        $detail['coin_total'] = $one->coin_total;
        $detail['platform'] = $one->platform;
        $detail['type'] = $one->type;
        $detail['accept_coin'] = $one->accept_coin;
        $detail['limit_zone'] = $one->limit_zone;
        $detail['sorf_cap'] = $one->sorf_cap;
        $detail['hard_cap'] = $one->hard_cap;
        $detail['is_kyc'] = $one->restrictions_KYC;
        $Token_info = json_decode($one->Token_info,true);
        $ratio = isset($Token_info['Price in ICO'])?:'';
        $detail['ratio'] = $ratio;
        $detail['upload_time'] = $this->time;
        DB::table('project_details')->insert($detail);
    }

}