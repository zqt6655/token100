<?php

namespace App;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Adminuser extends BaseModel
{
    //
    public $table='adminuser';
    public $timestamps = false;

    public function add($data){
        $data = $this->check_field($data);
        //过滤不属于数据库中的字段，然后将密码加密
//        $data['password'] = Hash::make($data['password']);
        $time = date('Y-m-d H:i:s');
        $data['password'] = sha1(md5($data['password']));
        $data['add_time'] = $time;
        $data['last_login'] = $time;
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重新点击发送验证码，再试一次。');
        }
        return $id;
    }

    public function get_user_info(){
        return $this::where('id','=',$this->user_id)
            ->first()
            ->makeHidden(['password','permission','is_delete','add_time'])
            ->toArray();
    }
    public function checkPhoneIsAvailable($phone){
        $exist = $this::where('phone','=',$phone)
            ->select('id')
            ->first();
        if($exist){
            $this->returnApiError('手机号已注册，请直接登录');
        }
    }
    public function bind_emali($user_id,$email){
        $this->checkEmailIsAvailable($email);
        return $this::where('id','=',$user_id)
            ->update(['email'=>$email]);
    }
    public function checkEmailIsAvailable($email){
        $exist = $this::where('email','=',$email)
            ->select('id')
            ->first();
        if($exist){
            $this->returnApiError('该邮箱已经被绑定');
        }
    }
    public function login($data){
        if(strstr($data['user'],'@')){
            return $this->login_by_email($data['user'],$data['password']);
        }
        return $this->login_by_phone($data['user'],$data['password']);
    }
    protected function login_by_email($email,$password){
        $data = $this::where('email','=',$email)
            ->first();
        if(!$data){
            $this->returnApiError('邮箱未注册');
        }
        if( $this->ecry_password($password) !=$data->password){
            $this->returnApiError('密码错误');
        }
        $data->last_login = date('Y-m-d H:i:s');
        $data->save();
        return ($data->toArray());

    }
    protected function login_by_phone($phone,$password){

        $data = $this::where('phone','=',$phone)
            ->first();
        if(!$data){
            $this->returnApiError('手机号未注册');
        }
        if( $this->ecry_password($password) !=$data->password){
            $this->returnApiError('密码错误');
        }
        $data->last_login = date('Y-m-d H:i:s');
        $data->save();
        return ($data->toArray());
    }
    protected function ecry_password($password){
        return sha1(md5($password));
    }


    public function update_by_id($data){
        $data = $this->check_up_field($data);
        if(isset($data['user_id'])){
            $this->user_id = $data['user_id'];
        }
        return DB::table($this->table)
            ->where('id','=',$this->user_id)
            ->update($data);
    }
    public function update_password($data){
        if(isset($data['user_id'])){
            $this->user_id = $data['user_id'];
        }

        $orgin_data = $this::where('id','=',$this->user_id)
            ->first();
        if($orgin_data->password !=$this->ecry_password($data['old_password'])){
            $this->returnApiError('旧密码错误');
        }
        $orgin_data->password = $this->ecry_password($data['password']);
        if($orgin_data->save()){
            return [];
        }
        $this->returnApiError('系统繁忙，请重试一次');

    }
    protected function check_up_field($data){
        $field = ['email', 'name', 'avatar_url','user_id'];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
    protected function check_field($data){
        $field = ['email', 'phone', 'name',
            'password', 'avatar_url'
        ];
        foreach ($data as $key=>$val){
            if (!in_array($key, $field)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
