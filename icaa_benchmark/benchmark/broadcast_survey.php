<?php
require_once(getEnv("DOCUMENT_ROOT").'/include.php');

include "../php-lib/wellness_audit_config.php";
//include '../../php-lib/wellness_audit_functions.php';
include '../../php-lib/brodcast_residential_function.php';


$_SESSION['icaa_after_login_page'] = getEnv("SCRIPT_NAME");
bAllowMemberAccess();   // in php-lib/wellness_audit_functions.php

// must be ICAA PAID member in order to sign up the audit account
/*if (isMemberLogin()  && $_SESSION['icaa_login']["member_type"]	== 'Paid'){

}else 	header("Location: /login.php?id=audit");
*/

if (!isWellnessAuditLogin())
	header("Location: ../login.php");
// only corporate and location manager can view this form

/*if ($_SESSION['icaa_wellness_audit_login']['from_corporate_signup']=='Y' &&  $_SESSION['icaa_wellness_audit_login']['is_location_manager'] =='Y'){

}else header("Location: main.php");*/

//Location manager cant view this
if ($_SESSION['icaa_wellness_audit_login']['is_location_manager'] =='Y'){
	header("Location: ../main.php");
}



include('../../lib.php');
//print_r($_SESSION);
siteHeader(array("page"=>"facility"));
	//get_banner_by_template('conferenceandevents');
	$banners=array(
        array(
                'src'=>'/images/pageBanners/icaaaudit.jpg',
        ),
);
	generate_top_banner($banners);
	pageFunctions();
	//$question_no = 1;
	$dData = $_REQUEST;

$location_id = $_SESSION['icaa_wellness_audit_login']['id'];
$lngFileSize=10000000;
$strIncludes=".csv";
$strUploadFolder = "/usr/local/www/groups/icaa.cc/tmp_upload";
$TABLE = 'audit_broadcast_location';
$emailList = array();
	//$database = Database::instance();

	/*if (strtolower($_REQUEST['action']) == 'upload' ){
		$csv_to_read="audit_".date('Y_m_d_H_i_s').".csv";
		uploadFile($csv_to_read);
		$emailList = readUploadedFile($csv_to_read);

      //  $sError=ImportFile($csv_to_read);
	}



  function uploadFile($csv_to_read)
  {
	global $_FILES, $lngFileSize, $strExcludes, $strIncludes,$strUploadFolder;

	$strMessage = '';
	if (is_uploaded_file($_FILES['file1']['tmp_name'])){
		$bolUpload=true;
		if ($_FILES['file1']['size'] > $lngFileSize){
			$bolUpload=false;
            $strMessage="File too large";
		}

		if ($strExcludes!=""){
			if (ValidFileExtension($_FILES['file1']['name'],$strExcludes)){
				 $strMessage="It is not allowed to upload a file containing a [.".GetFileExtension($_FILES['file1']['name'])."] extension";
				$bolUpload=false;
			}
		}

		if ($strIncludes!=""){
			if (InValidFileExtension($_FILES['file1']['name'],$strIncludes)){
				 $strMessage="It is not allowed to upload a file containing a [.".GetFileExtension($_FILES['file1']['name'])."] extension";
				 $bolUpload=false;
			}
		}

		if ($bolUpload == true){
			$bOK = copy($_FILES['file1']['tmp_name'],  $strUploadFolder.'/'.$csv_to_read);
		}

	}else {
		$strMessage="No file entered.";
	}
    echo  $strMessage."<br>";

  }
function readUploadedFile($csv_to_read) {

	 global $TABLE, $strUploadFolder;
	$ind = 0;
	$emailList = array();
	$file_to_read = fopen($strUploadFolder.'/'.$csv_to_read, 'r');
	if($file_to_read !== FALSE){
		while(($data = fgetcsv($file_to_read)) !== FALSE){
			$emailList[$ind] = array('email_address'=>trim($data[0]), 'first_name'=>ucwords(trim($data[1])));
			$ind++;
		}
		 fclose($file_to_read);
		$_SESSION['audit_email'] = $emailList;
	}

  //  $sMsg=$success." record(s) are inserted.<br>";
	return $emailList;
}

//--------------------------------------------
// ValidFileExtension()
// You give a list of file extensions that are allowed to be uploaded.
// Purpose:  Checks if the file extension is allowed
// Inputs:   strFileName -- the filename
//           strFileExtension -- the fileextensions not allowed
// Returns:  boolean
// Gives False if the file extension is NOT allowed
//--------------------------------------------
  function ValidFileExtension($strFileName,$strFileExtensions)
  {
    $strFileExtension = strtoupper(GetFileExtension($strFileName));

    $arrExtension=explode(";",strtoupper($strFileExtensions));
    for ($i=0; $i<=count($arrExtension); $i++){
		//Check to see if a "dot" exists
      if (substr($arrExtension[$i],0,1)=="."){
        $arrExtension[$i]=str_replace(".",NULL,$arrExtension[$i]);
	  }

	//Check to see if FileExtension is allowed
      if ($arrExtension[$i]==$strFileExtension){
		return true;
      }
    }
	return false;
  }
//--------------------------------------------
// InValidFileExtension()
// You give a list of file extensions that are not allowed.
// Purpose:  Checks if the file extension is not allowed
// Inputs:   strFileName -- the filename
//           strFileExtension -- the fileextensions that are allowed
// Returns:  boolean
// Gives False if the file extension is NOT allowed
//--------------------------------------------
  function InValidFileExtension($strFileName,$strFileExtensions)
  {
    $strFileExtension=strtoupper(GetFileExtension($strFileName));
    $arrExtension=explode(";",strtoupper($strFileExtensions));
    for ($i=0; $i<=count($arrExtension); $i++)
    {
		//Check to see if a "dot" exists
		if (substr($arrExtension[$i],0,1)=="."){
			$arrExtension[$i]=str_replace(".",NULL,$arrExtension[$i]);
		}
		//Check to see if FileExtension is not allowed
		if ($arrExtension[$i]==$strFileExtension){
			return false;
		}
    }
	return true;

  }
//--------------------------------------------
// GetFileExtension()
// Purpose:  Returns the extension of a filename
// Inputs:   strFileName     -- string containing the filename
//           varContent      -- variant containing the filedata
// Outputs:  a string containing the fileextension
//--------------------------------------------
  function GetFileExtension($strFileName){
	return substr(strrchr($strFileName, '.'), 1);
  } */
?>
	<script src="https://www.google.com/recaptcha/api.js?render=<?php echo reCAPTCHA_SITE_KEY_v3?>"></script>
<script>
	// onload
	grecaptcha.ready(function () {
		grecaptcha.execute('<?php echo reCAPTCHA_SITE_KEY_v3?>', { action: 'broadcast_locator' }).then(function (token) {
			var recaptchaResponse = document.getElementById('recaptchaResponse');
			recaptchaResponse.value = token;
		});
	});
// Every 90 Seconds
/**setInterval(function () {
  grecaptcha.ready(function () {
    grecaptcha.execute('<?php echo reCAPTCHA_SITE_KEY_v3?>', { action: 'broadcast_locator' }).then(function (e) {
      $('#recaptchaResponse').val(e);
    });
  });
}, 90 * 1000);
*/
</script>
 <style>
  .ui-progressbar {
    position: relative;
  }
  .progress-label {
    position: absolute;
    left: 50%;
    top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
  }
  </style>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/js/custom_blockUI.js"></script>
 <section class="fullWidth" id="main-cont">
    	<div class="cWrap">
           	<h2 class="fullWidth blockTitle"><span>ICAA Wellness Audit</span></h2>
            <div id="real-cont">

			<form id="frm" class="fullWidth flex auditFrm wrap"  action="<?php echo getEnv("SCRIPT_NAME")?>"  method="post" enctype="multipart/form-data">

			<div class="flex wrap"  >

				<?php brodcastNavigation()  // in php-lib/broadcast_residential_function.php?>

				<h3 class="detail-title">Broadcast residential survey url</h3>
				<div class="clear"></div>

					<label class="full" style="display:none" id="loading_section">
					<div id="progressbar"><div class="progress-label">Sending email...</div></div>
					</label>
					<input type="hidden" name="recaptcha_response" id="recaptchaResponse">

					<div id="display_section" style="display:none">
					<label class="full" id="emaillist_section">
						<!--<table class="formTable testing" cellpadding="0" cellspacing="0" border="0">
						</table>-->
					</label>
					<label class="full buttons">
						<input type="submit" name="action"  id="send_email" value="send email">
						<input type="submit" name="action"  id="back" value="Back">
					</label>
					</div>


					<div id="upload_section">
					<div>Use the tool below to invite recipients in multiple communities to take part in the ICAA benchmark. Individual invitations will go to the recipients at the same time.<br><br>

 All you need to do is prepare and upload a CSV file as described below:<Br><br>

 Input the email address in the first column and the name of a recipient in the second column. Below is an example:<br>

 <img src="icaawellness_broadcast_sample.jpg"><br>

 Enter information in your file for all recipients and save.<br><br>

 Click "Choose File" below to find and upload the information (CSV file only).<br><br>

 </div>
					<label class="full">
						<INPUT TYPE="FILE" NAME="file1">
					</label>

					<label class="full buttons" style="display:block;">
						<input type="submit" name="action" id="upload" value="Attach CSV file">
					</label>
					</div>

					</div>
				</form>

			</div>
		</div>
	</section>



	<?php siteFooter("");?>

 <script type="text/javascript">

	var loading_image = '<img src="/images/loading.gif"><br>';

$(document).ready(function(){

	$("#back").on('click',(function(e) {
	  e.preventDefault();
		$('#display_section').hide();
		$('#upload_section').show();
		$('#loading_section').html('').hide();
		}
	));

	  /// uploading the csv file
	$("#frm").on('submit',(function(e) {
	  e.preventDefault();
	  $.ajax({
			url: "ajaxupload.php",
		   type: "POST",
		   data:  new FormData(this),
		   contentType: false,
				 cache: false,
		   processData:false,
		   beforeSend : function()  {
			   //Please wait while your data is fetched and parsed. On the next step you will be able to map found fields into this application.
				$('#loading_section').html(loading_image + 'Uploading the file ... Please wait').show();
			},
			success: function(data)  {
				data = JSON.parse(data);
				if(data['status'] =='fail'){
					$("#loading_section").html(data['message']).show();
				}else{
					 // view uploaded file.
					 $('#loading_section').html('Uploaded file successfully.').show();
					 $('#display_section').show();
					 $("#emaillist_section").html(data['html']);
					 $("#frm")[0].reset();
					 $('#upload_section').hide();
				}
			},
			error: function(e)   {
				$("#loading_section").html(e).show();
			}
		});
 }));

	// broadcast the email
	$('#send_email').on('click', function (e) {
		e.preventDefault();


		$('#loading_section').html(loading_image + '<div style="color:red">Sending email ... Please wait</div>').show();
	//	var oo = $('.auditFrm').serialize()  + '&action=submit_form';
		var oo ="action=broadcast_location";
		$.post("ajax.php?v1", oo, function(data){
			data = JSON.parse(data);

			//if (data['carthtml'] != ''){
	//			$('.cartList').html(data['carthtml']);
		//	}
			if (data['status'] == 'success'){
				//message('Emails have been sent.');
				$('#loading_section').html(data['message']).show();
				$('#display_section').hide();
				$('#upload_section').show();
			}else {
				//message(data['message']);
			}
		});


	})

});
</script>
