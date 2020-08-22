<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/', function () {
    return view('welcome');
});

//api登录接口
Route::any("/user/login","Admin\LoginApiController@login");

Route::any("/user/cat","Admin\CatController@cat");


/*
 *阅读api接口公共文件路由
 */
Route::prefix("/user")->group(function(){
    //api注册接口
    Route::post("/Api/reg","Admin\LoginApiController@reg");
    //api图片验证接口
    Route::post("/ImageCodeUrl","Admin\ImagesController@ImageCodeUrl");
    //图形验证码
    Route::get("/showImageCode","Admin\ImagesController@showImageCode");
    //验证码注册
    Route::post("/Sendverificationcode","Admin\ImagesController@Sendverificationcode");

});

/*
 *阅读api接口公共文件路由
 */
Route::prefix("/user")->group(function(){
    //api注册接口
    Route::post("/Api/reg","Admin\LoginApiController@reg");
    //api图片验证接口
    Route::post("/ImageCodeUrl","Admin\ImagesController@ImageCodeUrl");
    //图形验证码
    Route::get("/showImageCode","Admin\ImagesController@showImageCode");
    Route::post("/Sendverificationcode","Admin\ImagesController@Sendverificationcode");
});


Route::get("/phpinfo",function(){
    phpinfo();
});

Route::get("/text",function(){
//    dump("jkdf ");
////    decrypt
//    dd(encrypt(""));
//    $user_pwd = "zhao";
//    $rand_code = rand(10000,99999);
//    dump($rand_code);
//    $password = encrypt($user_pwd.$rand_code);
//    dd($password);
//    dd(date("Y-m-d H:i:s",time()+7200));
//    $name = "17732727492";
//    $time = time();
////    $user = 'yd'.substr(md5($name.$time),4,10).substr(md5($name.$time),23,6);
//    $desc = rand(1000,9999);
//    $n = rand(10,99);
//    $asc = rand(10000,99999);
//    $user = $n.substr(MD5($time),20).$desc.substr(MD5($name),15).$asc;
//    dd($user);
    dd(time()+7200);
    //decrypt("");
});





//测试方法
Route::any("/user/desc",function(){
//    $data = [
//        "error"=>1,
//        "msg"=>"文件错误",
//        "data"=>"来划分的好地方了",
//    ];
//    return json_encode($data);
//    echo "eyJpdiI6IkdLalBSWVN6a0FNMjY5NFVzVTBPcXc9PSIsInZhbHVlIjoiSUl2ejVmUFJUcVJyWStJTEdTNWNQZz09IiwibWFjIjoiYTE0NmZiNDQ3ZDQzOWRlNjc3ZDRhMmI4MjYyNTljNDdkZGZlMTk2MTFhNDZhMGRiZTdjMjQyYjM0MDI1ODZkMiJ9";
//    "<br>";
    $a = "zhao";
    $b = "123";
//    $name = "eyJpdiI6IkdLalBSWVN6a0FNMjY5NFVzVTBPcXc9PSIsInZhbHVlIjoiSUl2ejVmUFJUcVJyWStJTEdTNWNQZz09IiwibWFjIjoiYTE0NmZiNDQ3ZDQzOWRlNjc3ZDRhMmI4MjYyNTljNDdkZGZlMTk2MTFhNDZhMGRiZTdjMjQyYjM0MDI1ODZkMiJ9";
    dd(MD5($a.$b));
//    dd(decrypt($name));
//    dd(MD5("zhao"));
});





























