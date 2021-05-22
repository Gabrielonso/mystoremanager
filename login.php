<?php
include 'my_db.class.php';

if (isset($_SESSION['type'])) {
	header("location:index.php");
}


$message = '';
$u_message = '';

if (isset($_POST['login'])) {
	$keys = "*";
	$query_condtn = "user_name = :user_name";
	$bind_user_val['user_name'] = addslashes(trim($_POST['user_name']));
	
	$user_dtl = $actions->get_users($keys,$query_condtn,$bind_user_val,$pdo);
	$no_of_users = $actions->no_of_rows;
	if ($no_of_users == 1) {

		foreach ($user_dtl as $dtl) {
			//verify password
			if (password_verify(addslashes(trim(strtoupper($_POST['user_password']))) , $dtl['user_password'])) {
				//for 'Active' user account
				if ($dtl['user_status'] == 'active') {
					//set user name, type and id for logged-in user session
					$_SESSION['type'] = $dtl['user_type'];
					$_SESSION['user_status'] = $dtl['user_status'];
					$_SESSION['user_id'] = $dtl['user_id'];
					$_SESSION['user_name'] = $dtl['user_name'];

					$pdo->beginTransaction();

					$users_tbl = 'users_tbl';
					$user_last_login = $query->curr_datetime($pdo);
					$user_curr_login_status = 'online';
					$user_last_logout = '0000-00-00 00:00:00';

					$users_key_vals = 'user_last_login = :user_last_login,
										user_curr_login_status = :user_curr_login_status,
										user_last_logout = :user_last_logout';
					$bind_user_vals['user_last_login'] = $user_last_login;
					$bind_user_vals['user_curr_login_status'] = $user_curr_login_status;
					$bind_user_vals['user_last_logout'] = $user_last_logout;
					$bind_user_vals['user_id'] = $_SESSION['user_id'];
					$query_condtn = 'user_id = :user_id';
					$update_login = $query->update($users_tbl,$users_key_vals,$query_condtn,$bind_user_vals,$pdo);

					if (isset($update_login) && isset($_SESSION['user_id'])) {
						$pdo->commit();
						header("location:index.php"); 
					}
					else{
						$u_message = '*Unable to update login session variables';
						$pdo->rollBack();
					}

					
				}
				else {
					$u_message = '<b class="text-danger">*Your account is disabled, contact your Admin</b>';
				}
			}
			else {
				$message = '<b class="text-danger">*Wrong Password!</b>';
			}
		}
	}
	else {
		$u_message = '<b class="text-danger">*Multiple or no user record found for user!</b>';
	}

}

include 'bg_login.php';

if (isset($_GET['reset_successfull'])) {
	echo '<script>alert("Password reset successfully. Proceed to Login with your new password")</script>';
	session_destroy();
}


?>
	<form class="container" method="post">
	    <h1>Login</h1>
		<div>
			<?php echo $u_message; ?>
		</div>
		<div class="form-group">
			<label for="user_name"><b>Username</b></label>
		    <input type="text" class="form-control" placeholder="Enter username..." name="user_name" required>
		</div>
		<div class="form-group" style="position:relative;">
		    <label for="psw"><b>Password</b></label>
		    <input type="password" class="form-control" placeholder="Enter password..." name="user_password" required>
			<i class="btn fas fa-eye" id="toggle-password" toggle="#user_password" style="position: absolute; top: 50%; right:0; box-shadow:none;"></i>
		</div>
		<div>
			<?php echo $message; ?>
		</div>
		<div class="form-group">
			<button type="submit" name="login" class="btn btn-success btn-block submit-btn" disabled>
  				<span class="spinner-grow spinner-grow-sm"></span>
  				Loading, please wait...
			</button>
	 	</div>
		<div class="font-weight-bold text-center forgot_password">
    		
		</div>
	</form>
</div>
</div>
</div>
</div>


<script>
	$(document).ready(function() {
<?php

$pdo->beginTransaction();

	$update =$db_sp->first_daily_update($pdo);

if (isset($update)) {
	$pdo->commit();
?>
		$('.submit-btn').attr('disabled',false);
		$('.submit-btn').text('Login');
		$('.forgot_password').html('<a class="" href="forgot_password.php?step1=1">Forgot password?</a>');
<?php
}
else{
	$pdo->rollBack();
}
?>

});
</script>
</body>
</html>