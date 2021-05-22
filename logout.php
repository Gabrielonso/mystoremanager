<?php
//session_start();
include 'my_db.class.php';

if (isset($_SESSION['user_id'])) {
	$users_tbl = 'users_tbl';
	$user_curr_login_status = 'offline';
	$user_last_logout = $query->curr_datetime($pdo);

	$users_key_vals = 'user_curr_login_status = :user_curr_login_status,
						user_last_logout = :user_last_logout';

	$bind_user_vals['user_curr_login_status'] = $user_curr_login_status;
	$bind_user_vals['user_last_logout'] = $user_last_logout;
	$bind_user_vals['user_id'] = $_SESSION['user_id'];
	$query_condtn = 'user_id = :user_id';
	$query->update($users_tbl,$users_key_vals,$query_condtn,$bind_user_vals,$pdo);
	session_destroy();
	
}

header("location:login.php");

?>



