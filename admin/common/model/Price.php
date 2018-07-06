<?php
 namespace app\common\model;
 use think\Model;
 class Price extends Model
 {
 protected $table="t_pirce";
 public $status=[
    'code'=>'',
    'errorproduct'=>array(),
    'errordate'=>array(),
 ];
 //excel 的数据上传到数据库
 public function upprice($uploadfile)
   {
    $data=$this->getExcel($uploadfile);
     
  if($this->status['code']==2)
    {
     return $this->status; 
    };
    $product=model('admin_extend/Yaopin');
    $goods=model('admin_extend/Goods');
    $errormessage=array();
    $numarray=array();
    foreach ($data as $key => $value) 
    {
      
      //检查数据药材规格是否存在，并获取药材编号
      try {
            
             $goodid=$goods->where('goods_name',$value['product'])
            ->find();//检查是否有这个药品
              $qq['product']=$goodid['goods_id'];
              $qq['spec']=$value['spec'];
              $qq['market']=$value['market'];
              $qq['quality']=$value['quality'];
              $qq['Origin']=$value['Origin'];
              $productno=$product->checkproduct($qq);

           $data=['thePrice' =>$value['thePrice'],
            'yao_id'=>$productno['id'],
            'theTime' => $value['theTime']];
            ////去掉重复数据
            if($this->where($data)->find())
            {  
             $qq=null;
             continue;
            }
            else
            {
              $this->data(['thePrice' =>$value['thePrice'],
            'yao_id'=>$productno['id'],
            'theTime' => $value['theTime']]);
            $this->isUpdate(false)->save($data); 
             }
                  }
       catch (\Exception $e) {
             if(empty($productno['id'])||empty($goodid['goods_id']))
             {
              array_push($this->status['errorproduct'],$value);
             }
             else
             {
              array_push($this->status['errordate'],$value);
             }
            }
       $qq=null;
      }
      return $this->status;  
     // print_r($this->status);exit;
      }
  public function getExcel($uploadfile)
  {
    vendor("PHPExcel.PHPExcel");
    try{
      //$excel=new \PHPExcel();  
      $inputFileType = \PHPExcel_IOFactory::identify($uploadfile);
      $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
      $objPHPExcel = $objReader->load($uploadfile); 
      $sheet = $objPHPExcel->getSheet(0); 
      $highestRow = $sheet->getHighestRow(); // 取得总行数 
        for ($row=2;$row<=$highestRow;$row++)
       {
        $data[$row]['product'] = $objPHPExcel->getActiveSheet()->getCell("A".$row)->getValue();
        $data[$row]['spec'] = $objPHPExcel->getActiveSheet()->getCell("B".$row)->getValue();          
        $data[$row]['market'] = $objPHPExcel->getActiveSheet()->getCell("E".$row)->getValue();
        $data[$row]['quality'] = $objPHPExcel->getActiveSheet()->getCell("F".$row)->getValue();
        $data[$row]['Origin'] = $objPHPExcel->getActiveSheet()->getCell("G".$row)->getValue();
      $data[$row]['thePrice'] = $objPHPExcel->getActiveSheet()->getCell("C".$row)->getValue();
       $data[$row]['theTime'] = gmdate("Y-m-d",\PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("D".$row)->getValue()));
        }}
         catch (\Exception $e) {
             $this->status['code']=2; 
            }

        //需要验证数据的类型和数据的完整性
      return $data;
    }
  }
