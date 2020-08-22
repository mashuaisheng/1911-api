<?php

namespace App\Http\Controllers;
use App\Exceptions\ApiExceptions;
//引入登录表
use App\Model\UserModel;
/*
 *继承父类 方法
 */
class CommonController extends Controller
{
    /**
     * 检查参数不能为空
     * @param $key
     * @return array|null|string
     * @throws ApiExceptions
     */
    protected function checkApiParam($key){
        $request=request();
        if(empty($value=$request->post($key))){
            throw new ApiExceptions($key.'参数不能为空',100);
        }else{
            return $value;
        }
    }
    /**
     * 成功时返回的数据
     */
    protected function success($data=[],$status=200,$msg='success'){
        $add =[
            'status'=>$status,
            'msg'=>$msg,
            'data'=>$data
        ];
        return json_encode($add);
    }
    /**
     * 判断用户是否注册过
     */
    protected function checkUserExists($user_tel){
        $where=[
            ['phone','=',$user_tel],
            ['user_status','<',4],
            ['user_status','<',4],
            ['user_name','=',$user_name],
        ];
        return UserModel::where($where)->count();
    }
    /*
     * 判断用户是否注册过2
     */
    protected function checkUser($user_name){
        $where=[
            ['user_name','=',$user_name],
        ];
       $user =  UserModel::where($where)->first();
        if($user->del == 0){
            return "1";
        }else{
            return "2";
        }
//
    }
    /*
     * 检查参数不能为空
     * @param $key
     * @return array|null|string
     * @throws ApiException
     * */
    public function checkUserStatus($user_obj){
        if($user_obj->user_status==2){
            throw new ApiExceptions('该账号已被锁定,请联系管理员解锁');
        }
    }



































}
