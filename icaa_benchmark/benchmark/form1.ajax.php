<?php

require_once(getEnv("DOCUMENT_ROOT").'/include.php');

//$_SESSION['icaa_after_login_page'] = '/wellness_audit/benchmark/form.php';

$database = Database::instance();

include 'benchmark_form1_config.php';
include 'form1_function.php';


$sError = '';
$location_id = $_REQUEST["location_id"];
//$location_id =$_SESSION['icaa_wellness_audit_login']['id'];

if(isset($_REQUEST["action"]) && !empty($_REQUEST["action"]) && $_POST['action']!="add_note" && $_POST['action']!="move_in_note" && $_POST['action']!="delete" ){

  $action = $_REQUEST["action"];
  //$survey_id  = $_REQUEST["survey_id"];


  /*$sql = "select id from benchmark_residential_record where survey_id =:survey_id";
  $params = array('survey_id'=>$survey_id,'year'=>$residential_year);
  $bExisted = recordExisted($sql,$params);*/



  benchmark_submit($question_list);


  if(!empty($action)){
    $response = [
      'status'=>'success',
      'message'=>'Form submitted',
      'data'=>[
        //'id'=>$id,
        'location_id'=>$location_id,
        //'residential_id'=>$residential_id
      ]
    ];
  }

  echo json_encode($response);

}






if(isset($_POST['action']) && $_POST['action'] == 'add_note'){
      //$survey_id   = $_POST['survey_id'];
      //$location_id = $_POST['location_id'];
      $benchmark_table = $_POST['benchmark_table'];
      $question_id = $_POST['question_id'];
      $first_name = $_POST['first_name'];
      $title_name = $_POST['title_name'];

      $sql = "insert into $benchmark_table set location_id=:location_id, question_id=:question_id, first_name=:first_name,title_name=:title_name ";
      //$sql = "select * from $benchmark_table where survey_id=: survey_id ";
      $params = array('location_id'=>$location_id,'question_id'=>$question_id,'first_name'=>$first_name,'title_name'=>$title_name);

      $database->query_result($sql, $params);


      $response = [
        'status'=>'success',
        'message'=>'Form submitted',
        'data'=>[
          'location_id'=>$location_id,
          'sql'=>$sql
        ]
      ];


      echo json_encode($response);
}

if(isset($_POST['action']) && $_POST['action']=='move_in_note'){


  $sql = "select * from benchmark_result2 where location_id =:location_id and question_id ='5' order by track_number desc limit 1 ";
  $params = array('location_id'=>$location_id);
  $records = $database->query_result($sql,$params);

  //To get Next tracking Number
  if(count($records)>0){

    $track_number = $records[0]['track_number'] + 1;

    //$track_number = '100';

  }else {
    $track_number = $location_id.'1';

  }


    $move_out_reason = $_POST['move_out_reason'];
    $move_in_date  = $_POST['move_in_date'];
    $move_out_date = $_POST['move_out_date'];
    $resident_name  = $_POST['resident_name'];
    //$location_id = $_POST['location_id'];
    $benchmark_table = $_POST['benchmark_table'];
    $question_id = $_POST['question_id'];


    if($move_out_date == ''){
      $is_active = 'Y';
    }else {
      $is_active = 'N';
    }



    $sql = "insert into $benchmark_table set location_id=:location_id, question_id=:question_id, move_in_date=:move_in_date,move_out_date=:move_out_date,track_number=:track_number ,resident_name=:resident_name,is_active=:is_active,move_out_reason=:move_out_reason ";
    $params = array('location_id'=>$location_id , 'question_id'=>$question_id, 'move_in_date'=>$move_in_date,'move_out_date'=>$move_out_date ,'resident_name'=>$resident_name,'track_number'=>$track_number,'is_active'=>$is_active,'move_out_reason'=>$move_out_reason );
    $database->query_result($sql, $params);

    $response = [
      'status'=>'success',
      'message'=>'Form submitted',
      'data'=>[
        'location_id'=>$location_id,
      ]
    ];



    echo json_encode($response);

}

if(isset($_POST['action']) && $_POST['action']== 'edit_note' ){

  $id  = $_POST['id'];
  $benchmark_table = $_POST['benchmark_table'];
  $move_out_date  = $_POST['move_out_date'];
  $move_out_reason = $_POST['move_out_reason'];
  $move_in_date = $_POST['move_in_date'];
  $resident_name = $_POST['resident_name'];


  if($move_out_date == ''){
    $is_active = 'Y';
  }else {
    $is_active = 'N';
  }


  $sql = "Update $benchmark_table set resident_name=:resident_name,move_out_date =:move_out_date,move_in_date=:move_in_date ,move_out_reason=:move_out_reason ,is_active=:is_active where id=:id ";

  $params = array('id'=>$id,'resident_name'=>$resident_name,'move_in_date'=>$move_in_date,'move_out_date'=>$move_out_date,'is_active'=>$is_active,'move_out_reason'=>$move_out_reason );
  $database->query_result($sql,$params);

  $response = [
    'status'=>'success',
    'message'=>'Form submitted',
    'sql'=>$sql,

  ];

  echo json_encode($response);

}

if(isset($_POST['action']) && $_POST['action']== 'delete' ){
    $id  = $_POST['id'];
    $benchmark_table = $_POST['benchmark_table'];
    $question_id = $_POST['question_id'];


    $sql = "delete from $benchmark_table where id=:id ";
    $params = array('id'=>$id);
    $database->query_result($sql,$params);

    $response = [
      'status'=>'success',
      'message'=>'Form submitted',
      'sql'=>$sql,

    ];

    echo json_encode($response);

}






 ?>
