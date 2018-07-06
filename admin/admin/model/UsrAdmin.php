<?php
 namespace app\admin\model;
 use think\Model;
 use think\Db;
 class UsrAdmin extends Model
 {
   protected $table="usr_map";
    public function checkpost($data)
   {
     $validate =  \think\Loader::validate('UsrAdmin');
     if($data['action']=='EditUsr')
     {
      $result = $validate->scene('edit')->check($data);
     }
     elseif($data['action']=='AddUsr')
     {
      $result = $validate->scene('add')->check($data);
     }
     else{
              }
     if(!$result)
     {
        //$this->error($validate->getError()); 
        return $validate->getError();
     }
   }
   public function checklogin($data){
    $loginsta='';
    $loginmas='';
    $validate =  \think\Loader::validate('UsrAdmin');
     $result = $validate->scene('login')->check($data);
     if(!$result)
     {
        //$this->error($validate->getError()); 
        $loginsta=0;
        $loginmas=$validate->getError();
        return array($loginsta,$loginmas);
     }
     $user=Db::name('usr_map')->where('usr_name','=',$data['cName'])->find();
     if($user)
     {
      if($user['password'] == md5($data['password']))
      {
         switch($user['usr_status'])
           {
            case 0:
             $loginsta= 3;
             break;   
            case 2:
             $loginsta= 4;
             break;
             case 1:
             $loginsta= 6;
             $loginmas=$user;
             break;
             default:
             $loginsta= 5;
             break;
           }
                
       }
       else
        { 
            $loginsta=1; //密码错误

        }
       }
       else {
        $loginsta=2; //用户不存在
       }
      return array($loginsta,$loginmas); 
   }


 }