<?php


$question_no = 1;

$benchmark_result2 = 'benchmark_result2';
$benchmark_result4 = 'benchmark_result4';

$number = array('0-50','50-100','100-150','more');

$answer = array('Yes','No');



$question_list = array(

  array("question_no"=>$question_no++,
        "question" => 'For the location, enter total annual POPULATION (census) into respective levels of living below & distribute total units or CAPACITY into respective levels of living period below',
        "benchmark_table"=>$benchmark_result2,
        "ans_type"=>"custom",
        "ans"=>"wellness_custom_2",
        "sub_question_header"=>array('title'=>array('This is available','Total number of apartments/unit capacity','Number of residents/census annual population'),
                                      'ans_type'=>array('dropdown','dropdown','dropdown'),
                                      'dropdown_field'=>array('available1','capacity','population')
                                    ),
        "sub_question" => array(
          'Independent living',
          'Assisted living community',
          'Assisted living memory care',
          'Skilled nursing',
          'Seniors housing (homes,apartments, homes, apartments, condos)',
          'Other'
        ),
        "form"=>"form1"
      ),

  array("question_no"=>$question_no++,
        "question" => 'Do any of your independent living residents receive in-home personal care services for assistance with daily activities such as managing medications, dressing, bathing or getting from place to place?',
        "benchmark_table"=>$benchmark_result4,
        "ans_type"=>"radio",
        "ans"=>array('Yes','No'),
        "form"=>"form1"
),
  array("question_no"=>$question_no++,
        "question" => 'How many Full Time Equivalent (FTE) staff do you have dedicated to wellness programs? ',
        "benchmark_table"=>$benchmark_result4,
        "ans_type"=>"textarea",
        "ans"=> '',
        "form"=>"form1"
),



  array("question_no"=>$question_no++,
        "question"=> 'Please provide more detailed information about full-time employees working in the wellness center / department. ',
        "benchmark_table"=>$benchmark_result2,
        "ans_type"=>"custom",
        "ans"=>'fill_employee',
        "sub_question_header"=>array('title'=>array('First Name','Title','Action'),
                                      'ans_type'=>array('textarea'),
                                      'textarea_field'=>array('firstname','title')
                                    ),
        "sub_question" => array(
          'Employee1',
        ),

        "form"=>"form1"
      ),
  array("question_no"=>$question_no++,
        "question"=> 'Please provide more detailed information about the residents at this location. The first time you enter residents, please enter the move-dates. In later times, also note residents who have moved out and the reason why.',
        "benchmark_table"=>$benchmark_result2,
        "ans_type"=>"custom",
        "ans"=>"move_in_date",
        "sub_question_header"=>array('title'=>array('Resident name','Tracking number','Move-in date','Move-out date','Action',''),
                                     'ans_type'=>array('dropdown'),
                                     'dropdown_field'=>array('resident_name','track_number','move_in_date','move_out_date'),
        "sub_question"=>array(
          'Resident',
        )
      ),
      "form"=>"form1"

    ),



);

$move_out_reason_list = array(
  ''=>'',
  'improved_health_condition'=>'Improved Health Condition',
  'family_taking_over'=>'Family Taking Over',
  'unsuitable_service_or_environment'=> 'Unsuitable Services or Environment',
  'financial_reasons' => 'Financial Reasons',
  'personal_perference'=>'Personal Perference',
  'transfer_to_hospital'=>'Transfer to a Hospital or Hospice',
  'moving_to_more_suitable_facility'=> 'Moving to a More Suitable Facility',
  'passing_away'=>'Passing Away'


);






 ?>
