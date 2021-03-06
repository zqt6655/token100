<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FoundProject extends BaseModel
{
    //
    public $table='found_project';
    public $timestamps = false;
    public $perPage = 10;
    public function get_by_found_id($found_id){
        $data = DB::table("$this->table as fp")
            ->leftJoin('found as f','fp.found_id','=','f.id')
            ->where('fp.is_delete','=',0)
            ->where('fp.found_id','=',$found_id)
            ->select('pf.found_id','pf.project_id','pf.op_type','pf.price','pf.num','pf.user_id','f.name','f.account','f.unit')
            ->get()
            ->toArray();
    }

    //获取项目详情
    public function get_by_project_id($project_id){
        $data = DB::table("$this->table as fp")
            ->leftJoin('found as f','fp.found_id','=','f.id')
            ->where('fp.project_id','=',$project_id)
            ->where('fp.is_delete','=',0)
//            ->select('pf.found_id','pf.project_id','pf.op_type','pf.total_price','pf.num','pf.user_id','pf.status','f.name','pf.status','pf.status')
            ->select('fp.*','f.name','f.unit')
            ->orderBy('fp.pay_coin_time','desc')
//            ->paginate(10);
            ->get()
            ->toArray();
        if($data){
            $new_data = [];
            $buy=[];
            $back=[];
            $sell=[];
            $should_back_num=0;
            $back_num=0;
            foreach ($data as $one){
                unset($one->is_delete);
                unset($one->add_time);

                $one->pay_coin_time = substr($one->pay_coin_time,0,-3);
                //基金买入
                if($one->op_type==0){
                    if($one->status==1){
                        $buy[] = $one;
                        $should_back_num +=$one->num;
                    } else{
                        $back[] = $one;
                        $back_num +=$one->num;
                    }
                }elseif ($one->op_type==1){
                    $sell[] = $one;
                }
                unset($one->op_type);
                unset($one->status);
            }
            $new_data['buy'] = $buy;
            $new_data['back'] = $back;
            $new_data['sell'] = $sell;
            if($should_back_num==0)
                $new_data['rate'] = '0%';
            else{
                $rate = $back_num/$should_back_num;
                $new_data['rate'] = (sprintf("%.4f",$rate)*100).'%';
            }

            return $new_data;
        }
        return [];
    }
    //添加回币时，应回、已回的信息
    public function get_back_info($project_id){
        $data = DB::table("$this->table as fp")
            ->where('fp.project_id','=',$project_id)
            ->where('fp.is_delete','=',0)
            ->where('fp.op_type','=',0)
            ->select('fp.num','fp.status')
            ->get()
            ->toArray();
        if(!$data)
            return [];
        $should_back = 0;
        $back = 0;
        foreach ($data as $one){
            if($one->status==0)
                $back +=$one->num;
            else
                $should_back +=$one->num;
        }
        $rest = $should_back - $back;
        $new_data['should_back'] = $should_back;
        $new_data['back'] = $back;
        $new_data['rest'] = $rest;
        return $new_data;
    }
    //添加回币记录
    public function add_back($data){
        $data = $this->check_field($data);
        $data = $this->add_date_to_data($data);
        $data['user_id'] = $this->user_id;
        $data['pay_coin_time'] = $data['pay_coin_time'].':00';
        //回币，可用
        $data['status'] = 0;
        $data['op_type'] = 0;
//        dd($data);
        return(DB::table($this->table)->insertGetId($data));
    }
    //更新回币记录
    public function update_back($data,$id){
        $data = $this->check_field($data);
        $data['pay_coin_time'] = $data['pay_coin_time'].':00';
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }

    //购买之前，获取当前所有项目可用资金
    public function get_buy_info(){
        $data = DB::table("found")
            ->where('is_delete','=',0)
            ->select('id','name','account','unit')
            ->get()
            ->toArray();
        if(!$data)
            return [];
        $new_data = [];
        foreach ($data as $one) {
//            $sum = DB::table("$this->table")
//                ->where('is_delete', '=', 0)
//                ->where('op_type', '=', 0)
//                ->where('status', '=', 1)
//                ->where('found_id', '=', $one->id)
//                ->sum('total_price');

            $found_data = DB::table("$this->table")
                ->where('found_id', '=', $one->id)
                ->where('is_delete', '=', 0)
                ->select('op_type','total_price')
                ->get()
                ->toArray();
            $buy_num = 0;
            $back_num = 0;
            foreach ($found_data as $two){
                //如果type为0，说明是买，如果为1，说明是卖出返回
                if($two->op_type==0)
                    $buy_num +=$two->total_price;
                else
                    $back_num +=$two->total_price;
            }

            $one->buy_num = $buy_num;
            //剩余可投等于 总账户-已经买的 + 卖出返回的
            $one->rest_num = $one->account - $buy_num + $back_num;
            $new_data[] = $one;
        }
        return $new_data;

    }
    public function add_buy($data){
        $data = $this->check_field($data);
        $data = $this->add_date_to_data($data);
        $data['user_id'] = $this->user_id;
        //买入，不可用
        $data['status'] = 1;
        $data['op_type'] = 0;
        $data['pay_coin_time'] = $data['pay_coin_time'].':00';
        return(DB::table($this->table)->insertGetId($data));
    }
    //更新回币记录
    public function update_buy($data,$id){
        $data = $this->check_field($data);
        $data['pay_coin_time'] = $data['pay_coin_time'].':00';
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }
    //项目投资回报率
    /*
     * 传入对应的项目id，以及几天的回报率
     * **/
    public function rate_of_return($project_id,$days){
        $data = DB::table("$this->table as fp")
            ->leftJoin('project as p','fp.project_id','=','p.id')
            ->where('fp.project_id','=',$project_id)
            ->where('fp.is_delete','=',0)
            ->select('fp.op_type','fp.total_price','fp.num','fp.status','fp.pay_coin_time','fp.current_price','p.token_symbol')
//            ->orderBy('pay_coin_time','asc')
            ->get()
            ->toArray();

        //计算出对应天数的日期
        $dates = [];
        for($i=$days-1;$i>=0;$i--){
            $one_date = date('Y-m-d',strtotime("-$i days"));
            $dates[] = $one_date;
        }
        //如果没有记录，说明这个项目没有投资记录，收益率为0
        if(!$data){
            $return_data = [];
            foreach ($dates as $one){
                $return_data[] = ['date'=>$one,'rate'=>'0'];
            }
            return $return_data;
        }

        $rest_num = 0; //剩余多少数字币
        $capital = 0;  //本金等值多少USDT
        $sell_capital = 0; //已经卖出的币等值多少USDT
        $new_data = [];
        $token_symbol = '';
        $first_day = false;
        $is_sell = false; //是否有卖出记录标识
        foreach ($dates as $date){
            if(!$first_day)
                $first_day = $date;
            foreach ($data as $key=>$one){
//                print_r($one);
                //如果打币时间小于当前日期
                if( $one->pay_coin_time<$date.' 23:59:59'){
                    if($one->op_type==0 && $one->status==1){
                        //如果是投资记录，剩余数字币增加
                        //投入的本金增加
                        $rest_num +=$one->num;
                        $capital +=$one->current_price;
                    }elseif($one->op_type==1){
                        //如果是卖出记录，剩余数字币减去卖出的数量
                        //记录一共卖出等值多少的USDT
                        $rest_num -=$one->num;
                        $sell_capital +=$one->current_price;
                    }
                    //将处理过的数据unset，提高效率
                    unset($data[$key]);
                }
                if($one->op_type==1){
                    //有卖出记录，说明数字币可以交易
                    $token_symbol = $one->token_symbol;
                    $is_sell=true;
                }

            }
            $new_data[$date] = ['rest_num'=>$rest_num,'sell_capital'=>$sell_capital];
        }
        $return_data = [];
        //如果该项目没有卖出过，默认该币不能交易，收益率为-100%.
        if(!$is_sell){
            foreach ($new_data as $key=>$val){
                $return_data[] = ['date'=>$key,'rate'=>'-100'];
            }
        }else{
            //拿着token_symbol去查询实时价格
            $price_list = DB::table('symbol_price')
                ->where('token_symbol','=',$token_symbol)
                ->where('date','>',$first_day)
                ->select('date','price')
                ->get()
                ->toArray();

            foreach ($new_data as $key=>$val){
                $price = 0;
                foreach ($price_list as $k=>$one){
                    //如果日期在表中能找到，则取当天的价格
                    //否则默认价格为0
                    if($one->date == $key){
                        $price = $one->price;
                        unset($price_list[$k]);
                    }
                }
                //收益率 = （剩余数字币的USDT价格 + 卖出的币的USDT的价格）除以 （投资本金对应的USDT的价格），保留小数点后4位
                $rate = round( (($val['rest_num']* $price + $val['sell_capital']) / $capital),4 )*100;
                $return_data[] = ['date'=>$key,'rate'=>$rate];
            }
        }
        return $return_data;


    }
    //基金投过的项目列表
    public function found_detail($id){
        $data = DB::table("$this->table as fp")
            ->leftJoin('project as p','fp.project_id','=','p.id')
            ->leftJoin('adminuser as ad','p.user_id','=','ad.id')
            ->where('fp.found_id','=',$id)
            ->where('fp.is_delete','=',0)
            ->where('p.is_delete','=',0)
            ->distinct('fp.project_id')
            ->select('p.logo','p.name','p.token_symbol','p.grade','p.show_name','ad.name as up_name','p.from','p.id','fp.project_id')
            ->get()
            ->toArray();
        //将该基金下的所有投资过的项目id放入到数组中
        $project_ids = [];
        foreach ($data as $key=>$one){
            if($one->from !=1){
                $one->up_name = $one->show_name;
            }
            $project_ids[] = $one->project_id;
            unset($one->from);
            unset($one->show_name);
        }
//        查询项目集合内的所有项目的购买记录
        $invest_data = DB::table("$this->table as fp")
            ->leftJoin('found as f','fp.found_id','=','f.id')
            ->whereIn('project_id',$project_ids)
            ->where('fp.is_delete','=',0)
            ->where('fp.status','=',1)
            ->select('fp.found_id','fp.total_price','fp.project_id','f.unit')
            ->get()
            ->toArray();
//        return $invest_data;
        foreach ($data as $one){
            $one->ETH = 0;
            $one->BTC = 0;
            $one->USDT = 0;
            foreach ($invest_data as $two){
                if($two->project_id == $one->project_id){
                    $uni = $two->unit;
                    $one->$uni += $two->total_price;
                }
            }
            $one->buy_info = '';
            if($one->ETH != 0)
                $one->buy_info .=$one->ETH.'ETH.';
            if($one->BTC != 0)
                $one->buy_info .=$one->BTC.'BTC.';
            if($one->USDT != 0)
                $one->buy_info .=$one->USDT.'USDT.';
            unset($one->ETH);
            unset($one->BTC);
            unset($one->USDT);
        }

        return $data;
    }
//购买之前，获取当前所有项目可用资金
    public function get_sell_info($project_id){
        $data = DB::table("found as f")
            ->leftJoin('found_project as fp','fp.found_id','=','f.id')
            ->where('f.is_delete','=',0)
            ->where('fp.project_id','=',$project_id)
            ->where('fp.is_delete','=',0)
            ->distinct('f.id')
            ->select('fp.found_id','f.name','f.account','f.unit')
            ->get()
            ->toArray();
        if(!$data){
            $new_data['data'] = [];
            $new_data['num_info'] = [];
            return $new_data;
        }
        //获取该项目可卖，已卖，剩余可卖的数量
        $num_info = DB::table($this->table)
            ->where('project_id','=',$project_id)
            ->where('is_delete','=',0)
            ->where('status','=',0)
            ->select('num','op_type')
            ->get()
            ->toArray();
        $total_num = 0;
        $sell_num = 0;
        foreach ($num_info as $one){
            if($one->op_type==0)
                $total_num +=$one->num;
            else
                $sell_num +=$one->num;
        }
        $rest_num = $total_num - $sell_num;
        $num['total_num'] = $total_num;
        $num['sell_num'] = $sell_num;
        $num['rest_num'] = $rest_num;
        $new_data['data'] = $data;
        $new_data['num_info'] = $num;
        return $new_data;

    }
    public function add_sell($data){
        //首先，取出info中的信息
        if($data['info']){
            $all_record = [];
            $one_record=[];
            $info = json_decode($data['info'],true);
            if(!$info)
                $this->returnApiError('info格式不正确');

            //判断info里的num总和和外部的总和是否一致
            $total = 0;
            $time = date('Y-m-d H:i:s');
            foreach ($info as $one){
                $total +=$one['num'];
                $one_record['found_id'] = $one['found_id'];
                $one_record['num'] = $one['num'];
                //基金获得的卖出的价格
                //单个基金的数量，除以卖出总数的数量，乘以卖出的金额，就是其中一个基金的金额
                $total_price = ($one['num']/$data['num'])*$data['total_price'];
                $one_record['total_price'] = sprintf('%.5f',$total_price);
                $one_record['user_id'] = $this->user_id;
                //卖出，可用
                $one_record['status'] = 0;
                $one_record['op_type'] = 1;
                $one_record['add_time'] = $time;
                $one_record['project_id'] = $data['project_id'];
                $one_record['pay_coin_time'] = $data['pay_coin_time'].':00';
                if(isset($data['pay_coin_address']))
                    $one_record['pay_coin_address'] = $data['pay_coin_address'];
                $all_record[] = $one_record;
            }
            if($total != $data['num'])
                $this->returnApiError('获取总额与多个基金总和不一致');
            return DB::table($this->table)->insert($all_record);
        }
        return $this->returnApiError('info字段不能为空');
    }
    //更新回币记录
    public function update_sell($data,$id){
        $data = $this->check_field($data);
        $data['pay_coin_time'] = $data['pay_coin_time'].':00';
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
    }
    public function delete_by_id($id){
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update(['is_delete'=>1]);
    }
    protected function check_field($data){
        $field = [ 'found_id', 'project_id','invest_stage','total_price','num','pay_coin_time', 'pay_coin_name','pay_coin_address','pay_coin_tx','discount_note','lock_note', 'confirm_name'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
