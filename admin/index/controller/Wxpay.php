<?php 
 namespace app\index\controller;
 use think\Controller;
 use think\Db;
 class Wxpay extends Controller
 {
  public $UsrId='UsrId';
  public function _initialize() {
     parent::_initialize();
     // session_start(); 
    }

public function choicePay()
{
   $vv=$this->ismobile();
   $dd=$this->isWeixin();
   $trade_no=input('trade_no');
   if($vv)
   {
    //如果为在手机微信端就跳转到预定页面并且要求用户从微信外部浏览器打开页面
       if($dd)
       {
       // //print_r('weixin');
       header("location:http://price.zgycsc.com/echart/public/index.php/index/Yaocai/echartprice");  
        
        }
    else
       $this->h5pay();
       //如果是手机端但不是微信端就跳到h5支付
        }
    else
    {
    $this->native();
     return $this->fetch('Wxpay/native');

    //如果是电脑端的就直接使用扫码支付
    }
}
// public function paycontent($trade_no)
// {
//   $goods = M()->table('orderlist  as  a')->join('goods  as  b  on  b.goods_id = a.goods_id')->where('a.out_trade_no = '.$trade_no)->select();
//   if(empty($goods))
//   {
//     $this->error("没有订阅成功请重新订阅",'Checkorder/place');
//   }
//   return $goods;
// }

 public function native()
 {
   //session_start(); 
   $trade_no=input('trade_no');
     vendor("WxpayAPI.lib.WxPay#NativePay");
     $notify = new \NativePay();
     $input = new \WxPayUnifiedOrder();
     if(empty($trade_no))
     {
      $this->error("订单号有误",'index/index');
     }
   $goods = Db::table('orderlist a')->join('goods b','b.goods_id = a.goods_id')->where('a.out_trade_no',$trade_no)->select();
   // print_r($goods);exit;
  if(empty($goods))
  {
    $this->error("没有订阅成功请重新订阅",'Checkorder/place');
  }
 $price=0;
  foreach ($goods as $key => $value) {
    if($value['pay'])
    {
       $this->error("该订单已经支付成功",'index/index');
    }
    $price=$price+1;
  }

    $out_trade_no=$trade_no.'D'.date("mdHis");
  
    $input->SetBody("佳本科技药材价格信息订阅付款");
    $input->SetAttach("佳本科技");
    $input->SetOut_trade_no($out_trade_no);
    $input->SetTotal_fee($price);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("no");

    $input->SetNotify_url("http://price.zgycsc.com/echart/public/index.php/index/Wxpay/notify");
    $input->SetTrade_type("NATIVE");
    $input->SetProduct_id($out_trade_no);
    $result = $notify->GetPayUrl($input);
    $url2 = $result["code_url"];
    // print_r($url2);exit;
    if(empty($url2))
    {
       $this->error("刚由于系统繁忙，请刷新再次尝试，如还遇到问题请重新预约");
    }
    
   $this->assign("code_url",urlencode( $url2));
   $this->assign("goods_id",$trade_no);
   // return $this->fetch('Wxpay/native');

 }
 //huoqu erweima 
   public function phpcode()
   {
    error_reporting(E_ERROR);
    vendor("phpqrcode.phpqrcode");
    $url = urldecode($_GET["data"]);
    \QRcode::png($url);
    //return($ercode); 
   }
///对调看是否已经支付成功 
   public function notify()
    {
      $xml = file_get_contents('php://input');          
      vendor("WxpayAPI.lib.WxPay#Api");
      vendor("WxpayAPI.lib.WxPay#Notify");
     try {
          $result = \WxPayResults::Init($xml);
          if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS')
              {
               $pp=explode("D",$result['out_trade_no']);
               $dd= db('orderlist')->where('out_trade_no',$pp[0])->update(['transaction_id' =>$result['transaction_id'],'pay'=>1,'total_fee'=>$result['total_fee'],'payTime'=>date('Ymd'),'lateTime'=>date('Ymd', strtotime('1 year'))]);
                echo "<xml>
                  <return_code><![CDATA[SUCCESS]]></return_code>
                  <return_msg><![CDATA[OK]]></return_msg>
                </xml>";
               }
            } catch (\WxPayException $e){
              $msg = $e->errorMessage();
              return false;
            }
    }
  
 public function h5pay()
   {
     $trade_no=input('trade_no');
       if(empty($trade_no))
     {
      $this->error("订单号有误",'index/index');
     }
     vendor("WxpayAPI.lib.WxPay#Api"); 
     $input = new \WxPayUnifiedOrder();
    $goods = Db::table('orderlist a')->join('goods b','b.goods_id = a.goods_id')->where('a.out_trade_no',$trade_no)->select();
  if(empty($goods))
  {
    $this->error("没有订阅成功请重新订阅",'Checkorder/place');
  }
 $price=0;
  foreach ($goods as $key => $value) {
    if($value['pay'])
    {
       $this->error("该订单已经支付成功",'index/index');
    }
    $price=$price+1;
  }
    
    $out_trade_no=$trade_no.'D'.date("mdHis");
    $usrip=$this->get_client_ip();
    $input->SetSpbill_create_ip($usrip);
    $input->SetBody("佳本科技药材价格信息订阅付款");
    $input->SetAttach("佳本科技");
    $input->SetOut_trade_no($out_trade_no);
    $input->SetTotal_fee($price);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("no");

    $input->SetNotify_url("http://price.zgycsc.com/echart/public/index.php/index/Wxpay/notify");
    $input->SetTrade_type("MWEB");
    $input->SetProduct_id($out_trade_no);
     
   $result=\WxPayAPI::unifiedOrder($input);
   
    $url2 = $result["mweb_url"]."&redirect_url=http://price.zgycsc.com/echart/public/index.php/index/Wxpay/backpage/id/".$trade_no;   
   header("Refresh:0;url=$url2");  
   exit();
    // print_r($url2);exit;

   }

public function backpage()
{
  
  $trade_no = input('id');
  $goods = Db::table('orderlist a')->join('goods b','b.goods_id = a.goods_id')->where('a.out_trade_no',$trade_no)->where('pay',1)->select();
  //$goods = Db::table('orderlist  as  a')->join('goods  as  b','b.goods_id = a.goods_id')->where('a.out_trade_no',$trade_no)->where('pay',1)->select();
 // print_r($goods);exit;
  if($goods)
  {

   $paystatus=1;

  }
  else{
    $paystatus=0;
  } 
  $this->assign('orderid',$trade_no);
  $this->assign('paystatus',$paystatus);
 return $this->fetch('Wxpay/backpage');
}

public function ismobile() {  
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备  
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))  
        return true;  
      
    //此条摘自TPM智能切换模板引擎，适合TPM开发  
    if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])  
        return true;  
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息  
    if (isset ($_SERVER['HTTP_VIA']))  
        //找不到为flase,否则为true  
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;  
    //判断手机发送的客户端标志,兼容性有待提高  
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {  
        $clientkeywords = array(  
            'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'  
        );  
        //从HTTP_USER_AGENT中查找手机浏览器的关键字  
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {  
            return true;  
        }  
    }  
    //协议法，因为有可能不准确，放到最后判断  
    if (isset ($_SERVER['HTTP_ACCEPT'])) {  
        // 如果只支持wml并且不支持html那一定是移动设备  
        // 如果支持wml和html但是wml在html之前则是移动设备  
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {  
            return true;  
        }  
    }  
    return false;  
 } 

public function isWeixin() { 
  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
    return true; 
  } else {
    return false; 
  }
}
public function get_client_ip(){
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {

        $ip = getenv('HTTP_CLIENT_IP');

    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {

        $ip = getenv('HTTP_X_FORWARDED_FOR');

    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {

        $ip = getenv('REMOTE_ADDR');

    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {

        $ip = $_SERVER['REMOTE_ADDR'];

    }

    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

 }
