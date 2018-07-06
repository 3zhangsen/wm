<?php
namespace app\admin\controller;
Class Group extends Basic
{
    //模块表名
    public $tableName = 'usr_group';
    //后右侧 名称
    private static $usr_group;

    public $tableNameAdminCatalog='usr_catalog';
    public $tableArr= array(
              'id'=>'',             
              'group_name'=>'', 
              'catalog'=>''
              );
    function _initialize()
    {
        parent::_initialize();
      self::$usr_group= model('admin/Group');
    }

    public function index()
    {
        $pageparams = array();
        $condition = array();
       // $table = db($this->tableName);
        $tableWhere = $condition;
        $tableOrder = "";
        
        $tableArr = self::$usr_group->where($tableWhere)->order($tableOrder)->select();
        $this->assign('tableArr',$tableArr);
        return $this->fetch($this->controller_name.'/index');
    }

    public function Addgroup(){
        $catalog=Model('admin/CataAdmin');
        $tableWhere = "";
        $tableOrder = "length(code) ASC, iOrder DESC, id ASC";
        $catalogArr =$catalog->getCata($tableWhere,$tableOrder);
        $this->assign('catalogArr',$catalogArr);
        $this->assign('tableArr',$this->tableArr);
        $this->assign('tableArr1',array());
        return $this->fetch($this->controller_name.'/Editgroup');
    }

    public function Editgroup(){
        $id = input('id');
        if(empty($id)){
            $this->error("请选择编辑项!");
        }

        $table = db($this->tableName);
        $tableWhere = "id = $id";
        $tableArr = $table->where($tableWhere)->find();
        $this->assign('tableArr',$tableArr);
        $theCatalogArr = unserialize($tableArr['catalog']);
       // print_r($theCatalogArr);exit;
        if(!empty($theCatalogArr['code']))
        {
        $this->assign('tableArr1',$theCatalogArr['code']);
        }
        else{
        $this->assign('tableArr1',array());  
        }
        //print_r($theCatalogArr);exit;
       $catalog=Model('admin/CataAdmin');
        //$table = db('usr_catalog');
        $tableWhere = "";
        $tableOrder = "length(code) ASC, iOrder DESC, id ASC";

       $catalogArr =$catalog->getCata($tableWhere,$tableOrder);
        $this->assign('catalogArr',$catalogArr);


       return $this->fetch($this->controller_name.'/Editgroup');
    }

    public function Savegroup()
    {
        //栏目权限

        $group_name = input('cName');
        if(empty($group_name))
        {
           $this->error('权限创建失败',$this->controller_name.'/index');  
        }
        $column_power = array();
        $column_power=input('catalog/a');
        $column_power_arr = array();
        if($column_power != array())
        {
        foreach((array)$column_power as $val){
            $column_power_arr['code'][] = $val;
                       }
            }
       //print_r($column_power);exit;
      $catalog = serialize($column_power_arr);
      $id = input('id');
      $group = model('admin/Group');
      if(empty($id)) //新增数据
      { 
        $group->data([
           'group_name'=>$group_name,
           'catalog'=>$catalog
        ]);
        $group->save();
        if(!empty($group->id))
        {
          $this->error('创建用户组成功',$this->controller_name.'/index');
        }
        else
        {
           $this->error('创建用户组失败',$this->controller_name.'/index');
        }      

      }else{ //修改数据
        
        $up_group=$group->where('id',$id)->update([
         'catalog'=>$catalog
        ]);
        
        if($up_group==false)
        {
         
          $this->error('更新失败',$this->controller_name.'/index');
        }
        else
        {
           $this->error('更新成功',$this->controller_name.'/index');
        }
      }

    }


    public function Delgroup()
    {
      $id=input('id');
      if(empty($id))
      {
         $this->error("请选择要删除的用户组",$this->controller_name.'/index');
      }  
      $delusr=db('usr_group')->where('id','eq',$id)->delete();
       if($delusr==false)
       {
        $this->error('删除错误',$this->controller_name.'/index');
       }
       $this->success('删除成功',$this->controller_name.'/index');
    }
}
?>