<?php 
 namespace app\admin\controller;
  use think\Request;
 class Index extends Basic
 {
    
  public function _initialize() {
     parent::_initialize();
     //print_r('ddd');
  }
  public function index()
  {
     return $this->fetch($this->controller_name.'/'.$this->action_name);
  }
 
 }

 ?>