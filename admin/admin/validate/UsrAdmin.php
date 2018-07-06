<?php 
 namespace app\admin\validate;
 use think\Validate;
 class UsrAdmin extends Validate
 {
  protected $rule=[
    'usr_id'=>'require',
    'usr_name|登录名'=>'require|max:25|min:3',
    'display_name'=>'require|max:25|min:2',
    'usr_tel'=>'require|number|length:11',
    'password'=>'require',
     'login_cap|验证码'=>'require|captcha',

      ];
      protected $message=[
        'usr_name.require'=>'帐号不能为空',
        'passwaord.require'=>'密码不能为空',
        'usr_tel.require'=>'手机不能为空',
         ];
      protected $scene=[
       'add'=>['usr_name'=>'require|max:25|min:4|unique:usr_map','display_name','usr_tel','password','login_cap'],
       'edit'=>['usr_id','usr_name','display_name','usr_tel','login_cap'],
       'login'=>['cName'=>'require','password'=>'require','login_cap']
        ];
 }