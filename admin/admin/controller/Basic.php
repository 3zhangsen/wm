<?php 
   namespace app\admin\controller;
   use think\Controller;
   class Basic extends Controller
     {  
       public $UsrId='wmUsrId';
       public $UsrIp='wmUsrIp';
       public $UsrName='wmUsrName';
       public $displayName='displayName';
       public $UsrGroup='wmUsrGroup';
       public $UsrStatus='wmUsrStatus';
       public $controller_name='Index';
       public $action_name='index';
       public $module_name='index';
       public $webName="";
       function _initialize()
       {

        session_start();  
        $tplIndexHeader = 'Public:indexHeader';
        $this->assign('tplIndexHeader',$tplIndexHeader);
        //$tplIndexfooter = 'Public:Indexfooter';
        //$this->assign('tplIndexfooter',$tplIndexfooter);
         ///网站常量以及路径的配置
        $this->controller_name=$this->request->controller();
        $this->action_name=$this->request->action();
        $this->module_name=$this->request->module();

        $this->assign('controller_name',$this->controller_name);
        $this->assign('action_name',$this->action_name);
        $this->assign('module_name',$this->module_name);
 
        $this->assign('nowDate',date('Y-m-d'));
        $this->assign('nowDateTime',date('Y-m-d H:i:s'));
        $sTime = time();
        $this->assign('sTime',$sTime);

        $this->assign('webName','');///

        //判断有没有是否登录以及权限核实
         
        if($this->controller_name!='Login')          
         {    
                //如果没有登录就跳转到登录页面  
             if(empty($_SESSION[$this->UsrId]))
              {
             $this->redirect('admin/Login/login');
              }
              if($_SESSION[$this->UsrGroup]==3)
              {
                $this->error('you are  not  admin ',"index/Yaocai/index");
              }
             ///////用户登录后将用户信息渲染到view
             $this->assign('UsrName',$_SESSION[$this->UsrName]);
             $this->assign('displayName',$_SESSION[$this->displayName]);
             $this->UsrGroup = isset($_SESSION[$this->UsrGroup])?$_SESSION[$this->UsrGroup]:0;
             //print_r($this->UsrGroup);exit;
            $this->checkgroup();
            }
        
        // $this->assign('catalogArr2',array());
        // $this->assign('catalogArr1',array());
        }
     public  function checkgroup()
      {
          $group_able=db('usr_group');
         $usr_modle=$group_able->where('id','eq',$this->UsrGroup)->find();
         $theCatalogInArr = unserialize($usr_modle['catalog']);
         $theCatalogArr = $theCatalogInArr['code'];
      //print_r($theCatalogArr);exit;
         $catalogStr = implode(',',$theCatalogArr);
       //  //如果权限列表为空则没有后台登录权限
         if(empty($catalogStr))
         {
         $this->error('你目前没有后台管理权限，请联系管理员');//需要修改错误路径，建立一个错误页面
         }
         $tablewhere="code in ($catalogStr)";
          $tableOrder = "";
          $moduleArr=Model('admin/CataAdmin')->getCata($tablewhere,$tableOrder,$meth=2);
       
          $t_arr=array();
          foreach($moduleArr as $t_val){
        $t_arr += array($t_val['code'] => $t_val['cModule']);
       };
         $t_arr += array('Index'=>'Index','Public'=>'Public','login'=>'login','logout'=>'logout');
         
         //后台权限验证
         if(!in_array($this->controller_name,$t_arr)){
      
          $this->error("没该栏目权限!");
         }
        
           $tableWhere = "length(code) = 3 and iShow = 1 and code in ($catalogStr)";
        $tableOrder = "iOrder DESC, id ASC";
        // $catalogArr1 = $this->
        $catalogArr1 =Model('admin/CataAdmin')->getCata($tableWhere,$tableOrder,$meth=2);
         $this->assign('catalogArr1', $catalogArr1);
         $tableWhere = "length(code) = 6 and iShow = 1 and code in ($catalogStr)";
         

        $catalogArr2 = Model('admin/CataAdmin')->getCata($tableWhere,$tableOrder,$meth=3);
        
         $this->assign('catalogArr2',$catalogArr2); 
      }
      ///
   protected function buildParam($array)
    {
        $data=[];
        if (is_array($array)){
            foreach( $array as $item=>$value ){
                $data[$item] = $this->request->param($value);
            }
        }
        return $data;
    }

           }