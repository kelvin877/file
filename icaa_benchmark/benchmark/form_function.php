<?php




function benchmark_submit($section){
  global $survey_id;
  global $residential_id;
  global $location_id;

  $residential_year = date("Y");



  $database = Database::instance();



  include "benchmark_config.php";
  //include "benchmark_form1_config";



  foreach($section as $k =>$v):

    $params = array();
    $field = 'q_'.$v['question_no'];
    $benchmark_table = $v['benchmark_table'];


      if($v['ans_type']=='radio'){
        $strSQL = "select id from $benchmark_table where survey_id =:survey_id and question_id={$v['question_no']} and year=:year and location_id=:location_id";
        $params = array('survey_id'=>$survey_id,'year'=>$residential_year,'location_id'=>$location_id);
        $bExisted = recordExisted($strSQL, $params);



        if(count($bExisted) >0 ){
          $id = $bExisted['id'];
          $strSQL = "update $benchmark_table set option=:option where id=$id";
          $params = array('option' => (isset($_REQUEST[$field]) ? $_REQUEST[$field] : ''));
        }else {
          $strSQL = "insert into $benchmark_table set survey_id=:survey_id , question_id={$v['question_no']},option=:option,residential_id=:residential_id,year=:year , location_id=:location_id ";
          $params = array('survey_id'=>$survey_id ,'option'=>(isset($_REQUEST[$field]) ? $_REQUEST[$field] : ''),'residential_id'=>$residential_id,'year'=>$residential_year, 'location_id'=>$location_id);
        }
        $rs = $database->query_result($strSQL,$params);

          //echo $database->odo_sql_debug($strSQL, $params).'<br>';

      }elseif ($v['ans_type'] == 'textarea') {
        $strSQL = "select id from $benchmark_table where survey_id=:survey_id and question_id={$v['question_no']} and year=:year and location_id=:location_id";
        $params = array('survey_id'=>$survey_id,'year'=>$residential_year,'location_id'=>$location_id);
        $bExisted = recordExisted($strSQL, $params);

        if(count($bExisted) > 0){
          $id = $bExisted['id'];
          $strSQL = "update $benchmark_table set textarea=:textarea where id=$id";
          $params = array('textarea'=> trim($_REQUEST[$field]));
        }else {
          $strSQL = "insert into $benchmark_table set survevy_id=:survevy_id, question_id={$v['question_no']},textarea=:textarea , location_id=:location_id";
          $params = array('survey_id'=>$survey_id, 'textarea' => trim($_REQUEST[$field]), 'location_id'=>$location_id );

        }
        $rs = $database->query_result($strSQL, $params);

      }



      elseif ($v['ans_type'] == 'checkbox' ) {
        $strSQL = "select id from $benchmark_table where survey_id =:survey_id and question_id={$v['question_no']} and location_id=:location_id ";
        $params = array('survey_id'=>$survey_id ,'location_id'=>$location_id);
        $bExisted = recordExisted($strSQL, $params);
        if(count($bExisted) > 0){
          $id = $bExisted['id'];
					$strSQL = "update  $benchmark_table set ##sSQL_SET##   where id=$id";
					$params = array();
        }else {
          $strSQL = "insert into $benchmark_table set survey_id=:survey_id , question_id={$v['question_no']} , ##sSQL_SET##, residential_id=:residential_id,year=:year , location_id=:location_id";
          $params = array('survey_id'=>$survey_id,'residential_id'=>$residential_id,'year'=>$residential_year,'location_id'=>$location_id);
        }

        $strSQL_set = '';

        foreach($v['ans'] as $kk =>$vv){
          $option_field = 'q_'.$v['question_no'].'_'.($kk+1).'_option1';
          $ans = IsExistedRequest($option_field);
          if ($ans  === false){
						$ans = 0;
					}
          $strSQL_set .= ($strSQL_set ? ',' : '') . " option_".($kk+1). " = :option".($kk+1) ;
          $params = array_merge($params,  array('option'.($kk+1) => $ans) 	);

          if (strtoupper($vv) =='OTHER'){
						$option_field = $field.'_'.($kk+1).'_other';
						$otherValue = trim($_REQUEST[$option_field]);

						// if other is checked, the user must input the detail/other field
						if (($ans ==1) && $otherValue ==''){
							$sError .= "Error: Question ".$v['question_no']." other field is empty.<Br>";
						}

						$strSQL_set .= ($strSQL_set ? ',' : '') ." textarea = :textarea " ;
						$params = array_merge($params,  array('textarea' => $otherValue) 	);
					}

        }

        $rs = $database->query_result( str_replace('##sSQL_SET##', $strSQL_set, $strSQL ) , $params);

        //echo $database->odo_sql_debug($strSQL, $params).'<br>';
      }elseif($v['ans_type'] == 'custom'){
        switch($v['ans']){
          case 'wellness_custom_2':
            $strSQL = checkbox($benchmark_table, $v['question_no'],$v['sub_question'],1,1,1);

            /*$strSQL = "insert into $benchmark_table set survey_id=:survey_id,question_id=$question_no";
            $params = array('survey_id'=>$survey_id);*/
            //echo $database->odo_sql_debug($strSQL, $params).'<br>';
            break;


        }
      }

  endforeach;

}


function checkbox($benchmark_table, $question_no, $ans_list, $NumOfOption=1,$apartment_capacity=1,$population_number=1){

global $survey_id;

$database = Database::instance();


foreach($ans_list as $ind => $v){

  $strSQL_set = '';
  $strSQL = "select id from $benchmark_table where survey_id=:survey_id ";
  $params = array('survey_id'=>$survey_id,'option'=>'selection'.($ind+1));
  $bExisted = recordExisted($strSQL, $params);
  if(count($bExisted) >0){
    $id = $bExisted['id'];
    $strSQL = "update $benchmark_table set ##sSQL_SET## where id=$id";
    $checkboxParams = array();
  }else {
    $strSQL = "insert into $benchmark_table set survey_id=:survey_id, question_id=$question_no, option_id=:option, ##sSQL_SET##";
    $checkboxParams = array('survey_id'=>$survey_id, 'option'=>'selection'.($ind+1));

  }

  for($x=1; $x<= $NumOfOption; $x++){
    $option_field = 'q_'.$question_no.'_'.($ind+1).'_option'.$x;
    $val = IsExistedRequest($option_field);
    if($val === false ){$val = 0;}
    $strSQL_set .= ($strSQL_set ? ',' : '') . " available".($x)."=:available".($x) ;
    $checkboxParams = array_merge($checkboxParams,  array('available'.($x) => $val) 	);
  }

  if($apartment_capacity){
    $field1 = 'q_'.$question_no.'_'.($ind+1).'_capacity';
    $val = IsExistedRequest($field1, 0);
    if ($val  === false){ $val = '';	}
    $strSQL_set .= ($strSQL_set ? ',' : '')."capacity=:capacity";

    $checkboxParams = array_merge($checkboxParams,  array('capacity' => $val) 	);
  }

  if($population_number){
    $field1 = 'q_'.$question_no.'_'.($ind+1).'_population';
    $val = IsExistedRequest($field1, 0);
    if ($val  === false){ $val = '';	}
    $strSQL_set .= ($strSQL_set ? ',' : '')."population=:population";
    $checkboxParams = array_merge($checkboxParams,  array('population' => $val) 	);
  }






  $rs = $database->query_result( str_replace('##sSQL_SET##', $strSQL_set, $strSQL ), $checkboxParams  );

}




}







function IsExistedRequest($field, $checkbox=1){
		global $_REQUEST;
		foreach($_REQUEST as $k => $v){
			//$field = 'q_'.$q;
			if ($k == $field  && trim($_REQUEST[$k]) ){
				return $checkbox == 1 ? 1 :  trim($_REQUEST[$k]);
			}
		}
		return false;
	}



function recordExisted($sSQL, $params){
		$database = Database::instance();
		$rs = $database->query_result($sSQL, $params);
		if (count($rs) >0){
			return $rs[0];
		}else return array();
	}











 ?>
