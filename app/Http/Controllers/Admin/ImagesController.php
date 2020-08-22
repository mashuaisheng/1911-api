<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;

//引入登录表
use App\Model\UserModel;
class ImagesController extends CommonController
{
    /**
     * 因为每个图片都要标示出来，所需需要在API端生成一个唯一的标示【可以使用sessionid，因为sessionid是不会重复的】
     * pc需要获取到这个唯一标示，所以我们需要在写一个接口，去获取这个唯一标识【 获取图片验证码的地址的接口 】
     */
    public function ImageCodeUrl(){
        $request=request();
        //开启session
        $request->session()->start();
        $sid=$request->session()->getId();
        $arr['url']='http://1911td2.yangwenlong.top/user/showImageCode?sid='.$sid;
        $arr['sid']=$sid;
        return $this->success($arr);
    }


    //eyJpdiI6IkVrN09iYldyYjVZSERFNEpzSzdkT0E9PSIsInZhbHVlIjoiQWNWK05yZEgxNVVoSk0rSEFrZDFOa0NwWWtoUGxINDA5QmxxZ2dcL1YyTVk9IiwibWFjIjoiODU5YjkzNjIzYTM4MzRiNDgxMWQwYmQ3M2RmMmNhZDc2MWYyNzFmMDI3NzJiMzc5ZjlhNDhhNjUzYmYzN2Q0NCJ9
    public function showImageCode(){
//        $request = request();
//        $sid = $request ->get("sid");
//        if(empty($sid)){
//            return  $this->success('1',20008,"图片验证码输出失败");
//        }
//        $request->session()->setId($sid);
//        $request->session()->start();
//        return $this->success(rand(1000,9999));
        // Set the content-type
        header('Content-Type: image/png');
        // Create the image
        $im = imagecreatetruecolor(100, 30);
        // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 399, 29, $white);
//         The text to draw
        $text = ''.rand(1000,9999);
        // Replace path by your own font path
        $font = storage_path().'/Gabriola.ttf';
        // Add some shadow to the text
        $i = 0;
        //eyJpdiI6InZqU2pGYUtHdmxSVFVvYUtzbHZkVEE9PSIsInZhbHVlIjoiVTVWVTV2ODNLMmtUZkc4Q3BCK2hcLys4QnNQSUZoY0d2K0FaakxvNEhVa3BYeWM0UzRtc3IyYURlRzJPZ3BLZFciLCJtYWMiOiI2YTA1Mzg0MjRkMjRiNzFjMjVlZDYyMmQ4NjRlMzNmNWM3OWMyZGFhMmVhYWE2YWYwMDQwYjZmN2ZjODg4YjAyIn0=
        while($i < strlen($text)) {
            //eyJpdiI6ImFwVmg1UUhocUtraDloemdDSGY5VHc9PSIsInZhbHVlIjoiMVYyd2lQNjZJaFFWSUIybkIzeGVmWlVRdU1PVDY1cUlncG5aRDFKR0hsVnZiNHVDRHlpSWR4NnNPK3NXclpWUSIsIm1hYyI6IjBlNTAxNzc3ZDg4ZWU0YTQ0NTJhODA4ODQxMmI5YjEzNmM3MWM1MjNiODBjNjBiNzgzZTkyMDc0MGZiYjY5YzAifQ==
            imageline($im,rand(0,10),rand(0,25),rand(90,100),rand(10,25),$grey);
            imagettftext($im, 20, rand(-15,15), 11+20*$i, 21, $black, $font, $text[$i]);
            $i++;
        }
        // Using imagepng() results in clearer text compared with imagejpeg()
        imagepng($im);
        imagedestroy($im);
        exit;
    }

    //发送验证码
    public function Sendverificationcode()
    {
        $user_name = $this->checkApiParam("user_name");
        $tt = $this->checkApiParam("tt");
        $user = UserModel::where("user_name",$user_name)->first();
        if($user){
            return  $this->success(1,40000,"用户已存在");
        }
        $code = rand(10000,99999);
        $reg_time = time()+7200;
        $UserModel  = new  UserModel;
        $UserModel ->user_name = $user_name;
        $UserModel ->code = $code;
        $UserModel ->reg_time = $reg_time;
        $UserModel ->tt = $tt;
       $user =  $UserModel ->save();
       if($user){
           return  $this->success(1,0,"验证码已发送，60秒再试");
       }else{
           return  $this->success(1,40000,"请求失败");
       }
    }

}
