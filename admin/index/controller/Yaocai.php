<?php 
 namespace app\index\controller;
 //use app\admin\controller\Basic;
 use think\Controller;
 use app\common\controller\Comyaocai; 
 class Yaocai extends Controller
 {
  public  $dd;
    public function _initialize()
     {
      $this->dd=   new comyaocai();
     parent::_initialize();
        }
    public function index()
    {
    $this->redirect('index/Yaocai/echartprice');
    }
    public function echartprice()
    {
         $vv=action('Wxpay/ismobile');
      $dd=action('Wxpay/isWeixin');
      if($vv&&$dd)
      {
      $this->assign('showwhat',1);
           }
      else
        $this->assign('showwhat',0);
      //$this->assign('showwhat',1);
       $productlist= $this->dd->index();
    $this->assign('product',$productlist);///获取所有的品种用于选择输出 
      return $this->fetch('Yaocai/echartprice');
    } 
      public function ajaxprice()
    {
        $data= $this->dd->ajaxprice();
      print_r($data);exit;     
     }
        public function getproduct()
    {      
      //print_t(input('product'));exit;
        $data= $this->dd->getproduct();
         print_r($data);exit;
       }
       public function exported()
   {
    $data= $this->dd->exported();
  }
   public function getgoods()
   {
    $productlist= $this->dd->index();
    print_r(json_encode($productlist));exit;
   }
 }
 ?>
