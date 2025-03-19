

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
<script>  
  $( function() {
    $( "#datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  } );
  
   var add_note_movein = new bootstrap.Modal(document.getElementById('add_note_movein'), {});
  </script>
  
  
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>Date: <input type="text" id="datepicker" style="border: 1px solid #000; height:100px; width:100px;"></p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
  <script>
$(document).ready(function(){
   $(document).on('click', '#openmodal', function() {
       console.log("clicked");
     add_note_movein.show();
   });
});
</script>
<?php siteFooter(""); ?>

