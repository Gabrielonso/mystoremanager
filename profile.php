<?php

include 'my_db.class.php';



//if user is not logged in
if (!isset($_SESSION['type'])) {
	header("location:login.php");
}

$user_keys = "user_name,user_mobile_no,user_email,user_status,user_security_code";
$query_condtn = "user_id = :user_id";
$bind_user_vals['user_id'] = $_SESSION['user_id'];
$user_dtl = $actions->get_users($user_keys,$query_condtn,$bind_user_vals,$pdo);


foreach ($user_dtl as $dtl) {
	$user_name = $dtl['user_name'];
	$user_email = $dtl['user_email'];
	$user_mobile_no = $dtl['user_mobile_no'];
	$user_status = $dtl['user_status'];

	if (isset($dtl['user_security_code']) || $dtl['user_security_code'] != '') {
		$private = '<span class="badge badge-success">This has been set</span>';
	}
	else{
		$private = '<span class="badge badge-danger">*NOTE* This has not been set!</span>';

	}

}

if ($user_status == 'inactive') {
	header("location:logout.php");
}

include 'header.php';
?>

<div class="container">
	<div class="card card-default" style="box-shadow: 0 0 5px rgba(0,0,0,0.5);">
		<div class="card-header">Edit Profile</div>
		<div class="card-body">
			<form method="post" id="edit_profile_form">
				<span id="message"></span>
				<div class="form-group">
					<label class="font-weight-bold" for="user_name">Username</label><span class="text-danger font-weight-bold uname_error" style="position:absolute;"> *</span>
					<input type="text" name="user_name" class="form-control" id="user_name" value="<?php echo $user_name;?>" required>
				</div>
				<div class="form-group">
					<label class="font-weight-bold" for="user_mobile_no">Mobile number</label><span class="text-danger font-weight-bold mobile_error" style="position:absolute;"> *</span>
					<input type="number" name="user_mobile_no" class="form-control" id="user_mobile_no" value="<?php echo $user_mobile_no;?>" required>
				</div>
				<div class="form-group">
					<label class="font-weight-bold" for="user_email">Email</label>
					<input type="email" name="user_email" class="form-control" id="user_email" value="<?php echo $user_email;?>">
				</div>
				<hr>
				<label class="text-primary">Ignore this section if you do not want to change</label>
				<div class ="text-danger">*Important*<br>Keep these details safe and private</div>
				<div class="form-group"  style="position: relative;">
					<label class="font-weight-bold">New Password</label>
					<input type="password" name="user_password" class="form-control" id="user_password">
					<i class="btn fas fa-eye" id="toggle-password" toggle="#user_cpassword" style="position: absolute; top: 50%; right:0"></i>
				</div>
				<div class="form-group"  style="position: relative;">
					<label class="font-weight-bold">Re-enter Password</label>
					<input type="password" name="user_cpassword" class="form-control" id="user_cpassword">
					<i class="btn fas fa-eye" id="toggle-password" toggle="#user_cpassword" style="position: absolute; top: 50%; right:0"></i>
				</div>
				<span id="error-password"></span>
				<div class="form-group" style="position: relative;">
					<label class="font-weight-bold">Mother's maiden name </label> <?php echo $private; ?>
					<input type="text" name="user_security_code" class="form-control" >
					<i class="btn fas fa-eye" id="toggle-password" toggle="#user_cpassword" style="position: absolute; top: 50%; right:0"></i>
				</div>
				<div class="form-group">
					<input type="hidden" name="btn_action" value="edit_profile">
					<input type="submit" name="edit_profile" class="btn btn-info" id="edit_profile" value="Edit profile">
				</div>
			</form>
		</div>
	</div>
</div>
<?php
include 'footer.php';
?>
<script type="text/javascript">
	$(document).ready(function () {
		//submit edited profile form
		$('#edit_profile_form').on('submit',function (event) {
			event.preventDefault();
			//check if passwords match
			if ($.trim($('#user_password').val()) != $.trim($('#user_cpassword').val())) {
				$('#error-password').html('<label class="text-danger font-weight-bold">Password Not Match</label>');
				return false;
			}
			else {
				$('#error-password').html('');
			}
			//convert & store form field datas to url encode
			var form_data = $(this).serialize();
			$.ajax({
				url: 'users_profile_action.php',
				method: 'post',
				data: form_data,
				success:function(data){
					if (data == 'Invalid name input') {
						$('.uname_error').text('*Invalid Input');
						$('.uname_error').next('input').focus();
						return;
					}
					else if(data == 'Mobile number already exist' || data == 'Invalid mobile no'){
						$('.mobile_error').text('*'+data);
						$('.mobile_error').next('input').focus();
						return;
					}
					else{
						$('#edit_profile').attr('disabled', false);
						$('#user_password').val('');
						$('#user_cpassword').val('');
						$('#alert-modal').find('.modal-body').html(data);
						$('#alert-modal').modal('show');
					}
					
				}
			});
		});
	});
</script>