<?php
 namespace app\admin\model;
 use think\Model;
 use think\Db;
 class CataAdmin extends Model
 {
   protected $table="usr_catalog";
   public function getCata($tableWhere,$tableOrder,$meth=0)
   {
     $result=$this->where($tableWhere)->order($tableOrder)->select();
      $result=json_decode(json_encode($result),true);
      $catalogArr = array();
      if($meth==3)
      {
      $catalogArr = $this->trimCatalog($result,$meth,array(),$code=0,$theCodeLength=0,$oneCodeLength=6,$endCodeLength=6);}
      else
      {
        $catalogArr = $this->trimCatalog($result,$meth,array(),$code=0,$theCodeLength=0,$oneCodeLength=3,$endCodeLength=6);
      }
     return $catalogArr;
   }
//排整栏目数组
   public function trimCatalog($arr,$meth=0,$catalogArr=array(),$code=0,$theCodeLength=0,$oneCodeLength=3,$endCodeLength=6)
   {
        $theCodeLength = $theCodeLength + $oneCodeLength;
        foreach($arr as $key=>$val){
            $nowCodeLength = 0;
            $nowCodeLength = strlen($val['code']);
            //编码前段
            $codePrefix = 0;
            if($code != 0){
                $codePrefix = substr($val['code'],0,$theCodeLength-$oneCodeLength);
            }
            if($theCodeLength == $nowCodeLength and $codePrefix == $code){
                $str = '';
                $strNum = $nowCodeLength / $oneCodeLength;
                for($i=1;$i<$strNum;$i++){
                    $str .= '—— ';
                }
                if($meth==0){
                $catalogArr += array($val['id']=>$val);
                $catalogArr[$val['id']]['cName'] = $str.$val['cName'];
                 }
                elseif($meth==1) {$catalogArr += array($val['code']=>$str.$val['cName']);}
                // elseif($meth==2)
                // {
                //   //$catalogArr =array();

                // $catalogArr += array($val['id']=>$val);
                // $catalogArr[$val['id']]['cName'] = $val['cName'];
                // }
                else
                {
                 $catalogArr += array($val['id']=>$val);
                $catalogArr[$val['id']]['cName'] = $val['cName'];  
                }
                unset($arr[$key]);
                if($nowCodeLength <= $endCodeLength){
                    $catalogArr += $this->trimCatalog($arr,$meth,$catalogArr,$val['code'],$nowCodeLength);
                }
            }
        }
        return $catalogArr;
    }


      public function getOptionArr(){
        
        $tableWhere = "length(code) <= 3";
        $tableOrder = "length(code) ASC";
        $catalogOptionArr=$this->getCata($tableWhere,$tableOrder,$meth=1);
        $catalogOptionArr[0]='无';
        return $catalogOptionArr;
    }


     public function maxCode(){
            $tableWhere = "length(code) = 3";
            $tableOrder = "code DESC";
            $maxCode = $this->where($tableWhere)->max('code');
            if(empty($maxCode)){
                $maxCode = 100;
            }
            $maxCode += 1;
            $maxCode = trim($this->f_numtostr($maxCode));
            return $maxCode;
    }

    public function f_numtostr($num) { 
    $result = " "; 
    while($num > 0) { 
        $v = $num - floor($num/10)*10; 
        $num = floor($num/10); 
        $result = $v.$result; 
    }
    return $result; 
    }

    public function checkpost($data)
    { 
       $validate=\think\Loader::validate('CataAdmin');
        if($data['action']=='EditCata')
         {
          $result = $validate->scene('edit')->check($data);
         }
         elseif($data['action']=='AddCata')
         {
          $result = $validate->scene('add')->check($data);
         }
         else{
                  }
         if(!$result)
         {
            //$this->error($validate->getError()); 
            return $validate->getError();
         }
    }
  public function savecata($data)
  { 
     $resstat=0;
     $resmess='';
     $this->data([
             'code'=>$data['code'],
             'cName'=>$data['cName'],
             'cModule'=>$data['cModule'],
             'cAction'=>$data['cAction'],
             'cUrl'=>$data['cUrl'],
             'iOrder'=>$data['iOrder'],
             'iShow'=>1,
              ]);
            $this->save();
            if(!empty($this->id))
            {
              $resstat=1;
              $resmess="创建功能成功";
             return array($resstat,$resmess);
              //$this->error('创建功能成功',$this->controller_name.'/index');
            }
            else
            {
                $resstat=0;
                $resmess="创建失败";
                 return array($resstat,$resmess);
                //return($this->id);
              // $this->error('创建功能失败',$this->controller_name.'/index');
            }
  }
  public function updatacata($data)
  {
     $resstat=0;
     $resmess='';
        $up_usr=$this->where('id',$data['cata_id'])->update([
              'code'=>$data['code'],
             'cName'=>$data['cName'],
             'cModule'=>$data['cModule'],
             'cAction'=>$data['cAction'],
             'cUrl'=>$data['cUrl'],
             'iOrder'=>$data['iOrder'],
             'iShow'=>1,      
            ]);
           if($up_usr==false)
           {
            $resstat=0;
                $resmess="更新失败";
                 return array($resstat,$resmess);
             
            // $this->error('功能没有更新',$this->controller_name.'/index');
            // return ()
           }
           else
           {
              $resstat=1;
              $resmess="更新成功";
               return array($resstat,$resmess);

            // $this->success('功能更新成功',$this->controller_name.'/index');
            
           }
  } 
   
 }