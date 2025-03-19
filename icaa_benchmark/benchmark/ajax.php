<?php
include_once( "../../include.php" );

include_once "../../php-lib/wellness_audit_config.php";

include "form_function.php";


$response = array();
//$_SESSION['icaa_after_login_page'] = '/wellness_audit/form_m.php';

// must be ICAA PAID member in order to sign up the audit account
if (isMemberLogin()  && $_SESSION['icaa_login']["member_type"]	== 'Paid'){

}else {
	$response = array('status' => 'error' , 'page' => 'member_login', 'message' => 'Only an icaa.cc member can access this page.');
	echo json_encode($response);
	exit;
	//header("Location: /login.php?id=audit");
}


$database = Database::instance();

if(isset($_REQUEST["action"])&&!empty($_REQUEST["action"])){
	$action = $_REQUEST["action"];
	$id = isset($_REQUEST['id']) ?    $_REQUEST['id'] : '';

	switch(strtolower($action)){

		case "check_emailaddress":
			include_once "../../php-lib/wellness_audit_functions.php";
			$response = checkEmailAllowRegister($_REQUEST['email'], $_REQUEST['company_id']);   // in php-lib/wellness_audit_functions.php
			echo json_encode($response);
			break;

		case "generate_pdf":
		//echo $_REQUEST['html_content'];

			$html_header = "<html>
					<head>
					<style>
						body{font-family: poppins, sans-serif; letter-spacing:-.02em; font-weight:400;}
						</style>
					</head>
					<body>
					";
			$audit_pdf_html = $_REQUEST['html_content'];
			// replace th_pdf to th inline css
			$audit_pdf_html = str_replace('table_pdf=""', 'style="width:100%; font-family: poppins"' ,  $audit_pdf_html);
			//$audit_pdf_html = str_replace('th_first_pdf=""', 'style="text-align:left; line-height:20;background:#0082ca;color:#fff; border-right:#000 1px solid;border-bottom:#000 1px solid; border-right:#000 1px solid; padding: 6px 3px;"',  $audit_pdf_html);

			$audit_pdf_html = str_replace('th_first_pdf=""', 'style="text-align:left; line-height:20px; font-style:bold;background:#D3D3D3; border-bottom:#000 1px solid;padding: 6px 10px;"',  $audit_pdf_html);
			$audit_pdf_html = str_replace('th_pdf=""', 'style="text-align:left; line-height:20px; font-style:bold; background:#D3D3D3; border-bottom:#000 1px solid; padding: 6px 10px;"' ,  $audit_pdf_html);

			// replace td_pdf to td inline css
			$audit_pdf_html = str_replace('td_first_pdf=""', 'style="color:#3a3b3c; border-bottom:#000 1px solid; padding:6px 10px;"' ,  $audit_pdf_html);
			$audit_pdf_html = str_replace('td_pdf=""', 'style="color:#3a3b3c; border-bottom:#000 1px solid; padding:6px 10px;"' ,  $audit_pdf_html);
			$response['success'] = 1;
			$audit_pdf_html = $html_header . $audit_pdf_html. "</body></html>";
			$response['html_content'] = $audit_pdf_html;
			$_SESSION['audit_pdf_html'] = $audit_pdf_html;

			echo json_encode($response);
		//	exit;
			break;

		case "broadcast_location":
			if (!isWellnessAuditLogin()){
				$response = array('status' => 'error' , 'page' => 'audit_login' , 'message' => 'Only a registered company can access this page.');
				echo json_encode($response);
				break;
			}

			$company_id = $_SESSION['icaa_wellness_audit_login']['company_id'];
			$company_name = $_SESSION['icaa_wellness_audit_login']['company_name'];
			$company_first_name = $_SESSION['icaa_wellness_audit_login']['company_contact_first_name'];
			$company_contact_job_title= $_SESSION['icaa_wellness_audit_login']['company_contact_job_title'];

			require '../../vendor/phpmailer/PHPMailerAutoload.php';

			//include "../vendor/phpmailer/class.phpmailer.php";
			//include "../vendor/phpmailer/class.smtp.php";

			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->Host = 'mail.icaa.cc';
			$mail->SMTPDebug = 0;
			$mail->SMTPAuth = true;
			$mail->Port = 587;
			$mail->Username = 'info@icaa.cc';
			$mail->Password = '@Int2o11!';
			$mail->SMTPSecure = 'tls';

			$mail->setFrom('info@icaa.cc', 'noreply');
			//$mail->setFrom('programming@eseelynx.com', 'noreply');

			//$mail->setFrom('info@icaa.cc', 'ICAA');
			//	$mail->addReplyTo('info@mailtrap.io', 'Mailtrap');

			$mail->Subject = 'Conduct your ICAA Wellness Audit';
			$mail->isHTML(true);
			//	$mail->addAttachment('path/to/invoice1.pdf', 'invoice1.pdf');

			$success = 0;
			$fail = 0;
			if (isset($_SESSION['audit_email']) && count($_SESSION['audit_email']) >0){
				foreach($_SESSION['audit_email'] as $k => $data){

					if ($k>0 && $k % 4)	sleep(1);


					$check_email_exist_sql = "select * from benchmark_residential_survey where email_address=:email_address";
					$params = array('email_address'=>$data['email_address']);
					$bExisted	= recordExisted($check_email_exist_sql,$params);

					if(count($bExisted) > 0){
						$residential_id  = $bExisted['id'];

					}else {
						$strSQL = "insert into benchmark_residential_survey set company_id=:company_id, location_id=:location_id, email_address=:email_address, first_name=:first_name, created_date=NOW(), created_by_location_id=:location_id1 ";
						$params = array('company_id' => $company_id, 'location_id' => $_SESSION['icaa_wellness_audit_login']['id'], 'location_id1'=> $_SESSION['icaa_wellness_audit_login']['id'], 'email_address'=>$data['email_address'] , 'first_name'=>$data['first_name']);
						$database->query_result($strSQL, $params);
						$residential_id = $database->lastInsertId();


					}


					$subject= 'Conduct your ICAA Wellness Audit';
					$url = "https://www.icaa.cc/wellness_audit/benchmark/form.php?n=".md5($residential_id);
					$sMailHtmlFileSent = 'broadcast_survey_template.inc';
					//$shtmlbody = join(file($sMailHtmlFileSent), "");
					$shtmlbody = file_get_contents($sMailHtmlFileSent);
					$sDelimiter = '%%';
					$aVar = array("NAME" => $data['first_name'],
								"COMPANY_NAME" => $company_name,
								"URL"=> '<a href="'.$url.'">'.$url.'</a>',
								"COMPANY_ID" => $company_id,
								"SIGNATURE" => $company_first_name.'<br>'.$company_contact_job_title.'<br>'.$company_name	);

					$search = array_keys($aVar);
					for($i=0; $i< count($search); $i++){
						$search[$i] = $sDelimiter.$search[$i].$sDelimiter;
					}
					$replace = array_values($aVar);

					$sContent = str_replace($search, $replace, $shtmlbody);

					$mail->addAddress($data['email_address'], $data['first_name']);
					//$mail->addBcc('colinmilner@icaa.cc', 'Colin Milner');
					$mail->Body = $sContent;


					$b = $mail->send();
					if (!$b){
						//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
						$sql_update = "Update benchmark_residential_survey set send_email_date=NOW(),mail_error=:mail_error, is_mail_sent=0 where id =:id";
						$params = array('mail_error'=>$mail->ErrorInfo,'id'=>$residential_id);
						$database->query_result($sql_update, $params);

						/*$strSQL .= " ,send_email_date=NOW() , mail_error= :mail_error, is_mail_sent=0 ";
						$params = array_merge(array('mail_error' => $mail->ErrorInfo), $params);*/
						//	echo 'Message could not be sent.';
						//echo 'Mailer Error: ' . $mail->ErrorInfo;
						$email_unsent[$fail] = array('email' => $data['email_address'], 'message' => $mail->ErrorInfo);
						$fail++;
					}else {

						$sql_update = "Update benchmark_residential_survey set send_email_date=NOW(), is_mail_sent=1 where id =:id";
						$params = array('id'=>$residential_id);

						$database->query_result($sql_update, $params);
						//$strSQL .= " ,send_email_date=NOW(), is_mail_sent=1 ";
						$email_sent[$success] = array('email' => $data['email_address'],'id'=>$residential_id);
						$success++;
					}

					//echo $sSql.'<br>' ;

					$mail->ClearAddresses();
				}

				if (count($email_unsent) >0){
					$msg = '<br><br>The following email(s) have not send out:<br>';
					foreach($email_unsent as $kk => $dd){
						$msg .= '<span style="color:red">'.$dd['email'].' ('.$dd['message'].')</span><br>';
					}
				}
				$response['status'] = 'success';
				$response['email_sent'] = $email_sent;
				$response['email_unsent'] = $email_unsent;
				$response['message'] = count($email_sent).' email(s) have been sent.'. $msg;
			}else $response = array('status' => 'fail', 'message' => 'Email address is not uploaded');

			$mail->smtpClose();

			$_SESSION['audit_email'] = '';
			unset($_SESSION['audit_email']);

			echo json_encode($response);
			break;
	}
}
?>
