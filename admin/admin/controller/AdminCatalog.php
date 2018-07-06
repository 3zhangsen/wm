<?php
namespace app\admin\controller;
Class AdminCatalog extends Basic
{
    //模块表名
    public $tableName = 'usr_catalog';
    private static $CataAdmin; 
    //后右侧 名称
    public $webName = '后台栏目';
    public $tableArr= array(
                      'id'=>'',
                      'code'=>'',
                      'cName'=>'',
                      'cModule'=>'',
                      'cAction'=>'',
                      'cUrl'=>'',
                      'iOrder'=>'',
                      'iShow'=>''
                      );

    function _initialize()
    {
      parent::_initialize();
      $setIsShowArr=array(1=>'显示',2=>'不显示');
      $this->assign('setIsShowArr',$setIsShowArr);
      self::$CataAdmin=model('admin/CataAdmin');
    }

    public function index()
    {
        $tableWhere = "";
        $tableOrder = "length(code) ASC, iOrder DESC, id ASC";
        $catalogArr = self::$CataAdmin->getCata($tableWhere,$tableOrder);
        $this->assign('CataInfo',$catalogArr);
        return $this->fetch($this->controller_name.'/'.$this->action_name);

    }
    public function SaveCata()
      {
        $data=input('post.');
        $tt=self::$CataAdmin->checkpost($data);
        if(!empty($tt))
        {
           $this->error($tt);
        }
        else
        {
            if($data['action']=='AddCata')
            {
              $result=self::$CataAdmin->savecata($data);
                if($result[0]==1)
                {
                // print_r($result);
                  $this->success($result[1],$this->controller_name.'/index');
                }
                else
                {
                 $this->error($result[1],$this->controller_name.'/index');
                }
            }
            elseif($data['action']=='EditCata')
            {
              $result=self::$CataAdmin->updatacata($data);
               if($result[0]==1)
                {
                // print_r($result);
                  $this->success($result[1],$this->controller_name.'/index');
                }
                else
                {
                 $this->error($result[1],$this->controller_name.'/index');
                }
            }
            else
            {
              $this->error("修改要求不对");
            }
        }
       }

    public function DelCata()
    {

       $usr_id=input('id');
       if(empty($usr_id))
       {
        $this->error("请选择要删除的功能",$this->controller_name.'/index');
       }
       $delusr=self::$CataAdmin->where('id','eq',$usr_id)->delete();
       if($delusr==false)
       {
        $this->error('删除错误',$this->controller_name.'/index');
       }
       $this->success('删除成功',$this->controller_name.'/index');
      
    }
   
    public function AddCata(){

        $catalogSelected = 0;
        $this->assign('catalogSelected',$catalogSelected);

        //列表数组
        $catalogOptionArr = self::$CataAdmin->getOptionArr();//去取大的功能模块
        $this->assign('catalogOptionArr',$catalogOptionArr);
       // print_r($catalogOptionArr);exit;
        //$this->web_op();
        //print_r($catalogOptionArr);exit;
        //栏目编号
        $this->tableArr['code'] = self::$CataAdmin->maxCode();
        $this->assign('tableArr',$this->tableArr);
        return $this->fetch($this->controller_name.'/EditCata');
    }

   public function getMaxCode()
   {
        $scode = trim(input('scode'));
    
        if($scode != ''){
            $codeLength = strlen($scode) + 3;
           
            if($scode != 0){
                $tableWhere = "length(code) = $codeLength and code like '".$scode."%'";
            }else{
                $tableWhere = "length(code) = 3";
            }
            $tableOrder = "code DESC";
            $maxCode = self::$CataAdmin->where($tableWhere)->max('code');
            if(empty($maxCode)){
                $maxCode = $scode.'100';
            }
            $maxCode += 1;
            //$maxCode = trim($this->f_numtostr($maxCode));
            echo $maxCode;
        }else{
            echo '';
        }
    }
    public function EditCata(){
        $id = input('id');
        $tableWhere = "id = $id";
        $tableArr = self::$CataAdmin->where($tableWhere)->find();
        $this->assign('tableArr',$tableArr);
        $catalogOptionArr = self::$CataAdmin->getOptionArr();//去取大的功能模块
        $this->assign('catalogOptionArr',$catalogOptionArr);
        //默认值
        $catalogSelected = 0;
        $codeLength = strlen($tableArr['code']) - 3;
        if($codeLength > 0){
            $catalogSelected = substr($tableArr['code'],0,$codeLength);
        }
        $this->assign('catalogSelected',$catalogSelected);
    
        //$this->web_op();

      return $this->fetch($this->controller_name.'/'.$this->action_name);
    }
 
}
?>