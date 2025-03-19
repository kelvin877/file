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

    <form id="frm" class="fullWidth flex auditFrm wrap"  action="<?php echo getEnv("SCRIPT_NAME")?>"  method="post" enctype="multipart/form-data">

      <div class="flex wrap"  >
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
    <?php
    $row_count = 0;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      unset($_SESSION['success']);
      unset($_SESSION['error_message']);

      if(isset($_POST['submit']) ) {
      //unset($_SESSION['success']);
      //unset($_SESSION['error_message']);

        if (isset($_FILES['csv_file'])){
            // Get file information
            $file_name = $_FILES['csv_file']['name'];
            //$file_name = "benchmark".date('Y_m_d_H_i_s').".csv";
            $file_tmp = $_FILES['csv_file']['tmp_name'];
            $file_type = $_FILES['csv_file']['type'];

            // Check if the uploaded file is a CSV file
            $allowed_types = ['text/csv', 'application/vnd.ms-excel']; // MIME types for CSV
            if (in_array($file_type, $allowed_types)) {
                //echo 'File is a valid CSV file.';
                $upload_dir = '/usr/local/www/groups/icaa.cc/tmp_upload/benchmark/';
                $destination = $upload_dir . basename($file_name);

                if (move_uploaded_file($file_tmp, $destination)) {
                  //echo '<p style="color:green">File are correct Formant </p>';
                  importFile($file_name);
                  //$_SESSION['success']='Success';
                  redir(getEnv("SCRIPT_NAME"));
              } else {
                $_SESSION['error_message']  = '<p style="color:red">Error: File upload failed.</p>';
              }

                // Process the file here (move or read it, etc.)
            } else {
                $_SESSION['error_message']  = '<p style="color:red">Error: Please upload a valid CSV file.</p>';
            }
        } else {
            $_SESSION['error_message']  = '<p style="color:red">Fail: No file uploaded.</p>';
        }



    }
  }
    //echo DOCUMENT_ROOT;


    function importFile($file_name)
    {
      global $move_out_reason_list;
      $need_move_out_reason='';

        $file_path = '/usr/local/www/groups/icaa.cc/tmp_upload/benchmark/' . $file_name;
        $date_format = 'd/m/Y';
        $all_rows_valid = true; // Flag to track overall row validity
        $valid_rows = []; // Store valid rows for insertion later

        if (($handle = fopen($file_path, "r")) !== false) {

            // Read the first row to get the column headers
            $headers = fgetcsv($handle);

            // Initialize row counter starting from 1 (for first data row)
            $row_number = 2;



            // Loop through each row in the CSV
            while (($row = fgetcsv($handle)) !== false) {

              // Check if the row has exactly 4 columns
            if (count($row) != 4) {
                $_SESSION['error_message'] = "<p style='color:red'>Error: Row $row_number must have exactly 4 columns (Resident, Move-in Date, Move-out Date, Move-out Reason).</p>";
                $all_rows_valid = false;
                $row_number++; // Increment the row counter and move to the next row
                continue; // Skip this row and go to the next
            }


                $row_valid = true; // Flag to track if the current row is valid

                // Process each column
                $move_in_date = null;
                $move_out_date = null;



                for ($i = 0; $i < count($row); $i++) {
                    // Check if this is the Move-in Date or Move-out Date column
                    //if($i == 0){
                      if(empty($row[0])){
                        $_SESSION['error_message'] .= "<p style='color:red'>Error: " . htmlspecialchars($headers[0]) . " cannot be empty in row $row_number.</p>";
                        $row_valid = false;
                        break;
                      }

                      if(empty($row[1])){
                        $_SESSION['error_message']  .= "<p style='color:red'>Error: " . htmlspecialchars($headers[1]) . " cannot be empty in row $row_number.</p>";
                        $row_valid = false;
                        break;
                      }

                      if(empty($row[2]) && !empty($row[3])){

                        $_SESSION['error_message']  .= "<p style='color:red'>Error: Move out reason cannot be import as move out date are empty in row $row_number.</p>";
                        $row_valid = false;
                        break;

                      }

                      if(!in_array(strtolower($row[3]), array_map('strtolower', $move_out_reason_list))) {
                        $_SESSION['error_message']  .= "<p style='color:red'>Error: The move out reason is not correct in row $row_number. Please follow guideline to insert move out reason</p>";
                        $row_valid = false;
                        break;
                      }

                        //check date format
                      if(!empty($row[1]) && !empty($row[2])){
                          $format = 'd/m/Y';

                          $date1 = DateTime::createFromFormat($format, $row[1]);
                          $date2 = DateTime::createFromFormat($format, $row[2]);

                          if (!$date1 || $date1->format($format) !== str_pad($row[1], 10, '0', STR_PAD_LEFT)) {
                                // If date1 is not in the correct format, throw an error
                             $_SESSION['error_message']  .= "<p style='color:red'>Error: ".htmlspecialchars($headers[1])." is not in the correct format (DD/MM/YYYY) in row $row_number.</p>";
                             // Optionally, you can stop further processing here or handle the error as needed.
                             $row_valid = false;
                             break;
                         }

                         // You can also check for date2 format if needed:
                         elseif (!$date2 || $date2->format($format) !== str_pad($row[2], 10, '0', STR_PAD_LEFT)) {
                             // If date2 is not in the correct format, throw an error
                             $_SESSION['error_message']  .= "<p style='color:red'>Error: ".htmlspecialchars($headers[2])." is not in the correct format (DD/MM/YYYY) in row $row_number.</p>";
                             $row_valid = false;
                             break;

                         }else {
                           $move_in_date = $date1;
                           $move_out_date = $date2;

                           continue;

                         }

                      }


                    //}
                }


                // Check if Move-out Date is later than Move-in Date
                if ($move_in_date && $move_out_date && $move_out_date <= $move_in_date) {
                    echo "<br>";
                    $_SESSION['error_message'] .= "<p style='color:red'>Error: Move-out date must be later than Move-in Date in row $row_number.</p>";
                    $row_valid = false; // Invalid row due to the date comparison
                }

                // If the row is valid, store it for insertion later
                if ($row_valid) {
                    $valid_rows[] = $row;
                } else {
                    $all_rows_valid = false;
                }

                $row_number++; // Increment the row counter for the next row
            }

            // Close the file
            fclose($handle);

            // If all rows are valid, proceed with insertion
            if ($all_rows_valid) {
                echo '<br>';
                $_SESSION['success'] .= "<p style='color:green'>All rows are valid.</p>";

                $location_id =$_SESSION['icaa_wellness_audit_login']['id'];
              $database = Database::instance();

              $sql = "SELECT * FROM benchmark_result2 WHERE location_id = :location_id AND question_id = '5' order by track_number desc limit 1";
              $params = array('location_id' => $location_id);
              $record = $database->query_result($sql, $params);


              if(count($record)>0){
              $track_number = $record[0]['track_number'] + 1;

              }else {
                  $track_number = $location_id.'1';
              }


              //echo $track_number;

                // Insert each valid row into the database (example)
                foreach ($valid_rows as $valid_row) {
                    // Example: insertRowIntoDatabase($valid_row);
                    //echo $track_number;
                    $resident = $valid_row[0]; // Resident column
                    $move_in_date = $valid_row[1]; // Move-in Date column
                    $formatted_move_in_date = date('Y-m-d', strtotime($move_in_date));

                    $move_out_date = $valid_row[2]; // Move-out Date column

                    if(empty($move_out_date)){
                      $formatted_move_out_date = '0000-00-00';
                      $is_active = 'Y';
                    }


                    else {
                      $is_active = 'N';
                      $formatted_move_out_date = date('Y-m-d', strtotime($move_out_date));
                    }


                    $move_out_reason = $valid_row[3];

                    $move_out_reason_key = array_search(strtolower($move_out_reason), array_map('strtolower', $move_out_reason_list));



                    $sql = "insert into benchmark_result2 set location_id=:location_id, question_id='5', move_in_date=:move_in_date,move_out_date=:move_out_date,track_number=:track_number, resident_name=:resident_name,move_out_reason=:move_out_reason,is_active=:is_active ";
                    $params = array('location_id'=>$location_id,'move_in_date'=>$formatted_move_in_date,'move_out_date'=>$formatted_move_out_date ,'resident_name'=>$resident,'track_number'=>$track_number,'move_out_reason'=>$move_out_reason_key,'is_active'=>$is_active);
                    $database->query_result($sql,$params);


                    //echo "Row inserted successfully:<br>";
                  foreach ($valid_row as $cell) {
                    //echo $move_in_date;

                      //echo $cell . "\t"; // Print the inserted row
                    }
                    //echo "<br>";
                    $track_number++;
                }
                 $_SESSION['success'] = "<p style='color:green'>All rows have been successfully inserted.</p><br>";
                //header("Location: form1.php");



            } /*else {
              $_SESSION['error_message'] .= "<p style='color:red'>Some rows had errors and were not inserted.</p>";
            }*/

        } else {
          $_SESSION['error_message'] .= "<p style='color:red'>Unable to open the file.</p>";
        }

    }










    ?>




    <h3 class="detail-title">Import residential data</h3>

      <?php

      if(isset($_SESSION['success'])){

        echo '<div class="fullWidth">'.$_SESSION['success'].'</div>';
        //unset($_SESSION['success']);
      }

      if(isset($_SESSION['error_message'])){
        echo '<div class="fullWidth">'.$_SESSION['error_message'].'</div>';

        //unset($_SESSION['error_message']);
      }


      //echo $_SESSION['success'];
      //echo $_SESSION['error_message'];
      //unset($_SESSION['success']);
      //unset($_SESSION['error_message']);

       ?>







        <div class="clear"></div>
        <div id="upload_section">

        <div class="fullWidth">Please <a style="color:blue;" href="/wellness_audit/benchmark/import_residential_example.csv" target="_blank">download</a> this sample file. (First row is the header)</div>
        <br>
        <br>
        <h3 style="font-size:1.1em; color:#a90000;padding-top:12px" > Move Out Reasons must be one of the following :</h3>
        <div>
        <ul>
        	<li>Improved health condition</li>
            <li>Family taking over</li>
            <li>Unsuitable services or environment</li>
            <li>Financial reasons</li>
            <li>Personal perference</li>
            <li>Transfer to a hospital or hospice</li>
            <li>Moving to a more suitable facility</li>
            <li>Passing away</li>
        </ul>
        </div>
        <div style="font-size:1.1em; color:#a90000" >If there are no move out date, leave it empty.</div>


        <div style="padding:20px 0">Move in or move out date formant:<span style="font-size:1.1em; color:#a90000" >
        DD/MM/YYYY (e.g. 31/01/2024)</span>
        </div>


<div>
Click "Choose File" below to find and upload the information (CSV file only).
</div>


					<label class="full">
						<INPUT TYPE="FILE" name="csv_file" id="csv_file" accept=".csv">
					</label>



      <label class="full buttons" style="display:block;">
        <input type="submit" value="Upload File" name="submit">
        <input type="submit" value="Back" name="submit" id="back_btn">
      </label>



</div>

</form>


 <!-- <div class="cWrap">
  <label class="fullWidth buttons">
  <button onclick="redirectToPage()">Back</button>
  </label>
  </div>
-->

  <script type="text/javascript">

$(document).ready(function(){

      $('#back_btn').click(function(e){

		e.preventDefault();
        redirectToPage();
      });
});


    function redirectToPage() {
      //sessionStorage.removeItem('error_message');
      //sessionStorage.removeItem('success');
      window.location.href = "form1_q5.php"; // Replace with your desired URL
    }

    </script>

</div>

</div>

</section>


<?php siteFooter(""); ?>
