<?php 
 namespace app\admin\validate;
 use think\Validate;
 class CataAdmin extends Validate
 {
  protected $rule=[
    'code|栏目编号'=>'require',
    'cName|栏目名称'=>'require',
     'login_cap|验证码'=>'require|captcha',
      ];
  protected $message=[
    'cName.require'=>'栏目名称不能为空',
    'code.require'=>'密码不能为空'
     ];
  protected $scene=[
      'add'=>['code'=>'require|unique:usr_catalog,code','cName'=>'require|unique:usr_catalog,cName','login_cap'],
      'edit'=>['cata_id'=>'require','code'=>'require','cName'=>'require','login_cap'],
     ];
 }