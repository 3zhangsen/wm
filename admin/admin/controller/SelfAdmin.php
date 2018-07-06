<?php 
 namespace app\admin\controller;

 class SelfAdmin extends Basic
 {
  public $usr_info=array('id'=>'',
     'usr_id'=>'',
   'usr_name'=>'',
   'display_name'=>'',
);
private static $usrmap;
  public function _initialize() {
     parent::_initialize();
     self::$usrmap= model('admin/UsrAdmin');
     $this->usr_info=self::$usrmap->field('id,usr_name')->where('id','eq',$_SESSION[$this->UsrId])->find();
     $this->assign('usr_info',$this->usr_info);
  }

  // public function index()
  // {
     
  //    $usrall=self::$usrmap->where('id','eq',$_SESSION[$this->UsrId])->find();
     
  //    $this->assign('usrall',$usrall);
    
  //    //print_r($usrall);exit;
  //    return $this->fetch($this->controller_name.'/'.$this->action_name);
  // }
 public function cpassword()
  {
    
     return $this->fetch($this->controller_name.'/'.$this->action_name);
  }
private function setAuthorization($aa){  
          $authorization = md5(substr(md5($aa), 8, 24).$aa);  
          return $authorization;  
        } 

 public function SaveInfo()
  {
   $naction =trim(input('post.naction'));
   //$usr_id=input('post.usr_id');
   
   $password=trim(input('post.password'));
   
   
   $user = model('admin/UsrMap');
   $usrinfo=$user->where('id','eq',$_SESSION[$this->ImgUsrId])->where('password','eq',md5($password))->find();

   if($usrinfo==false)
   {
    $this->error('密码错误','SelfAdmin/'.$naction);
   }
   else{
    switch ($naction) {
        case 'cip':
        $usr_ip=trim(input('post.usr_ip'));
        $rip=str_replace(" ","",$usr_ip);
        $mip=explode('/',$rip);
        $lip=serialize($mip);
        $up_usr=$user->where('id',$_SESSION[$this->ImgUsrId])->update([
         'ip'=>$lip
        ]);
            break;
        case 'cpassword':
        $npassword=trim(input('post.npassword'));
          $up_usr=$user->where('id',$_SESSION[$this->ImgUsrId])->update([
         'password'=>md5($npassword)
        ]);
          break;
          case 'ctoken':
          $token=trim(input('post.token'));
          $mtoken=$this->setAuthorization($token);
           $up_usr=$user->where('id',$_SESSION[$this->ImgUsrId])->update([
         'token'=>$mtoken,
           ]);
           break;
        default:
            $this->error('申请修改内容不被允许','Index/index');
            break;
    }
   }
       if($up_usr==false)
       {
        $this->error('数据没有更新','SelfAdmin/'.$naction);
       }
       else
       {
        $this->success('数据更新成功','Index/index');
        
       }
     

  }
}