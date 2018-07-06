<?php 
 namespace app\index\controller;
 use think\Controller;
  use app\common\controller\Comyaocai; 
 class Checkorder extends Controller 
{
    public $UsrId='UsrId';
       public $UsrIp='UsrIp';
       public $UsrName='UsrName';
       public $displayName='displayName';
       public $UsrGroup='UsrGroup';
       public $UsrStatus='UsrStatus';
       public $controller_name='Index';
       public $action_name='index';
       public $module_name='index';
       public $webName="";
    public function _initialize()
     {
     parent::_initialize();
      session_start();  
            //如果没有登录就跳转到登录页面  
     if(empty($_SESSION[$this->UsrId]))
      {
       // print_r('neimei');
     $this->redirect('admin/Login/login');
      }
     ///////用户登录后将用户信息渲染到view
     $this->assign('UsrName',$_SESSION[$this->UsrName]);
     $this->assign('displayName',$_SESSION[$this->displayName]);
            }
            
     public function check()
     {

     $vv=action('Wxpay/ismobile');
      $dd=action('Wxpay/isWeixin');
      if($vv&&$dd)
      {
      $this->assign('showwhat',1);
      $this->redirect('index/Yaocai/echartprice');
      }
      $tady=date("Y/m/d");//获取当天时间
      $goodsid=input('id');//获取要下载的商品id
      // $order=db('orderlist')->where('use_id',$_SESSION[$this->UsrId])->where('goods_id',$goodsid)->where('lateTime','>=',$tady)->select();//查询是否有这个商品以及这个用户的支付记录
       $order=db('orderlist')->where('use_id',$_SESSION[$this->UsrId])->where('goods_id',$goodsid)->select();
       $orderstatus=0;
      foreach ($order as $key => $value) {
          if($value['pay']==1)
          {
             if ($tady<=$value['lateTime']) {
                $orderstatus=2;
                break;
             }
             else
                $orderstatus=1;
          }
      }
    switch ($orderstatus) {
        case 0:
            $this->error('您还没有购买本套餐','Checkorder/place');
            break;
         case 1:
            $this->error('您的套餐已到期，如有需要请再次购买','Checkorder/goon');
            break;
        case 2:
            $this->redirect("index/Yaocai/exported",["id"=>$goodsid]);
            break;
        default:
            $this->error('核对出错','');
            break;
    }

     
    }
       public function checkjs()
     {
      $goodsid=input('id');
      if (empty($goodsid)) {
        return json_encode(' no id');
      }
        $goods = M()->table('orderlist  as  a')->join('goods  as  b  on  b.goods_id = a.goods_id')->where('a.out_trade_no = '.$goodsid)->where('pay',1)->select();
      if ($goods)
      {
      return json_encode(1024);
      }
      else
      {
      return json_encode('fail');
      }
     }
     public function place()
     {
      $vv=action('Wxpay/ismobile');
      $dd=action('Wxpay/isWeixin');
      if($vv&&$dd)
      {
      $this->assign('showwhat',1);
           }
      else
        $this->assign('showwhat',0);
      $dd=new comyaocai();
      $productlist= $dd->index();
       $this->assign('product',$productlist);///获取所有的品种用于选择输出 
      return $this->fetch('Checkorder/place');
     }
    // public function goon()
    // {

    // }
 public function saveorder()
  {
    $goodslist=input('idlist');
    $out_trade_no=$_SESSION[$this->UsrId].date("YmdHis");
    $data=explode(',', $goodslist);
    $data1=array();
    foreach ($data as $key => $value) {
      $data1[$key]['use_id']=$_SESSION[$this->UsrId];
      $data1[$key]['goods_id']=$value;
      $data1[$key]['out_trade_no']=$out_trade_no;
      $data1[$key]['pay']=0;
    }
    // print_r($data1);exit;
    $table=db('orderlist')->insertAll($data1);
    if($table)
    {
    $this->SUCCESS('订阅成功，将转入支付页面',url('Wxpay/choicePay',['trade_no'=>$out_trade_no]));
    }
    else
    $this->error('订阅失败，请稍后再试','Checkorder/place');
  }

}