<?php
 namespace app\admin_extend\model;
 use think\Model;
 class Price extends Model
 {
 protected $table="t_pirce";
 public $status=[
    'code'=>'',
    'errorproduct'=>array(),
    'errordate'=>array(),
 ];
 public function upprice($uploadfile)
   {
    $data=$this->getExcel($uploadfile);
    //print_r($data);exit;   
  if($this->status['code']==2)
    {
     return $this->status; 
    };
    $product=model('admin_extend/Yaopin');
    $errormessage=array();
    $numarray=array();
    foreach ($data as $key => $value) {
      $qq['product']=$value['product'];
      $qq['spec']=$value['spec'];
      $qq['market']=$value['market'];
      $qq['quality']=$value['quality'];
      $qq['Origin']=$value['Origin'];
      $productno=$product->checkproduct($qq);
      try {
            $this->data(['thePrice' =>$value['thePrice'],
            'yao_id'=>$productno['id'],
            'theTime' => $value['theTime']]);
            $this->isUpdate(false)->save(); 
                  }
       catch (\Exception $e) {
             if(empty($productno['id']))
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