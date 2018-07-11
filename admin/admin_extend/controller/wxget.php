<?php
  namespace app\admin_extend\controller;
  use think\Controller;
  class wxget extends Controller
  {
     
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
               $df= M('physical')->where('id='.$pp[0].'&& cPhone= '.$pp[1].' && physical= '.$pp[2])->save(['transaction_id' =>$result['transaction_id'],'ifufei'=>1,'total_fee'=>$result['total_fee']]);
              if($df)
              {
                echo "<xml>
                  <return_code><![CDATA[SUCCESS]]></return_code>
                  <return_msg><![CDATA[OK]]></return_msg>
                </xml>";
             }
         }
     }
     
   }

  }
