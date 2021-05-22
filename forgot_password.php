<?php
include 'my_db.class.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'class/src/Exception.php';
require 'class/src/PHPMailer.php';
require 'class/src/SMTP.php';
function send_otp($user_email,$user_reset_otp,$pdo){

	$mail = new PHPMailer;
	//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	$mail->IsSMTP();
	//$mail->SMTPDebug = 2;
	$mail->Host = 'ssl://smtp.gmail.com';
	$mail->Port = '465';
	$mail->SMTPAuth = true;
	$mail->Username = '';
	$mail->Password = '';
	$mail->SMTPSecure = 'ssl';
	$mail->From = '';
	$mail->FromName = '';
	$mail->AddAddress($user_email);
	$mail->AddCC('', '');
	$mail->IsHtml(true);
	$mail->Subject = 'Password reset request for your account';

	$message_body = '<p>For reset password, you have to enter this verification code when prompted: <b>'.$user_reset_otp.'</b>.</p>
	<p>Sincerely</p>';

	$mail->Body = $message_body;


	if($mail->Send()){
		$pdo->commit();
		header("location:forgot_password.php?otp_mail=sent");

	}
	else{
		$pdo->rollBack();
		$email_mssg = 'We\'re facing some issue sending email. :'.$mail->ErrorInfo ;
		echo $email_mssg;


	}
}

$message = '';
$pwd_message = '';
$u_name_mssg ='';
$mobile_no_mssg = '';
$mssg = '';
$mm_name_mssg = '';
$email_mssg = '';
$otp_mssg = '';


$users_tbl = 'users_tbl';
$keys = "*";
$user_verified = 0;


//submit user_name and mobile_no
if(isset($_POST['reset_step1']) && !isset($_POST['submit_mm_name'])){
	if (addslashes(trim($_POST['user_name'])) == '' || addslashes(trim($_POST['user_mobile_no'])) == '') {
		$mssg = '*All fields are required';
	}
	else{
		$pdo->beginTransaction();

		$bind_user_vals['user_mobile_no'] = addslashes(trim($_POST['user_mobile_no']));
		$bind_user_vals['user_name'] = addslashes(trim($_POST['user_name']));
		$query_condtn = "user_mobile_no = :user_mobile_no AND user_name = :user_name";
		$user_dtl = $actions->get_users($keys,$query_condtn,$bind_user_vals,$pdo);
		$no_of_users = $actions->no_of_rows;
		
		if ($no_of_users > 0) {
			foreach ($user_dtl as $dtl) {
				$_SESSION['user_id'] =$dtl['user_id'];
				$_SESSION['user_name'] = $dtl['user_name'];
				$_SESSION['user_mobile_no'] = $dtl['user_mobile_no'];
				$_SESSION['user_email'] = $dtl['user_email'];
				$_SESSION['user_security_code'] = $dtl['user_security_code'];
				
				header('Location:forgot_password.php?step2=1');
			}
		}
		else{
			$mssg = '*Incorrect username or mobile number. Ensure username and mobile number matches your account';
		}

	}
}

//submit mother_maiden_name
if (isset($_POST['submit_mm_name']) && isset($_SESSION['user_security_code'])) {

	if (addslashes(trim($_POST['user_security_code'])) == '') {
		$mm_name_mssg = '*This field is required';
		
	}
	else{
		if (password_verify(addslashes(trim(strtoupper($_POST['user_security_code']))), $_SESSION['user_security_code'])) {
			header('Location:forgot_password.php?step3=1');
		}
		else{
			$mm_name_mssg = '*Incorrect input';

		}
		
	}
	
}

//submit to send otp
if (isset($_POST['send_otp']) && isset($_POST['user_email'])) {
		if (addslashes(trim($_POST['user_email'])) != $_SESSION['user_email']) {
			$email_mssg = '*Input does not match your account';
		}
		else{

			$pdo->beginTransaction();
					
				$user_reset_otp = rand(100000,999999);
				$users_key_vals = 'user_reset_otp = :user_reset_otp,
									user_verified = :user_verified';
				$query_condtn = 'user_id = :user_id';
				$bind_user_otp['user_reset_otp'] = password_hash($user_reset_otp, PASSWORD_DEFAULT);;
				$bind_user_otp['user_verified'] = $user_verified;
				$bind_user_otp['user_id'] = $_SESSION['user_id'];
				$update_otp = $query->update($users_tbl,$users_key_vals,$query_condtn,$bind_user_otp,$pdo);
				
				if(isset($update_otp)){
					
					$send_otp = send_otp(addslashes(trim($_POST['user_email'])),$user_reset_otp, $pdo);
	
				}
				else{
					$pdo->rollBack();
					$email_mssg = 'Some problem occured, please try again';
				}					
		}
}

//verify otp
if (isset($_POST['verify_otp']) && isset($_POST['otp_code'])) {
	if (addslashes(trim($_POST['otp_code'])) == '') {
		$otp_mssg = '*Empty field';
	}else{
		$key = 'user_reset_otp';
		$pdo->beginTransaction();		
		$query_condtn = 'WHERE user_id = '.$_SESSION['user_id'];
		$user_dtl = $query->select_single_no_bind($key,$users_tbl,$query_condtn,$pdo);
		
		if (password_verify(addslashes(trim($_POST['otp_code'])) , $user_dtl)) {
			$pdo->commit();
			header("location:forgot_password.php?step4=1");
			
		}
		else{
			$pdo->rollBack();
			$otp_mssg = '*Incorrect verification code';
		}
	}
}


//save new password
if (isset($_POST['save_user_password']) && isset($_SESSION['user_id'])) {
	if (addslashes(trim($_POST['new_user_password'])) == '' || addslashes(trim($_POST['new_user_cpassword'])) == '') {
		$pwd_message = '*Fields cannot be empty!';	
	}
	elseif (addslashes(trim(strtoupper($_POST['new_user_password']))) === addslashes(trim(strtoupper($_POST['new_user_cpassword'])))) {

			$pdo->beginTransaction();

			$user_verified = 1;
			$user_reset_otp = rand(100000,999999);
			$users_key_vals = 'user_reset_otp = :user_reset_otp,
								user_verified = :user_verified,
								user_password = :user_password';
			$bind_user_vals['user_reset_otp'] = password_hash(addslashes(trim(strtoupper($user_reset_otp))), PASSWORD_DEFAULT);
			$bind_user_vals['user_verified'] = $user_verified;
			$bind_user_vals['user_password'] = password_hash(addslashes(trim(strtoupper($_POST['new_user_cpassword']))), PASSWORD_DEFAULT);
			$bind_user_vals['user_id'] = $_SESSION['user_id'];
			$query_condtn = 'user_id = :user_id';
			$update_pwd = $query->update($users_tbl,$users_key_vals,$query_condtn,$bind_user_vals,$pdo);
			if (isset($update_pwd)) {	
				$pdo->commit();

				header("location:login.php?reset_successfull=1");
			}
			else{
				echo '<script>alert("unable to update password, please try again or contact your admin.")</script>';
				$pdo->rollBack();
			}
	}
	else{
		$pwd_message = '*Passwords do not match';
	}
}

include 'bg_login.php';
if (isset($_GET['step1'])) {

?>

		<form class="container" method="post">
			<h4>Complete the following steps OR contact your Admin to reset password</h4>
				<p  class="text-danger font-weight-bold">
					<?php echo $mssg; ?>
				</p>
				<div class="form-group">
					<label class="font-weight-bold">Enter Username </label>
					<span class="text-danger font-weight-bold">*
						<?php echo $u_name_mssg; ?>
					</span>
					<input type="text" name="user_name" placeholder="Enter Username..." id="user_name" class="form-control" required>
				</div>
				<div class="form-group">
					<label class="font-weight-bold">Enter mobile no.</label>
					<span class="text-danger font-weight-bold">*
						<?php echo $mobile_no_mssg; ?>
					</span>
			    	<input type="number" placeholder="Enter mobile no..." class="form-control" name="user_mobile_no" required>
			    </div>
			    <div class="form-group">
			    	<input type="submit" name="reset_step1" class="btn btn-success btn-block submit-btn" value="Continue">
			    </div>
		
<?php

	}
	if (isset($_GET['step2'])) {
?>		
			<form class="container" method="post">
				<h4>What is your mother's maiden name?</h4>
				<p  class="text-danger font-weight-bold">
					<?php echo $mm_name_mssg ?>
				</p>
				<div class="form-group">
			    	<input type="text" placeholder="Enter your mother's maiden name..." class="form-control" name="user_security_code" required>
			    </div>	
			    <div class="form-group">
			    	<input type="submit" name="submit_mm_name" class="btn btn-primary btn-block submit-btn" value="Submit">
			    </div>

<?php
	}
	if (isset($_GET['step3']) || isset($_GET['otp_mail'])) {
?>

			<form class="container" method="post">
				<p>A One-Time-Password(OTP) will be sent to your registered email</p>
				<p  class="text-danger font-weight-bold">
					<?php echo $email_mssg;?>
				</p>
				<div class="form-group">
				<label>Enter Email</label>
			    	<input type="email" placeholder="Enter your email..." class="form-control" name="user_email" required
					<?php if(isset($_SESSION['user_email']) && isset($_GET['otp_mail'])){ echo 'value = "'.$_SESSION['user_email'].'"';}?>>
			    </div>	
			    <div class="form-group">
			    	<input type="submit" name="send_otp" class="btn btn-primary btn-block submit-btn" value="Send OTP">
			    </div>

<?php
	}
	if (isset($_GET['otp_mail'])) {
		if ($_GET['otp_mail'] == 'sent') {
			echo '<div class = "small alert alert-success text-success">Please check your e-mail, a verification code has been sent to reset password<div>';
		}

?>

				<p  class="text-danger font-weight-bold">
					<?php echo $otp_mssg;?>
				</p>
				<div class="form-group">
			    	<input type="text" placeholder="Enter OTP code..." class="form-control" name="otp_code" required>
			    </div>	
			    <div class="form-group">
			    	<input type="submit" name="verify_otp" class="btn btn-success btn-block submit-btn" value="Verify">
			    </div>
				<!-- <div><a href="#">Resend OTP?</a></div> -->


<?php
	}
	if (isset($_GET['step4'])) {
?>
				<form class="container" method="post">
					<h4>Create new password</h4>
						<div class="text-danger font-weight-bold">
							<?php echo $pwd_message; ?>
						</div>
						<div class="form-group" style="position:relative;">
							<label for="new_user_password" class="font-weight-bold">Enter new password</label>
							<input type="password" placeholder="Enter new password..." class="form-control" name="new_user_password" id="new_user_password" required>
							<i class="btn fas fa-eye" id="toggle-password" toggle="#user_password" style="position: absolute; top: 50%; right:0"></i>
						</div>
						<div class="form-group" style="position:relative;">
							<label for="new_user_cpassword" class="font-weight-bold">Re-enter new password</label>
							<input type="password" placeholder="Re-enter new password..." class="form-control" name="new_user_cpassword" id="new_user_cpassword" required>
							<i class="btn fas fa-eye" id="toggle-password" toggle="#user_password" style="position: absolute; top: 50%; right:0"></i>
						</div>
						<div class="form-group">
							<input type="submit" name="save_user_password" class="btn btn-success btn-block submit-btn" value="Confirm">
						</div>


<?php
	}
?>
		</form>
</div>
</div>
</div>
</div>

<script>
	$(document).ready(function(){});
</script>
</body>
</html>