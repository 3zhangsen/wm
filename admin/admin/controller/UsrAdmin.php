<?php 
 namespace app\admin\controller;

 class UsrAdmin extends Basic
 {
  public $usr_info=array('id'=>'',
                       'usr_id'=>'',
                     'usr_name'=>'',
                     'display_name'=>'',
                     'tel'=>'',
                     'password'=>'',
                     'usr_status'=>'',
                     'usr_group'=>'',
                  );
  private static $usrmap;

  public function _initialize() {
     parent::_initialize();
     self::$usrmap= model('admin/UsrAdmin');
  }

  public function index()
  {
     //self::$usrmap=model('UsrAdmin');
     $usrall=self::$usrmap->select();
     
     $this->assign('usrall',$usrall);
     //$this->assign('webName',$webName);
     //print_r($usrall);exit;
     return $this->fetch('UsrAdmin/index');
  }

  public function AddUsr()
  {
    ob_clean();
    $this->assign('usr_info',$this->usr_info);
    $this->getgroup();
   return $this->fetch('UsrAdmin/EditUsr');
  }


  public function DelUsr()
  {
   $usr_id=input('id');
   if(empty($usr_id))
   {
    $this->error("请选择要删除的用户",'UsrAdmin/index');
   }
   //$delusr=db('usr_map')->where('id','eq',$usr_id)->delete();
   $delusr=self::$usrmap->where('id','eq',$usr_id)->delete();
   if($delusr==false)
   {
    $this->error('删除错误','UsrAdmin/index');
   }
   $this->success('删除成功','UsrAdmin/index');
  }

  public function EditUsr()
  {
    ob_clean();
   $usr_id=input('id');
   //print_r($usr_id);exit;
   if(empty($usr_id))
   {
    $this->error("请选择需要编辑的内容");
   }
   $usr_info=self::$usrmap->where('id','eq',$usr_id)->find();
   $this->assign('usr_info',$usr_info);
   $this->getgroup();
   return $this->fetch($this->controller_name.'/'.$this->action_name);
  }



  public function seachusr()
  {
  
   $usr_for=input('post.seachfor');
   if($usr_for==0)
   {
    $seachfo='id';
   }
   else{$seachfo='display_name';};
   $usr_wt=trim(input('post.seachwt'));
   $usrall=self::$usrmap->where($seachfo,'eq',$usr_wt)->select();
   $this->assign('usrall',$usrall);
   return $this->fetch('UsrAdmin/index');
  }



  public function SaveUsr()
  {
   
   $data=input('post.');   
   print_r($data);exit;
   $result=self::$usrmap->checkpost($data);
   if(!empty($result))
   {
     $this->error($result);
   }
   else{
       if($data['action']=='AddUsr')
       {
         self::$usrmap->data([
             'usr_name'=>$data['usr_name'],
             'display_name'=>$data['display_name'],
             'tel'=>$data['usr_tel'],
             'password'=>md5($data['password']),
            
             'usr_status'=>1,
             'usr_group'=>3,
             'register_time'=>date('Y-m-d H:i:s')

            ]);
            self::$usrmap->save();
              if(!empty($user->id))
            {
             
               $this->error('创建用户失败');
            }
            else
            {
               $this->success('创建用户成功','UsrAdmin/index');
            }
       }
       elseif($data['action']=='EditUsr')
        { 
           $up_usr=self::$usrmap->where('id',$data['usr_id'])->update([
             'usr_status'=>$data['usr_status'],
             'usr_group'=>$data['group'],
            ]);
           if($up_usr==false)
           {
            $this->error('数据没有更新','UsrAdmin/index');
           }
           else
           {
            $this->success('数据更新成功','UsrAdmin/index');
            
           }
          }
          else{
            $this->error('操作错误','UsrAdmin/index');
          }
    }
  

  }
  public function getGroup()
    {
       $group=model('admin/Group')->select();
       $this->assign('group',$group);
    }
   private function setAuthorization($aa){  
          $authorization = md5(substr(md5($aa), 8, 24).$aa);  
          return $authorization;  
        } 
}