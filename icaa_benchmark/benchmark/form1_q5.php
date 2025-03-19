
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
$sError = '';
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
      <form class="auditFrm fullWidth" action="<?php echo getEnv("SCRIPT_NAME") ?>" method="post">

        <?php
          audit_benchmark_Navigation()

          ?>
        <input type="hidden" name="location_id" value="<?php echo $location_id; ?>">

        <?php foreach($question_list as $k => $v):
            $form=$v['form'];
            if($form == 'form1'){
              $question_no = $v['question_no'];
              if($question_no=='5'):



          ?>
          <dd class="flex-contanier">
            <?php
              echo '<div class="full withTable">Question '. $question_no. ': ' . $v['question'];
              $sql = "select * from ".$v['benchmark_table']." $where";
              $params = array_merge($params, array('question_id'=>$question_no));
              $aAudit_records = $database->query_result($sql,$params);
              $name= "q_".$question_no;
             ?>
             <?php if($v['ans'] == 'move_in_date' ):
               $header = $v['sub_question_header'];


               ?>
               <br>
               <br>
               <a class="btn btn-secondary"  href="https://www.icaa.cc/wellness_audit/benchmark/benchmark_upload.php">Import residential information</a>
              <br>
            <table class="formTable testing" cellpadding="0" cellspacing="0" border="0">
              <thead>
                <tr>

                  <?php
                  $sql = "select * from ".$v['benchmark_table']." where question_id =:question_id and location_id=:location_id";
                  $params = array('question_id' => $question_no, 'location_id'=>$location_id);



                  $records  = $database->query_result($sql, $params);
                  echo '<th class="empty"></th>';
                  if(count($records) > 0){
                  foreach($header['title'] as $h => $header_v){
                  echo '<th class="head">'.$header_v.'</th>';

                  }
                  ?>

                </tr>

              </thead>

            <?php
            $id_array[]='';
              foreach ($records as $key => $value) {
                  $resident_name=$value['resident_name'];
                  $move_in_date =$value['move_in_date'];
                  $move_out_date=$value['move_out_date'];
                  $track_number=$value['track_number'];
                  $move_out_reason  =$value['move_out_reason'];
                  $count = $key + 1;
                  $id=$value['id'];
                  $id_array[]=$value['id'];
                  if($move_out_date != '0000-00-00'){
                    $move_out_date = $move_out_date;
                  }else {
                    $move_out_date = 'N/A';
                  }
                  echo '<tr>
                          <th>Resident'.$count.'</th>
                          <th><input type="text" id="resident_name_'.$id.'" value='.$resident_name.'></th>
                          <th>'.$track_number.'</th>
                          <th><input id="move_in_date_'.$id.'" value='.$move_in_date.'  type="text" data-id="'.$id.'" data-table="'.$v['benchmark_table'].'" data-movein="'.$move_in_date.'"  data-question="'.$question_no.'"   style="border: 1px solid #000;" readonly/></th>
                          <th>
                          <input hidden id="move_in_date_'.$id.'" value='.$move_in_date.'  type="date" data-id="'.$id.'" data-table="'.$v['benchmark_table'].'" data-movein="'.$move_in_date.'"  data-question="'.$question_no.'"  style="border: 1px solid #000;"/>
                          Move-out date :
                          <input id="move_out_date_'.$id.'" value='.$move_out_date.'  type="text" data-id="'.$id.'" data-table="'.$v['benchmark_table'].'" data-moveout="'.$move_out_date.'"  data-question="'.$question_no.'"   style="border: 1px solid #000;" readonly/>
                          <br/>
                          Move out reasons:';
                        echo '<select id="move_out_reason_'.$id.'"  name="move_out_reason">';
                        foreach ($move_out_reason_list as $key => $reason){
                          //$selected = ($move_out_reason == $key) ? 'selected' : '';
                          if($move_out_reason == $key){
                            $selected = 'selected';
                          }else {
                            $selected ='';
                          }
                            echo '<option value="'.$key.'" '.$selected.'>'.$reason.'</option>';
                        }
                       echo '</select>';
                       echo '<br/>

                          </th>

                          <th>
                          <button type="submit" name="save_submit" data-id='.$id.' data-table='.$v['benchmark_table'].'  class="submit_button btn btn-primary save_submit">Save</button>

                          </th>

                          <th>
                          <div class="button-container">
                            <button class="delete_button btn btn-secondary" data-id="'.$id.'" data-table="'.$v['benchmark_table'].'" data-question="'.$question_no.'">Delete</button>
                          </div>
                        </th>
                        </tr>
                        ';
              }
          }
             ?>

            </table>
            <button alt="add" type ="button" class="showPanel btn btn-secondary" id="resideInfo" data-id="<?php echo $location_id; ?>">Add residential information</button>
            <div class="popPanel popForm" id="resideInfoPanel">
            	<h3>Add residential information<span>&times;</span></h3>
                <div class="formCont">
                    <label>
                        Resident name
                        <input type="text" id="resident_name" name="resident_name" value="" style="border: 1px solid #000;" autocomplete="off" >
                    </label>
                    <label class="withPicker">
                        Move in date
                        <input id="move_in_date" class="move_in_date" type="text"  style="border: 1px solid #000;"  readonly/>
                    </label>
                    <label class="withPicker">
                        Move out date
                        <input id="move_out_date" class="move_out_date" type="text"  style="border: 1px solid #000;" readonly/>
                    </label>
                    <label class="withPicker">
                        Move out reasons
                        <select id="move_out_reason" class="move_out_reason" >
                          <?php foreach ($move_out_reason_list as $key => $value): ?>
                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                          <?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="btns">
                	<button type="button" class="btn btn-secondary close" data-dismiss="modal">Cancel</button>
                    <button type="submit"  name="move_in_submit" id="move_in_submit"  data-id="<?php echo $location_id; ?>" data-table="<?php echo $v['benchmark_table']; ?>" data-question="<?php echo $question_no; ?>"   class="btn btn-primary move_in_submit">submit</button>
                </div>
            </div>





             <?php endif; ?>

          <?php endif; ?>

          </dd>

        <?php } ?>


        <?php endforeach; ?>

        <label class="full buttons" style="display:block;">
          <input type="submit" value="Back" name="submit" id="back_btn">

        </label>
      </form>
    </div>
  </div>

</section>

<?php siteFooter(""); ?>

<script type="text/javascript">
$(document).ready(function(){

  $('#back_btn').click(function(e){

e.preventDefault();
    redirectToPage();
  });


  $('.save_submit').click(function(e) {
    e.preventDefault();
    //alert('hello');
    var id = $(this).data('id');
    var resident_name = $('#resident_name_' + id).val();
    var move_in_date = $('#move_in_date_' + id).val();
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
    if(moveOutDateObj>moveInDateObj && resident_name!=''){
    $.ajax(
      {
        type:"POST",
        url:"form1.ajax.php",
        data:{action: 'edit_note',resident_name:resident_name,move_in_date:move_in_date ,move_out_date:move_out_date,benchmark_table:benchmark_table,id:id,move_out_reason:move_out_reason },
        success: function(response){
          blue_message("Resident information has changed.");
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
  }else if (resident_name=='') {
    alert("Resident name cannot be empty");
  }


  else {
     alert("Move-out date cannot be before move-in date.");
  }
    //alert(move_out_date);
    });





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


function redirectToPage() {
  //sessionStorage.removeItem('error_message');
  //sessionStorage.removeItem('success');
  window.location.href = "form1.php"; // Replace with your desired URL
}


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
            dateFormat: 'yy-mm-dd',
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

    $.each(id_array, function(index, id) {
        $("#move_in_date_" + id).datepicker({
            numberOfMonths: 2,
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear:true,
            showButtonPanel: true,
            onClose: function() {
                if ($(this).val() === '') {
                    $(this).datepicker('setDate', null); // Allow empty value
                }
            },
            /*beforeShow: function(input, inst) {
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
            }*/
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
