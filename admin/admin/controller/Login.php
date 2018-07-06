<?php
 namespace app\admin\controller;

 class Login extends Basic
 {
  
  public function _initialize()
  {
     parent::_initialize();
  }

  ///////////////////////////////////////////////////////////////显示登录页面
  public function login()
  {
   ob_clean();
   if(isset($_SESSION[$this->UsrId])){
            $this->success("账号已经登录","index/index");
        }   
        
    return $this->fetch($this->controller_name.'/'.$this->action_name);
  }
  //核对登录信息

////////////////////////////////////////////////////////////////////////
  public function checkLogin()
  {

   if(isset($_SESSION[$this->UsrId]))
   {
        $this->redirect("index/index");
        }

    $data=['cName'=>input('post.cName'),
    'password'=>input('post.cPass'),
    'login_cap'=>input('post.login_cap'),];
    
     $usrlogin=model('admin/UsrAdmin');
     $resultlogin=$usrlogin->checklogin($data);

    switch ($resultlogin[0])
    {
     case 1:
     $this->error("密码错误!");
      break;
     case 2:
     $this->error("用户不存在!");
     break;
     case 3:
     $this->error("账号禁用!");
     break;
     case 4:
     $this->error("账号审核中!");
     break; 
     case 5:
     $this->error("帐号状态不可用!");
     break; 
     case 6:
     $dDateTime = date('Y-m-d H:i:s');
      $usrid=$resultlogin[1]['id'];
      Db('usr_map')->where('id','eq',$usrid)->update(['last_load' =>$dDateTime]);
      $_SESSION[$this->UsrId] = $usrid;
      $_SESSION[$this->UsrName] = $resultlogin[1]['usr_name'];
      $_SESSION[$this->displayName] = $resultlogin[1]['display_name'];
      $_SESSION[$this->UsrGroup] = $resultlogin[1]['usr_group'];
      $_SESSION[$this->UsrStatus] = $resultlogin[1]['usr_status'];
      if( $resultlogin[1]['usr_group']!=3)
      $this->success('登陆成功！','admin/Index/index');
    else
      $this->success('登陆成功！','index/Yaocai/index');

     break;
     default:
     $this->error($resultlogin[1]);
      break;
    }
  }
///////////////////////////////////////////////////////////////////////////////
  public function logout()
  {
     if(isset($_SESSION[$this->UsrId])){
            unset($_SESSION);
            session_destroy();
            $this->success("成功退出!","index/index");
        }else{
            $this->error("已经成功登陆!","index/index");
        }
  }
  public function index()
  {
    return $this->fetch("index/index");
  }

 }
