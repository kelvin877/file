
<style media="screen">
.modal {
  display: none;
  position: fixed;
  z-index: 1050;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
}
.modal-dialog {
  position: relative;
  width: auto;
  margin: 10px;
}
.modal-content {
  background-color: #fff;
  border: 1px solid #dee2e6;
  border-radius: 0.3rem;
  outline: 0;
}
.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}
.modal-footer {
  padding: 1rem;
  border-top: 1px solid #dee2e6;
  text-align: right;
}
.close {
  font-size: 1.5rem;
  font-weight: 700;
  color: #000;
}
.button-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}
.full {
    display: block;
    margin-bottom: 15px;
}
.label-text {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333; /* Adjust color as needed */
}
textarea {
  border: 1px solid black;
}
select {
      border: 2px solid #000; /* You can change the color and thickness */
      padding: 5px; /* Optional, to add some space inside */
      border-radius: 4px; /* Optional, for rounded corners */
    }
    .button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        text-align: center;
        background-color: grey; /* Green background */
        color: white; /* White text */
        border: none;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
      }
      .button:hover {
        background-color: grey; /* Darker green on hover */
      }
</style>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<?php
require_once(getEnv("DOCUMENT_ROOT").'/include.php');
include 'benchmark_form1_config.php';
include '../../php-lib/wellness_audit_functions.php';
$database = Database::instance();
$form_m_complete = false;
$_SESSION['icaa_after_login_page'] = getEnv("SCRIPT_NAME");
bAllowMemberAccess();
$location_id =$_SESSION['icaa_wellness_audit_login']['id'];
$strSQL = "select * from audit_result1 where location_id=:location_id ";
$params = array('location_id'=>$location_id);
$record = $database->query_result($strSQL,$params);
if(count($record) > 1){
  $form_m_complete = true;
}
if(!isWellnessAuditLogin())
    header("Location: ../login.php");
if ($_SESSION['icaa_wellness_audit_login']['is_location_manager'] =='Y' || !($form_m_complete)){
  header("Location: ../main.php");
}
//$broadcast_idmd5 = isset($_REQUEST['n']) && $_REQUEST['n'] ? trim($_REQUEST['n']) : '';
//$id = 8;
$sError = '';
//$survey_id = $id;
$gender = 'male';
$marital = 'single';
$date_of_birth = '1996-01-02';
include('../../lib.php');
siteHeader(array("page"=>"facility"));
$banners=array(
        array(
                'src'=>'/images/pageBanners/icaaaudit.jpg',
        ),
);
generate_top_banner($banners);
pageFunctions();
//$question_no = 1;
$dData = $_REQUEST;
$database = Database::instance();
$where = " where location_id=:location_id and question_id=:question_id  ";
$params = array('location_id' => $location_id );
$auditAccount = getWellnessAuditAccount($_SESSION['icaa_wellness_audit_login']['id']);
 ?>
 <script type="text/javascript" src="/js/jquery.blockUI.js"></script>
 <script type="text/javascript" src="/js/custom_blockUI.js?v1"></script>
<section class="fullWidth" id="main-cont">
  <div class="cWrap">
    <h2 class="fullWidth blockTitle"><span>RIO TOOL</span></h2>
    <div id="real-cont">
  <form class="auditFrm fullWidth" action="<?php echo getEnv("SCRIPT_NAME")?>"   method="post">
    <?php
    audit_benchmark_Navigation()

     ?>


    <!--<div class="fullWidth auditBtns">
      <a href="../main1.php">Dashboard</a>
      <?php if (! ($_SESSION['icaa_wellness_audit_login']['type_of_account']=='corporate' && $_SESSION['icaa_wellness_audit_login']['from_corporate_signup']=='Y' && $_SESSION['icaa_wellness_audit_login']['is_location_manager'] =='Y')){?>
    <a href="../location_profile.php">Profile</a>
    <?php } ?>
    <?php if ($_SESSION['icaa_wellness_audit_login']['is_location_manager'] =='N'){ ?>
    <a href="../form_m.php">Audit form</a>
    <?php } ?>
    <a href="../report.php">Audit report</a>
    <a href="../logout.php">Logout</a>
  </div>-->
    <input type="hidden" name="location_id" value="<?php echo $location_id; ?>">
    <div class="clear"></div>
    <?php
    unset($_SESSION['success']);
    unset($_SESSION['error_message']);
    foreach($question_list as $k => $v):
      $form=$v['form'];
      if($form == 'form1'){
        $question_no = $v['question_no'];
     ?>
     <dd class="flex-container">
       <?php
        echo '<div class="full withTable">Question '. $question_no. ': ' . $v['question'];
        $sql = "select * from ".$v['benchmark_table']." $where";
        $params = array_merge($params, array('question_id'=>$question_no));
        $aAudit_records = $database->query_result($sql,$params);
        $name= "q_".$question_no;
        ?>
        <?php if($v['ans_type']== 'radio'){
          foreach($v['ans'] as $kk => $ans){
            if(count($aAudit_records) > 0){
              if($aAudit_records[0]['option'] == $ans){
                $dData[$name] = $ans;
              }
            }
            $bSelected = $dData[$name] ==$ans ?  'checked' : '';
          ?>
          <div class='full radioCheck' >
            <input type='radio' required name='<?php echo $name?>' value='<?php echo htmlspecialchars($ans, ENT_QUOTES )?>' id="<?php echo 'q_'.$question_no.'_'.($kk+1)?>" data-anstype="radio" <?php echo $bSelected?>><?php echo $ans?>
          </div>
          <?php
        }
       } ?>
       <?php if($v['ans_type'] == 'textarea'){
              if(count($aAudit_records) > 0 && $aAudit_records[0]['textarea'] ){
                $dData[$name] = $aAudit_records[0]['textarea'];
              }
          ?>
          <br>
          <label class="full">
            <textarea name="<?php echo $name?>" id="<?php echo $name.'_other'?>"  style="border: 1px solid black;width: 100%; height: 150px"><?php echo $dData[$name]?></textarea>
        </label>
      <?php } ?>
        <?php
        if($v['ans_type'] =='custom'){
                $header = $v['sub_question_header'];
                if($v['ans'] == 'wellness_custom_2'): ?>
        <table class="formTable testing" cellpadding="0" cellspacing="0" border="0">
                          <tr>
                              <th class="empty"></th>
  <?php
            foreach($header['title'] as $h => $header_v){
                echo '<th class="head">'.$header_v.'</th>';
            }
  ?>
                              <!--<th class="head">Yes, this is done</th>-->
                          </tr>
    <?php
        //foreach(${$v['ans']} as $kkk => $opt){
      foreach($v['sub_question'] as $kkk => $opt){
              $chk_name = $name .'_'.($kkk+1);
              if (count($aAudit_records) >0 ){
                foreach($aAudit_records as $record){
                  if ($record['option_id'] == 'selection'.($kkk+1)){
                    $capacity = $record['capacity'];
                    $population = $record['population'];
                    //echo $overall_record;
                  }
                  if($record['available1']==1){
                      $dData[$chk_name.'option1']=$kkk+1;
                  }
                }
              }
              $bSelected = $dData[$chk_name.'_option1']==($kkk+1) ?  'checked' : '';
?>
        <?php
        if ($v['ans'] == 'wellness_custom_2'){
        $dropdown_var = $number;
        $dropdown_answer = $answer;
        }elseif ($v['ans'] == 'wellness_custom_4'){
        $dropdown_var = $number;
        }else {
        $dropdown_var = $low_medium_high;
        }
        switch ($v['ans']):
        // one checkbox and one overall dropdown
        case 'wellness_custom_2':
        ?>
                          <tr>
                              <th><?php echo $opt?></th>
          <?php /* ?>  <td class="checkbox_td"><input type="checkbox" name='<?php echo $chk_name.'_option1'?>' value='<?php echo $kkk+1?>' <?php echo $bSelected?> id="<?php echo $chk_name.'_option1'?>" data-anstype="checkbox" class="checkbox_input"/></td><?php */ ?>
          <td>
              <select name="<?php echo $chk_name.'_available1' ?>" id="<?php echo $chk_name.'_available1'?>" data-anstype="dropdown">
              <?php foreach($dropdown_answer as $o){
                  // check the db record
                  if (isset($capacity)){
                    if ($capacity == $o){
                      $dData[$chk_name.'_available1'] = $o;
                    }
                  }
                  $bSelected = $dData[$chk_name.'_available1'] ==$o ?  'selected' : '';
                ?>
                <option value="<?php echo $o?>"  <?php echo $bSelected?>><?php echo $o?></option>
                                    <?php }?>
              </select>
            </td>
            <td>
                <select name="<?php echo $chk_name.'_capacity' ?>" id="<?php echo $chk_name.'_capacity'?>" data-anstype="dropdown">
                                      <option></option>
                <?php foreach($dropdown_var as $o){
                    // check the db record
                    if (isset($capacity)){
                      if ($capacity == $o){
                        $dData[$chk_name.'_capacity'] = $o;
                      }
                    }
                    $bSelected = $dData[$chk_name.'_capacity'] ==$o ?  'selected' : '';
                  ?>
                  <option value="<?php echo $o?>"  <?php echo $bSelected?>><?php echo $o?></option>
                                      <?php }?>
                </select>
              </td>
              <td>
                <select name="<?php echo $chk_name.'_population' ?>" id="<?php echo $chk_name.'_population'?>" data-anstype="dropdown">
                                      <option></option>
                <?php foreach($dropdown_var as $o){
                    // check the db record
                    if (isset($population)){
                      if ($population == $o){
                        $dData[$chk_name.'_population'] = $o;
                      }
                    }
                    $bSelected = $dData[$chk_name.'_population'] ==$o ?  'selected' : '';
                  ?>
                  <option value="<?php echo $o?>"  <?php echo $bSelected?>><?php echo $o?></option>
                                      <?php }?>
                </select>
              </td>
              </tr>
            <?php
            break;
            ?>
          <?php
        endswitch; ?>
         <?php
        }
          ?>
          </table>
          <?php
          endif;
        }
       ?>
      <?php if($v['ans'] == 'fill_employee'): ?>
        <table class="formTable testing" cellpadding="0" cellspacing="0" border="0">
          <thead>
            <tr>
                <!--<th class="empty"></th>-->
                <?php
                $sql = "select * from ".$v['benchmark_table']." where question_id =:question_id and location_id=:location_id ";
                $params = array('question_id' => $question_no,'location_id' => $location_id);
                $records  = $database->query_result($sql, $params);
                if(count($records) > 0){
                  echo '<th class="empty"></th>';
                foreach($header['title'] as $h => $header_v){
                echo '<th class="head">'.$header_v.'</th>';
                }
                ?>
                <!--<th class="head">Yes, this is done</th>-->
            </tr>
        </thead>
          <?php
            foreach ($records as $key => $value) {
                $firstname=$value['first_name'];
                $title_name =$value['title_name'];
                $count = $key + 1;
                $id =$value['id'];
                echo '<tr>
                        <th>Employee'.$count.'</th>
                        <th>'.$firstname.'</th>
                        <th>'.$title_name.'</th>
                        <th>
                        <div class="button-container">
                          <button type="submit" class="delete_button btn btn-secondary" data-id="'.$id.'" data-table="'.$v['benchmark_table'].'" data-question="'.$question_no.'">Delete</button>
                        </div>
                      </th>
                      </tr>
                      ';
            }
        }
           ?>
      </table>
        <button alt="add" type ="button" class="add_button btn btn-secondary" id="add-item" data-id="<?php echo $location_id; ?>">Add employee name</button>
        <div class="modal fade" id="add_note" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="myModalLabel">Add Employee</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter First Name">
                </div>
                <div class="form-group">
                    <label for="title_name" class="form-label">Title:</label>
                    <input type="text" id="title_name" name="title_name" class="form-control" placeholder="Enter Title">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="add_note_submit" id="add_note_submit" data-id="<?php echo $location_id; ?>" data-table="<?php echo $v['benchmark_table']; ?>" data-question="<?php echo $question_no; ?>" class="btn btn-primary add_note_submit">Submit</button>
            </div>
        </div>
    </div>
</div>
      <?php endif; ?>
      <?php if($v['ans'] == 'move_in_date' ): ?>
        <br>
        <!-- Form for uploading the CSV file -->
        <br>

        <a class="btn btn-secondary" href="form1_q5.php">Click here display more details</a>




      <?php endif; ?>
     <?php
      }
    endforeach;
      ?>
    </dd>
      <label class="fullWidth buttons">
				<input type="submit" name="Action" class="submit_form" value="Submit">
			</label>
    </form>
  </div>
</div>
</section>
<?php siteFooter(""); ?>
 <script type="text/javascript">
 $(document).ready(function(){
     // When the submit button is clicked
     $('#move_in_submit').on('click', function(e) {
         // Prevent the form submission (if it is inside a form)
         e.preventDefault();
         var resident_name = $("#resident_name").val();
         var move_in_date = $("#move_in_date").val();
         var move_out_date = $("#move_out_date").val();
         var move_out_reason = $("#move_out_reason").val();
         var location_id = $(this).data('id');



         var benchmark_table = $(this).data('table');
         var question_id = $(this).data('question');
         if(move_out_date != ''){
           var moveOutDateObj = new Date(move_out_date);
         }else {
           var moveInDateObj = new Date(move_in_date);
           moveInDateObj.setDate(moveInDateObj.getDate() + 1); // Adds 1 day to move_in_date
           var moveOutDateObj = moveInDateObj;
         }
         var moveInDateObj = new Date(move_in_date);
         if(moveOutDateObj>moveInDateObj){
           if(resident_name == '' || move_in_date == '' ){
             alert('please fill in resident name and move in date');
           }else {
             $.ajax({
               type: "POST",
               url: "form1.ajax.php",
               data:{action: 'move_in_note',move_in_date:move_in_date,move_out_date:move_out_date ,resident_name:resident_name ,location_id:location_id, benchmark_table:benchmark_table, question_id:question_id,move_out_reason:move_out_reason },
               success: function(response){
                 response=JSON.parse(response);
                 //	alert(response['comment']);
                 location.reload(true);
               }
             });
           }
       }else {
         alert("Move-out date cannot be before move-in date.");
       }
     });
 });





 $('.collapseDL dt').click(function(){
   $(this).toggleClass('open');
 })
 $('.collapseDL dd figure').click(function(){
   var targ=$(this).parent().prev('dt');
   targ.removeClass('open');
   console.log(targ.position().top);
   $("html, body").animate({scrollTop:targ.position().top},20);
 })
 $(function() {
    var today = new Date();
    var fiveYearsAgo = new Date();
    fiveYearsAgo.setFullYear(today.getFullYear() - 5);
    var threeYearsFromNow = new Date();
    threeYearsFromNow.setFullYear(today.getFullYear() + 3);

    $("#move_in_date").datepicker({
          numberOfMonths: 2,
          dateFormat: 'yy/mm/dd',
          changeMonth: true,
          changeYear: true,
          showButtonPanel: true,
          minDate: fiveYearsAgo, // Allow selecting dates from 5 years ago
          maxDate: threeYearsFromNow, // Allow selecting dates up to 3 years in the future
          onClose: function() {
              if ($(this).val() === '') {
                  $(this).datepicker('setDate', null); // Allow empty value
              }
          },
          beforeShow: function(input, inst) {
              // Add "Clear" button to the button panel
              var clearButton = $('<button type="button" class="ui-datepicker-clear">Clear</button>');
              clearButton.on('click', function() {
                  $(input).datepicker('setDate', null); // Clear the date
                  $(input).focus(); // Refocus to input field after clearing
              });
              // Insert the "Clear" button after the calendar is shown
              setTimeout(function() {
                  $(inst.dpDiv).find('.ui-datepicker-buttonpane').append(clearButton);
              }, 1);
          }
      });
  });

  $(function() {
      var today = new Date();
      var fiveYearsAgo = new Date();
      fiveYearsAgo.setFullYear(today.getFullYear() - 5);
      var threeYearsFromNow = new Date();
      threeYearsFromNow.setFullYear(today.getFullYear() + 3);

      $("#move_out_date").datepicker({
          numberOfMonths: 2,
          dateFormat: 'yy/mm/dd',
          changeMonth: true,
          changeYear: true,
          showButtonPanel: true,
          minDate: fiveYearsAgo, // Allow selecting dates from 5 years ago
          maxDate: threeYearsFromNow, // Allow selecting dates up to 3 years in the future
          onClose: function() {
              if ($(this).val() === '') {
                  $(this).datepicker('setDate', null); // Allow empty value
              }
          },
          beforeShow: function(input, inst) {
              // Add "Clear" button to the button panel
              var clearButton = $('<button type="button" class="ui-datepicker-clear">Clear</button>');
              clearButton.on('click', function() {
                  $(input).datepicker('setDate', null); // Clear the date
                  $(input).focus(); // Refocus to input field after clearing
              });
              // Insert the "Clear" button after the calendar is shown
              setTimeout(function() {
                  $(inst.dpDiv).find('.ui-datepicker-buttonpane').append(clearButton);
              }, 1);
          }
      });
  });

 $(function() {
     // Pass the PHP array to JavaScript
     var id_array = <?php echo json_encode($id_array); ?>;
     // Loop through each id in the id_array
     $.each(id_array, function(index, id) {
         $("#move_out_date_" + id).datepicker({
             numberOfMonths: 2,
             dateFormat: 'yy/mm/dd',
             changeMonth: true,
             changeYear:true,
             showButtonPanel: true,
             onClose: function() {
                 if ($(this).val() === '') {
                     $(this).datepicker('setDate', null); // Allow empty value
                 }
             },
             beforeShow: function(input, inst) {
                 // Add "Clear" button to the button panel
                 var clearButton = $('<button type="button" class="ui-datepicker-clear">Clear</button>');
                 clearButton.on('click', function() {
                     $(input).datepicker('setDate', null); // Clear the date
                     $(input).focus(); // Refocus to input field after clearing
                 });
                 // Insert the "Clear" button after the calendar is shown
                 setTimeout(function() {
                     $(inst.dpDiv).find('.ui-datepicker-buttonpane').append(clearButton);
                 }, 1);
             }
         });
     });
 });
 $(document).ready(function(){
	$('.showPanel').click(function(){
		var temp=$(this).attr('id');
		$('body').addClass('mask');
		$('#'+temp+'Panel').addClass('open');
		return false;
	})

	$('.popPanel h3 span,.btn.close').click(function(){
		$('body').removeClass('mask');
		$(this).parent().parent().removeClass('open');
	})

$('.delete_button').click(function(e) {
  e.preventDefault();
  //alert('hello');
  var id = $(this).data('id');
  var benchmark_table = $(this).data('table');
  var question_id = $(this).data('question');
  var confirmation = confirm("Are you sure you want to delete this item?");
  if (confirmation) {
    $.ajax({
      type: "POST",
      url: "form1.ajax.php",
      data:{action: 'delete', id:id,benchmark_table:benchmark_table,question_id:question_id  },
      success: function(response){
        blue_message("Deleting....");
        response=JSON.parse(response);
        //	alert(response['comment']);
            setTimeout(function() {
            location.reload(true);
        }, 2000);
      }
    });
  }
});
   var add_note = new bootstrap.Modal(document.getElementById('add_note'), {});
   $(document).on('click', '.add_button', function() {
     add_note.show();
   });
   $('.add_note_submit').click(function(e) {
     e.preventDefault();
     var first_name = $(".modal-body #first_name").val();
     var title_name = $(".modal-body #title_name").val();
     var location_id = $(this).data('id');
     var benchmark_table = $(this).data('table');
     var question_id = $(this).data('question');
     if(first_name == '' || title_name == ''){
       alert('please fill in all fields');
     }else {
       $.ajax({
         type: "POST",
         url: "form1.ajax.php",
         data:{action: 'add_note', first_name: first_name, title_name: title_name, location_id:location_id, benchmark_table:benchmark_table, question_id:question_id },
         success: function(response){
           response=JSON.parse(response);
           //	alert(response['comment']);
           location.reload(true);
         }
       });
     }
   });
   var add_note_movein = new bootstrap.Modal(document.getElementById('add_note_movein'), {});
   $(document).on('click', '.add_button_move_in', function() {
     add_note_movein.show();
   });





$('.save_submit').click(function(e) {
  e.preventDefault();
  alert('hello');
  var id = $(this).data('id');
  var move_out_date = $('#move_out_date_' + id).val();
  var benchmark_table = $(this).data('table');
  var move_in_date = $('#move_in_date_' + id).val();
  var move_out_reason = $('#move_out_reason_'+id).val();
  if(move_out_date != ''){
    var moveOutDateObj = new Date(move_out_date);
  }else {
    var moveInDateObj = new Date(move_in_date);
    moveInDateObj.setDate(moveInDateObj.getDate() + 1); // Adds 1 day to move_in_date
    var moveOutDateObj = moveInDateObj;
  }
  //alert(moveOutDateObj);
  var moveInDateObj = new Date(move_in_date);
  if(moveOutDateObj>moveInDateObj){
  $.ajax(
    {
      type:"POST",
      url:"form1.ajax.php",
      data:{action: 'move_out_note', move_out_date:move_out_date,benchmark_table:benchmark_table,id:id,move_out_reason:move_out_reason },
      success: function(response){
        blue_message("Move out date has changed.");
        //response=JSON.parse(response);
        //	alert(response['comment']);
        /*if (response.status == 'success') {
        //blue_message("Thank you for your submission.");
        console.log('Form submitted successfully');
        //location.reload(true);
    }*/
      }
    }
  );
}else {
   alert("Move-out date cannot be before move-in date.");
}
  //alert(move_out_date);
  });



   $('.submit_form').on('click', function (e) {
 		e.preventDefault();
 		console.log("this button name is " + $(this).val());
 		autoSubmit($(this));
 	});
 });
 function autoSubmit(thisbutton){
 	var allFieldsFilled = true;
 	var errorMessage = '';
     $('.auditFrm input.required').each(function() {
         if ($(this).val() === '') {
             allFieldsFilled = false;
 						errorMessage = "Please fill out all required fields.";
             return false; // exit the loop early
         }
     });
 		if (allFieldsFilled) {
         var radioGroups = {};
         $('.auditFrm input[type=radio][required]').each(function() {
             var name = $(this).attr('name');
             if (!radioGroups[name]) {
                 radioGroups[name] = false;
             }
             if ($(this).is(':checked')) {
                 radioGroups[name] = true;
             }
         });
         /*$.each(radioGroups, function(groupName, isSelected) {
             if (!isSelected) {
                 allFieldsFilled = false;
                 errorMessage = "Please select an option for all required questions.";
                 return false; // exit the loop early
             }
         });*/
     }
     if (!allFieldsFilled) {
         blue_message(errorMessage);
         return; // Exit function if not all fields are filled
     }
 		var oo = $('.auditFrm').serialize()  + '&action=submit_form';
 	  $.post("form1.ajax.php?v1", oo, function(data){
 	    data = JSON.parse(data);
 	    if(data['status'] == 'success'){
 	      if(thisbutton.val()=='Submit'){
 	        blue_message("Thank you for your submission.");
 	      }else {
 						blue_message(data['message']);
 					}
 	    }
 			else {
 					blue_message(data['message']);
 					if (data['page'] == 'member_login'){
 						window.location.href = "/login.php?id=audit";
 					}
 					if (data['page'] == 'audit_login'){
 						window.location.href = "login.php";
 					}
 				}
 	  });
 }
 </script>
