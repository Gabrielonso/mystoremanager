<?php 

include 'my_db.class.php';

if (!isset($_SESSION['type'])) {
	header("location:login.php");
}

if (isset($_POST['btn_action'])) {
	$users_tbl = "users_tbl";

	//add new user
	if ($_POST['btn_action'] == 'Add') {

		if(addslashes(trim($_POST['user_name'])) == ''){
			echo 'Invalid name input';
			return;
		}
		if(addslashes(trim($_POST['user_mobile_no'])) == ''){
			echo 'Invalid mobile no';
			return;
		}

		if (addslashes(trim($_POST['user_password'])) == '' && addslashes(trim($_POST['user_cpassword'])) == '') {
			echo 'Passwords cannot be empty';
			return;
		}
		
		

		$key = 'user_mobile_no';	
		$query_condtn = '';
		$users_mobile_no = $query->select_assoc_no_bind($key,$users_tbl,$query_condtn,$pdo);

		foreach ($users_mobile_no as $user_no) {
			if ($user_no['user_mobile_no'] == $_POST['user_mobile_no']) {
				echo 'Mobile number already exist';
				return;
			}
		}

		$pdo->beginTransaction();
		$users_keys = "user_name, user_mobile_no,user_password,user_status";
		$user_vals = ":user_name, :user_mobile_no,:user_password, :user_status";
		$bind_user_vals['user_name'] = addslashes(trim($_POST['user_name']));
		$bind_user_vals['user_mobile_no'] = $_POST['user_mobile_no'];	
		$bind_user_vals['user_password'] = password_hash(addslashes(trim(strtoupper($_POST['user_password']))), PASSWORD_DEFAULT);
		$bind_user_vals['user_status'] = 'active';

		if (addslashes(trim($_POST['user_email'])) != '') {
			$users_keys .= ', user_email';
			$user_vals .= ', :user_email';
			$bind_user_vals['user_email'] = addslashes(trim($_POST['user_email']));
		}

		$insert_new_user = $query->insert($users_tbl,$users_keys,$user_vals,$bind_user_vals,$pdo);

		if (isset($insert_new_user) && isset($users_mobile_no)) {
			$pdo->commit();
			echo '<div class="font-weight-bold text-success">New user Added!</div>';
		}
		else{
			$pdo->rollBack();
			echo '<div class="font-weight-bold text-danger">Unable to add user!</div>';
		}

		
	}

	//fetch user detail
	if ($_POST['btn_action'] == 'fetch_single') {
		$user_keys = "user_email, user_name, user_mobile_no, user_type";
		$query_condtn = "user_id = :user_id";
		$bind_user_vals['user_id'] = intval($_POST['user_id']);
		$result = $actions->get_users($user_keys,$query_condtn,$bind_user_vals,$pdo);
		foreach ($result as $row) {
			$output['user_email'] = $row['user_email'];
			$output['user_name'] = $row['user_name'];
			$output['user_mobile_no'] = $row['user_mobile_no'];
			$output['user_type'] = $row['user_type'];

		}
		echo json_encode($output);
	}

	//update or edit user
	if ($_POST['btn_action'] == 'update' || $_POST['btn_action'] == 'edit_profile') {

		if(addslashes(trim($_POST['user_name'])) == ''){
		echo 'Invalid name input';
		return;
	}

	if(addslashes(trim($_POST['user_mobile_no'])) == ''){
		echo 'Invalid mobile no';
		return;
	}

		$pdo->beginTransaction();

		$key = 'user_id,user_type, user_password,user_mobile_no';	
		$query_condtn = '';
		$users_dtl = $query->select_assoc_no_bind($key,$users_tbl,$query_condtn,$pdo);

		$user_key_vals = "user_name = :user_name,
						user_mobile_no = :user_mobile_no,
						user_email = :user_email";
		$query_condtn = "user_id = :user_id";

		$bind_user_vals['user_name'] = addslashes(trim($_POST['user_name']));
		$bind_user_vals['user_mobile_no'] = addslashes(trim($_POST['user_mobile_no']));
		$bind_user_vals['user_email'] = addslashes(trim($_POST['user_email']));

		/*.......edit user profile...*/
		if ($_POST['btn_action'] == 'edit_profile') {
			$user_id = $_SESSION['user_id'];
			$bind_user_vals['user_id'] = $user_id;
		}
		else{
			$user_id =  intval(trim($_POST['user_id']));
			$bind_user_vals['user_id'] = $user_id;
		}

		$admin = 0;
		foreach ($users_dtl as $dtl) {
			if ($dtl['user_mobile_no'] == $_POST['user_mobile_no'] && $dtl['user_id'] != $user_id) {
				echo 'Mobile number already exist';
				return;
			}
			if ($_POST['btn_action'] == 'update') {

				if ($_POST['user_type'] == 'admin' && $_POST['admin_password'] != '') {
					//.....verify admin password
					if ($dtl['user_type'] == 'master_admin' && password_verify(addslashes(trim(strtoupper($_POST['admin_password']))) , $dtl['user_password'])) {
						$admin++;
					}

				}
				elseif ($_POST['user_type'] == 'user' && $_POST['admin_password'] == '') {
					$admin++;
				}

				if ($_POST['user_type'] == $dtl['user_type'] && $user_id == $dtl['user_id']) {
						$admin++;
						$user_key_vals .= ', user_type = :user_type';
						$bind_user_vals['user_type'] = $dtl['user_type'];
				}
			}
			else{
				$admin = -1;
			}		
		}

		if ($admin > 0) {
			$user_key_vals .= ', user_type = :user_type';
			$bind_user_vals['user_type'] = addslashes(trim($_POST['user_type']));
		}
		if($admin == 0){
			echo 'Wrong admin password!';
			return;
		}
		
		if (addslashes(trim(strtoupper($_POST['user_password']))) != '' || addslashes(trim(strtoupper($_POST['user_cpassword']))) != '') {
			$user_key_vals .= ", user_password = :user_password";
			$bind_user_vals['user_password'] = password_hash(addslashes(trim(strtoupper($_POST['user_cpassword']))), PASSWORD_DEFAULT);
		}
	

		if (addslashes(trim(strtoupper($_POST['user_security_code']))) != '') {
			$user_key_vals .= ", user_security_code = :user_security_code";
			$bind_user_vals['user_security_code'] = password_hash(addslashes(trim(strtoupper($_POST['user_security_code']))), PASSWORD_DEFAULT);				
		}

		$update_user = $query->update($users_tbl,$user_key_vals,$query_condtn,$bind_user_vals,$pdo);

		if (isset($update_user)) {
			$pdo->commit();
			echo '<div class="font-weight-bold text-success">User profile edited!</div>';
		}
		else{
			$pdo->rollBack();
			echo '<div class="font-weight-bold text-danger">Failed to edit user profile!</div>';
		}
	}

	
	//change user status
	if ($_POST['btn_action'] == 'change_status') {
		$pdo->beginTransaction();
		$status = 'active';
		if ($_POST['status'] == 'active') {
			$status = 'inactive';
		}
		$user_key_vals = "user_status = :user_status";
		$query_condtn = "user_id = :user_id";
		$bind_user_vals['user_status'] = $status;
		$bind_user_vals['user_id'] = intval($_POST['user_id']);
		$update_user = $query->update($users_tbl,$user_key_vals,$query_condtn,$bind_user_vals,$pdo);

		if (isset($update_user)) {
			$pdo->commit();
			echo '<div class="font-weight-bold text-success">User status changed to '.$status.'</div>';
		}
		else{
			$pdo->rollBack();
			echo '<div class="font-weight-bold text-danger">User status not changed</div>';
		}
	}

	//delete user
	if ($_POST['btn_action'] == 'delete') {
		$pdo->beginTransaction();
		
		$query_condtn = "user_id = :user_id";
		$bind_user_vals['user_id'] = intval($_POST['user_id']);
		$delete_user = $query->delete($users_tbl,$query_condtn,$bind_user_vals,$pdo);

		if (isset($delete_user)) {
			$pdo->commit();
			echo '<div class="font-weight-bold text-success">User record has been removed</div>';
		}
		else{
			$pdo->rollBack();
			echo '<div class="font-weight-bold text-danger">Unable to remove user</div>';
		}
	}
}


 ?>