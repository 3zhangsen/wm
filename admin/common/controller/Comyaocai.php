<?php 
 namespace app\common\controller;
 use think\Controller;
 use think\Db;
 class Comyaocai extends Controller
 {
    //继承父类构造函数
    public function _initialize()
     {
     parent::_initialize();
        }
   // 显示价格的预览编辑页面
    public function  index()
    {
    $product=model('Goods');
    $productlist=$product->distinct(true)->field('goods_name,goods_id')->where('')->cache('goods_name')->select();
       return  $productlist;
    } 
    //图表页面的显示后台可以不使用
    public function echartprice()
    {
    $product=model('Yaopin');
    $productlist=$product->distinct(true)->field('product')->where('')->select();
    return $productlist; 
       } 

 
    public function getproduct()
    {  
         $yaocaiprice=[
         'product'=>'product',
        ];
     $fomdata=input('param.');
       if (is_array($yaocaiprice))
          {
            foreach( $yaocaiprice as $item=>$value ){
                if($fomdata[$value])
                $productdata[$item] = $fomdata[$value];
            }
          }
    //print_r(json_encode($productdata));exit;
    $product=model('Yaopin');
    $data=$product->getproduct($productdata); 
    // print_r(json_encode($data));exit;
    return json_encode($data);
    }
    //ajax获取价格
    public function ajaxprice()
    {
     $data=input("post.");
     //print_r(json_encode($id));exit; 
     if(!empty($data['leftime'])&&!empty($data['righttime']))
     {
      $yaoprice=Db('t_pirce')->where('yao_id',$data['id'])->whereTime('theTime','between',[$data['leftime'], $data['righttime']])->order('theTime desc')->select();
     }
     else
     {
     $yaoprice=Db('t_pirce')->where('yao_id',$data['id'])->order('theTime desc')->select();
       }
      
     return json_encode($yaoprice);
    
    }
   
 
 //导出数据时增加药品品种
 public function exlproduct()
 {
    $goods=model('Goods');
    $yaopin=model('Yaopin');
    $yaocaiprice=[
         //'product'=>'product',
         'spec' => 'spec',
         'market' =>'market',            
         'quality'=>'quality',
         'Origin' =>'Origin',             
        ];
        $vv=array();
    $data=input('post.');
    foreach ($data as $yy => $dd)
     {             
      $productdata=[];
      if (is_array($yaocaiprice))
      {
        foreach( $yaocaiprice as $item=>$value ){
            $productdata[$item] = $dd[$value];
        }
         $goodid=$goods->where('goods_name',$dd['product'])
            ->find();
         if(empty($goodid['goods_id']))
         {

            $goods->data([
            'goods_name'  => $dd['product'],
             'price' =>  100
                ]);            
            $goods->isUpdate(false)->save();
            // 获取自增ID
           $productdata['product']=$goods->goods_id;
         }
         else
         {
             $productdata['product'] = $goodid['goods_id'];
         }
       }  
       // return json_encode($productdata);
     $yaopin->addpingzhong($productdata);

     }
    // return json_encode($vv);
    }


  ///导出数据
   public function exported()
   {
     $id=input('id');
     if(!is_numeric($id))
      {
      $this->error('请选择你要下载的药材');
      }
     //$product=Db('yaopin1')->where('product',$id)->select();
    //  $idlist=array();
     $dd=Db('goods')->where('goods_id',$id)->field('goods_name')->find();
    $product['product']=$dd['goods_name'];
    //  foreach ($product as $key => $value) {
    //      $list=Db('t_pirce')->where('yao_id',$value['id'])->select();
    //      $list[]
    //  }
//$list=Db::query("select * from  t_pirce inner join  yaopin1 on yaopin1.product=goods.goods_id where goods.goods_id=".$id);
  $list=Db::query("select  t_pirce.thePrice,t_pirce.theTime,yaopin1.spec,yaopin1.market,yaopin1.quality,yaopin1.Origin from  t_pirce INNER JOIN yaopin1 on t_pirce.yao_id= yaopin1.id where yaopin1.product=".$id."");
// print_r($list);exit;

     $this->exportExcel($list,$product);
   }
    //表格导出处理
   public function exportExcel($list,$product){

        //1.从数据库中取出数据
        vendor("PHPExcel.PHPExcel");
         $objPHPExcel = new \PHPExcel();


        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '品名')                      
                ->setCellValue('B1', '规格')
                ->setCellValue('C1', '价格')
                ->setCellValue('D1', '时间')
                ->setCellValue('E1', '市场')
                ->setCellValue('F1', '质量')
                ->setCellValue('G1', '产地');
    
        for($i=0;$i<count($list);$i++){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$product['product']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),$list[$i]['spec']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),$list[$i]['thePrice']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2),$list[$i]['theTime']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),$list[$i]['market']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2),$list[$i]['quality']);
             $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2),$list[$i]['Origin']);
        }
        

        //7.设置保存的Excel表格名称
        $filename = '价格表'.date('ymdhis',time()).'.xls';
 
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle('价格表');
        
        //9.设置浏览器窗口下载表格
        header("Content-Type: application/force-download");  
        header("Content-Type: application/octet-stream");  
        header("Content-Type: application/download");  
        header('Content-Disposition:inline;filename="'.$filename.'"'); 
        
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
        //下载文件在浏览器窗口
        $objWriter->save('php://output');

        exit;
    }
  





    public function saveprice()
    {
     $yaocaiprice=Db('price');
     $data=input('post.');

      $updata=[
             // 'product'=>$data['product'],
             // 'spec' => $data['spec'],
             // 'market' => $data['market'],
             'thePrice' => $data['thePrice'],             
             // 'quality'=>$data['quality'],
             // 'Origin' => $data['Origin'],
             
            ];
     if(empty($data['id']))
      {
        
            $rs=$yaocaiprice->insert($updata);
            $id=$Table->getLastInsId();
    
              ////////////
              if($rs!==false)
            {
             
              $this->success('价格输入成功');
            }
            else
            {
              $this->error('价格输入失败');
            }   
       }
      else{
            $rs=$yaocaiprice->where('id',$data['id'])->update(
            $updata);
       
      if($rs!==false){ 
          $this->success('数据更新成功！'); 
        }else{ 
          $this->error("没有更新任何数据!"); 
        }
            } 
       

    }
    public function Editprice()
    {
    $id=input('param.id');
    $productid=input('param.productid');
    $product=Db('yaopin1')->find($productid);
    $yaocaiprice=Db('t_pirce')->find($id);
    $this->assign('yaocaiprice',$yaocaiprice);
    $this->assign('product',$product);
    return $this->fetch($this->controller_name.'/'.$this->action_name);
    }
    public function delprice()
    {
     $id=input('param.id');     
     $yaocaiprice=Db('t_pirce')->delete($id);
     if($yaocaiprice)
     {
      $this->success("删除数据成功");

     }
     else
     {
       $this->error("删除数据失败");
     }
    }


 /////药材品种管理以及编辑
    public  function yaocaipingz()
     {
     $product=model('Yaopin');
    $productlist=$product->select();
    $this->assign('product',$productlist);///获取所有的品种用于选择输出 
    return $this->fetch($this->controller_name.'/'.$this->action_name);
     }
    public function yaocaiedit()
    {
      $id=input('param.id');
      $product=Db('yaopin1')->find($id);   
    $this->assign('product',$product);
    return $this->fetch($this->controller_name.'/'.$this->action_name);
    }
    public function savepingz()
    {
     $yaocaiprice=Db('yaopin1');
     $data=input('post.');

      $updata=[
             'product'=>$data['product'],
             'spec' => $data['spec'],
             'market' => $data['market'],                        
             'quality'=>$data['quality'],
             'Origin' => $data['Origin'],
             
            ];
     if(empty($data['id']))
      {
        
            $rs=$yaocaiprice->insert($updata);
            $id=$Table->getLastInsId();
    
              ////////////
              if($rs!==false)
            {
             
              $this->success('价格输入成功');
            }
            else
            {
              $this->error('价格输入失败');
            }   
       }
      else{
            $rs=$yaocaiprice->where('id',$data['id'])->update(
            $updata);
       
      if($rs!==false){ 
          $this->success('数据更新成功！'); 
        }else{ 
          $this->error("没有更新任何数据!"); 
        }
            } 
       

    }
 }
 ?>
