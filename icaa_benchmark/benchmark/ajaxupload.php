<?php
include_once( "../../include.php" );

$_SESSION['audit_email'] = '';
unset($_SESSION['audit_email']);

// must be ICAA PAID member in order to sign up the audit account
if (isMemberLogin()  && $_SESSION['icaa_login']["member_type"]	== 'Paid'){

}else {
	$response = array('status' => 'fail' , 'page' => 'member_login', 'message' => 'Only an icaa.cc member can access this page.');
	echo json_encode($response);
	exit;
	//header("Location: /login.php?id=audit");
}

if (!isWellnessAuditLogin()){
	$response = array('status' => 'fail' , 'page' => 'audit_login' , 'message' => 'Only a registered company can access this page.');
	echo json_encode($response);
	exit;
	//header("Location: login.php");
}

$valid_extensions = array('csv'); // valid extensions
$strUploadFolder = "/usr/local/www/groups/icaa.cc/tmp_upload";

$response = array();

if( $_FILES['file1']){

	$img = $_FILES['file1']['name'];
	$tmp = $_FILES['file1']['tmp_name'];
	// get uploaded file's extension
	$ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

	$csv_to_read="broadcast_survey_".date('Y_m_d_H_i_s').".csv";
	// check's valid format
	if(in_array($ext, $valid_extensions)){
	//	$csv_to_read = $strUploadFolder.'/'.strtolower($csv_to_read);
		if(move_uploaded_file($tmp, $strUploadFolder.'/'.$csv_to_read )) {
			$emailList = readUploadedFile($csv_to_read);

			$html = '<div>Below are the uploaded email list:<br></div>';
			$html .= '<table cellpadding="0" cellspacing="0" border="0">';
			foreach($emailList as $k => $v){
				$html .= '<tr>
						<td>'. ($k+1).') ' .$v['email_address'].' ('.$v['first_name'].')</td>
						</tr>';
			}
			$html .= '</table>';

			//echo "<img src='$path' />";
		//	$name = $_POST['name'];
			//$email = $_POST['email'];
			//include database configuration file
			//include_once 'db.php';
			//insert form data in the database
			//$insert = $db->query("INSERT uploading (name,email,file_name) VALUES ('".$name."','".$email."','".$path."')");
			//echo $insert?'ok':'err';
			$response = array('status' => 'success', 'html' => $html);
			unlink ($strUploadFolder.'/'.$csv_to_read );
		}else $response = array('status' => 'fail', 'message' => 'Cant upload file');

	} else {
		$response = array('status' => 'fail', 'message'=>'It is only allowed to upload a CSV file.');
	}
	echo json_encode($response);
}



function readUploadedFile($csv_to_read) {

	 global $strUploadFolder;
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
?>
