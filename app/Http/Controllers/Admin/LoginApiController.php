<?php

namespace App\Http\Controllers\Admin;
use App\Exceptions\ApiExceptions;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\UserTokenModel;
use App\Model\UserModel;//引入用户表
use App\Model\TokenModel;//引入Token

//引入登录表
use App\Model\UserModel;
//引入Token
use App\Model\TokenModel;
use App\Model\AppIdModel;
class LoginApiController extends CommonController
{
    //阅读前台注册接口
    public function reg(){
        $user_name = $this->checkApiParam("user_name");
        $user_pwd = $this->checkApiParam("user_pwd");
        $code = $this->checkApiParam("code");
        $type = $this->checkApiParam("type");
        $tt = $this->checkApiParam('tt');
        $match ="/^1[3|5|6|7|8|9]d{9}$/";
        if(preg_match($match,$user_name)){
           return  $this->success("1",20001,"手机格式不对，请输入正确的格式,13,15,17");
        }
        if($this->checkUserExists($user_name) > 1)
        {
            return  $this->success("1",20002,"用户名已存在，请重新添加用户");
        }
        $where = [
            ["user_name", '=',$user_name],
            [ "status" ,  '<'  , '4' ] ,
            [ "del", '=', 0],
            [ "tt", '=', $tt],
        ];
       $MsgUser = UserModel::where($where)->orderby("user_id","desc")->first();
//       dd(UserModel::get());
       if(!$MsgUser){
           return  $this->success("1",20004,'请先手机获取验证码！');
       }
        if($this->checkUser($user_name) > 1 ){
            return  $this->success("1",20003,"用户名已存在，请重新添加用户");
        }
       if($MsgUser->code != $code){
           return  $this->success("1",20005,"验证码不对，请填写正确的验证码！");
       }
        if($MsgUser->reg_time < time()){
            return  $this->success("1",20006,"验证码已过期，请重新添加验证码！");
        }
        $rand_code = rand(1000,9999);
        $rand_deas = rand(10,99);
        $rand_desc = rand(10000,99999);
        $time = time();
        $password = encrypt($user_pwd.$rand_code);
        $where_data = [
            "user_name"=>$user_name,
            "user_pwd"=>$password,
            "rand_code"=>$rand_code,
            "tt"=>$tt,
            "code"=>$code,
            "reg_time"=> $time+7200,
            "status"=>1,
            "del"=>1,
            "type"=>1,
        ];
        $user = UserModel::where("user_name",$user_name)->update($where_data);
        //AppID
        $appid = 'yd'.substr(md5($user_name.$time),4,10).substr(md5($user_name.$time),23,6);
        $appsecret = $rand_deas.substr(MD5($time),20).$rand_code.substr(MD5($user_name),15).$rand_desc;
        $Token = new AppIdModel;
        $Token ->appid = $appid;
        $Token ->appsecret = $appsecret;
        $Token ->user_id = $MsgUser->user_id;
        $Token ->save();
        $where_app = [
            "user_id"=>$MsgUser->user_id,
            "appid"=>$appid,
            "appsecret"=>$appsecret,
        ];
        if($user){
            return  $this->success($where_app,0,"注册成功，请登录");
        }else{
            return  $this->success("1",20007,"注册失败，请重新注册！");
        }

    }


    /**
     * 阿里云发送短信
     * */
    public function sendTel($tel,$code){
//        return ['Code'=>'OK','Message'=>'OK'];
        AlibabaCloud::accessKeyClient('LTAI4FjNztFFLNp3JabDQAvf', 'V5gmjxVHWsu4bx9iKTYhGqgswFuS7f')
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' =>$tel,
                        'SignName' => "是不是应该放弃",
                        'TemplateCode' => "SMS_178755775",
                        'TemplateParam' => "{code:$code}",
                    ],
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage();
        } catch (ServerException $e) {
            return $e->getErrorMessage();
        }
    }

    public function text(){
        $code = rand(1000,9999);
        $reg_time = time()+7200;
        $UserModel  = new  UserModel;
        $UserModel ->user_name = "可见度发挥";
        $UserModel ->code = $code;
        $UserModel ->reg_time = $reg_time;
        $UserModel ->tt = 1;
        $user =  $UserModel ->save();
    }

//     登录

    public function login(){
        $user_tel=$this->checkApiParam('user_tel');
        $password=$this->checkApiParam('password');
        $tt=$this->checkApiParam('tt');
        $where=[
            ['user_name','=',$user_tel],
            ['status','<',4],
        ];
        $user_obj=UserModel::where($where)->first();
        if(empty($user_obj)){
            throw new ApiExceptions('没有此用户或账号正在审核中');
        }
        $this->checkUserStatus($user_obj);
        $password=$password.$user_obj->rand_code;
        //$user_pwds=decrypt($user_obj->user_pwd.$user_obj->rand_code);

        $error_count_key='error_count_key:'.$user_obj->user_id;
        $error_last_time_key='error_last_time:'.$user_obj->user_id;
        $error_count=Redis::get($error_count_key);
        $error_last_time=Redis::get($error_last_time_key);
        if($password==decrypt($user_obj->user_pwd.$user_obj->rand_code)){
        if(md5($password)==$user_obj->user_pwd){
            if($error_count>=3&&time()-$error_last_time<3600){
                $min=60-ceil((time()-$error_last_time)/60);
                throw new ApiExceptions('账号已被锁定,请于'.$min.'分钟后重新登录');
            }else{
                Redis::del($error_count_key);
                Redis::del($error_last_time_key);
                $token=$this->_createUserToken($user_obj->user_id,$tt);
                $user_arr=collect($user_obj)->toArray();
                $user_arr['token']=$token;
                $user_key='user_Info'.$user_obj->user_id;
                Redis::hMset($user_key,$user_arr);
                Redis::expire($user_key,7200);
                return $this->success($user_arr);
            }
        }else{
            if($error_count>=3){
                if((time()-$error_last_time)>3600){
                    Redis::del($error_count_key);
                    Redis::del($error_last_time_key);
                    Redis::set($error_count_key,1);
                    Redis::set($error_last_time_key,time());
                    throw new ApiExceptions('您还有两次机会');
                }else{
                    $min=60-ceil((time()-$error_last_time)/60);
                    throw new ApiExceptions('账号锁定中,请于'.$min.'分钟后重新登录');
                }
            }else{
                Redis::incr($error_count_key);
                Redis::set($error_last_time_key,time());
                throw new ApiExceptions('账号与密码不匹配,错误三次将被锁定');
            }
        }
    }

//     用户令牌
   private function _createUserToken($user_id,$tt){
        $token=md5(uniqid());
        $now=time();
        $user_token_model=new UserTokenModel();
        $where=[
            ['user_id',$user_id],
            ['tt',$tt],
            ['expire','>',time()]
        ];
        //var_dump($where);die;
        //如果没有过期的话
        $user_token_obj=$user_token_model->where($where)->first();
        //过期了直接在数据库写
        if(empty($user_token_obj)){
            $user_token_model->user_id=$user_id;
            $user_token_model->token=$token;
            $user_token_model->tt=$tt;
            $user_token_model->expire=$now+$this->expire;
            $user_token_model->status=1;
            $user_token_model->ctime=$now;
            $user_token_result=$user_token_model->save();
        }else{
            //在原来的基础上增加过期时间
            $user_token_obj->expire=$now+$this->expire;
            $user_token_result=$user_token_obj->save();
        }
        if($user_token_result){
            return $token;
        }else{
            throw new ApiExceptions('令牌生成失败,请重试');
        }
    }


}
