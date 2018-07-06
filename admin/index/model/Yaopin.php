<?php
 namespace app\admin_extend\model;

 class Yaopin extends DingBase
 {
    protected $table="yaopin1";
    public function checkproduct ($map=[])
    {
     $data=$this->where($map)->find();
     return $data;
    }
    public function getproduct ($map=[])
    {
     $data=$this->where($map)->select();
     return $data;
    }
    public function addpingzhong($map=[])
    {
      if(!$this->checkproduct($map))
      {
         $this->editData($map);
      }
      
    }
    public function editData ($data)
    {
        if (isset($data['id'])){
            if (is_numeric($data['id']) && $data['id']>0){
                    $save = $this->allowField(true)->save($data,[ 'id' => $data['id']]);
            }else{
                 $this->data($data);
                $save  = $this->allowField(true)->isUpdate(false)->save();
            }
        }else{
            $this->data($data);
            $save  = $this->allowField(true)->isUpdate(false)->save();
        }
        if ( $save == 0 || $save == false) {
            $res=[  'code'=> 1009,  'msg' => '数据更新失败', ];
        }else{
            $res=[  'code'=> 1001,  'msg' => '数据更新成功',  ];
        }
        return $res;
    }
 }