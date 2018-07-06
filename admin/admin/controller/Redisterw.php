<?php
 namespace app\admin\controller;
 use think\Controller; 
 class Redisterw extends Controller
 {
   public $UsrId='UsrId';
  public function _initialize()
  {
     parent::_initialize();
  }

  ///////////////////////////////////////////////////////////////显示登录页面
  public function index()
  {
   ob_clean();
   if(isset($_SESSION[$this->UsrId])){
            $this->success("账号已经登录","index/index");
        }   
        $this->assign('action','AddUsr');
    return $this->fetch('Redisterw/index');

  }
    public function theAdminClear(){
    $fileArr = array(
      RUNTIME_PATH,
      // "__DIR__.'/../runtime/Data/_fields'",
      // "__DIR__.'/../runtime/temp'",
      // "__DIR__.'/../runtime/logs'",
      // "__DIR__.'/../runtime/~runtime.php'",
      // "__DIR__.'/../runtime/~app.php'"
    );
    foreach($fileArr as $val){
      //print_r($val);exit;
      $this->removeDir($val);
    }
    
    $this->success("清除成功");
  }
  function removeDir($dirName){ 
      if(!is_dir($dirName)) return @unlink($dirName); 
      $handle = @opendir($dirName); 
      while(($file = @readdir($handle)) !== false){ 
          if($file != '.' && $file != '..'){ 
              $dir = $dirName . '/' . $file;
        is_dir($dir) ? $this->removeDir($dir) : @unlink($dir);
          } 
      } 
      closedir($handle); 
    return true;
  }
   public function SaveUsr()
  {
   
   $data=input('post.');   
   print_r($data);
   $usrmap= model('admin/UsrAdmin');
   $result=$usrmap->checkpost($data);
   if(!empty($result))
   {
     $this->error($result);
   }
   else{
         $usrmap->data([
             'usr_name'=>$data['usr_name'],
             'display_name'=>$data['display_name'],
             'tel'=>$data['usr_tel'],
             'password'=>md5($data['password']),
            
             'usr_status'=>1,
             'usr_group'=>3,
             'register_time'=>date('Y-m-d H:i:s')

            ]);
            $usrmap->save();
              if(!empty($user->id))
            {
             
               $this->error('创建用户失败');
            }
            else
            {
               $this->success('创建用户成功','UsrAdmin/index');
            }
       }
      } 
  }
