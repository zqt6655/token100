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
        $data['password'] = sha1(md5($data['password']));
        $data = $this->add_date_to_data($data);
        $id = DB::table($this->table)->insertGetId($data);
        if($id<0){
            $this->returnApiError('系统繁忙，请重新发送验证码，再试一次。');
        }
        return $id;
    }
    public function checkPhoneIsAvailable($phone){
        $exist = $this::where('phone','=',$phone)
            ->select('id')
            ->first();
        if($exist){
            $this->returnApiError('手机号已注册，请直接登录');
        }
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
            ->where('password','=',Hash::make($password))
            ->select('id','name','permission')
            ->first();
        if(!$data){
            $this->returnApiError('邮箱或者密码错误');
        }
        return ($data->toArray());

    }
    protected function login_by_phone($phone,$password){

        $data = $this::where('phone','=',$phone)
            ->select('id','name','permission','password')
            ->first();
        if(!$data){
            $this->returnApiError('手机号未注册');
        }
        if( sha1(md5($password)) !=$data->password){
            $this->returnApiError('密码错误');
        }
        return ($data->toArray());
    }


    public function update_by_id($data,$id){
        $data = $this->check_field($data);
        //说明字段没有空值，插入数据库即可。
        return DB::table($this->table)
            ->where('id','=',$id)
            ->update($data);
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
