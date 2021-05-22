<?php
include 'my_db.class.php';

if (!isset($_SESSION['type'])) {
	header("location:login.php");
}

$key = 'user_status';
$query_condtn = 'WHERE user_id = '.$_SESSION['user_id'];
$user_dtl = $query->select_single_no_bind($key,'users_tbl',$query_condtn,$pdo);

if ($user_dtl == 'inactive') {
	header("location:logout.php");
}

include 'header.php';
?>

<div class="row">
	<div class="col-lg-12">
		<div class="card card-default">
			<div class="card-header">
				<div class="row">
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
							<h3 class="card-title">User List</h3>	
					</div>
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
						<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#userModal" class="btn btn-success btn-sm rounded-0">Add new user</button>
					</div>
				</div>

				<div class="clear:both"></div>
			</div>
			<div class="card-body">
				<div class="row"><div class="col-lg-12 table-responsive">
						<table id="user_data" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th width="5%">S/N</th>
									<th>Username</th>
									<th>Mobile No.</th>
									<th>Email</th>
									<th>Type</th>
									<th>Status</th>
									<th width="7%">Edit</th>
									<th width="8%">Delete</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>
<div id="userModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="user_form">
				<div class="modal-header col-sm-12">
					<h4 class="modal-title">Add User</h4>
					<button type="button" class="close" data-dismiss="modal" align="left">&times;</button>
					
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="font-weight-bold">Choose Username </label><span class="text-danger font-weight-bold uname_error" style="position:absolute;"> *</span>
						<input type="text" name="user_name" placeholder="Enter Username..." id="user_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label class="font-weight-bold">Enter Mobile no.</label><span class="text-danger font-weight-bold mobile_error" style="position:absolute;"> *</span>
						<input type="number" name="user_mobile_no" placeholder="Enter your mobile no..." id="user_mobile_no" class="form-control" required>
					</div>
					<div class="form-group">
						<label class="font-weight-bold">Enter User Email</label>
						<input type="email" name="user_email" placeholder="Enter your email address..." id="user_email" class="form-control">
					</div>
					<div id="choose_user_type" style="display:none">
						<div class="font-weight-bold">Choose user type <br><span class="small font-weight-light" style="color:brown;">*Strictly for master admin</span></div>
						<div class="form-check-inline">
							<label class="form-check-label" for="admin">
								<input type="radio" class="form-check-input" id="admin" name="user_type" value="admin">Admin
							</label>
						</div>
						<div class="form-check-inline">
							<label class="form-check-label" for="user">
								<input type="radio" class="form-check-input" id="user" name="user_type" value="user">User
							</label>
						</div>
						<div class="form-group collapse" id="collapse_admin_pwd" style="position: relative;">
							<p class="text-danger admin_pwd_error font-weight-bold" style="margin:0; padding:0;"> *</p>
							<input type="password" name="admin_password" placeholder="Enter Master Admin password..." id="admin_password" class="form-control">
							<i class="btn fas fa-eye" id="toggle-password" toggle="#user_password" style="position: absolute; top: 50%; right:0"></i>
						</div>
					</div>
					
					<div class="table-responsive">
						<table class="table table-bordered">
							<caption style="color:brown; caption-side:top;">*User's private information. Keep safe</caption>
							<tr>
								<td>
									
									<div class="form-group" style="position: relative;">
										<label class="font-weight-bold">Choose User Password </label><span class="text-danger pwd_error" style="position:absolute;"> *</span>
										<input type="password" name="user_password" placeholder="Enter password..." id="user_password" class="form-control" required>
										<i class="btn fas fa-eye" id="toggle-password" toggle="#user_password" style="position: absolute; top: 50%; right:0"></i>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="form-group" style="position: relative;">
										<label class="font-weight-bold">Re-enter User Password </label><span class="text-danger pwd_error" style="position:absolute;"> *</span>
										<input type="password" name="user_cpassword" placeholder="Re-enter password" id="user_cpassword" class="form-control" required>
										<i class="btn fas fa-eye" id="toggle-password" toggle="#user_cpassword" style="position: absolute; top: 50%; right:0"></i>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="form-group">
										<p class="small" style="color:brown;">*Important*<br>User's should set this detail on their profile</p>
										<label class="font-weight-bold">Mother's maiden name </label>
										<input type="text" name="user_security_code" placeholder="Enter mother's maiden name..." class="form-control" readonly>
									</div>
								</td>
							</tr>		
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="user_id" id="user_id">
					<input type="hidden" name="btn_action" id="btn_action" value="Add">
					<input type="submit" name="action" id="action" class="btn btn-info" value="Add">
					<button type="button" data-dismiss="modal" class="btn btn-outline-danger">Close</button>
				</div>
			</form>
		</div>
		
	</div>
</div>
<?php
include 'footer.php';
?>
<script>
	
	$(document).ready(function () {
		var table = $('#user_data');
		sticky_header(table);
		var fetch_tbl = 'users_tbl';
		var userdataTable = $('#user_data').DataTable({
			"processing": true,    //activate processing option of DataTable's plugin
			"serverSide": true,   //activates serverSide operation
			"order": [],
			"ajax": {
					url: 'data_tables.php',
					type: 'POST',
					data: {fetch_tbl:fetch_tbl},
					dataType: 'json'
				},
			"columnDefs":[{
					//disable order sorting for column indexes
					"targets":[0,6,7],
					"orderable": false
				}],
			"pageLength":25
		});

		//submit user form datas
		$(document).on('submit','#user_form', function (event) {
			event.preventDefault();
			if ($.trim($('#user_password').val()) != $.trim($('#user_cpassword').val())) {
				$('.pwd_error').text('*Passwords do not match');
				$('.pwd_error').next('input').focus();
				return false;
			}
			else {
				$('.pwd_error').text('');
			}
			//$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url: 'users_profile_action.php',
				method: 'post',
				data: form_data,
				success:function(data) {
					console.log(data);
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
					else if(data == 'Passwords cannot be empty'){
						$('.pwd_error').text('*'+data);
						$('.pwd_error').next('input').focus();
						return;
					}
					else if(data == 'Wrong admin password!'){
						$('#collapse_admin_pwd').collapse('show');
						$('#collapse_admin_pwd').find('input').attr('required',true);	
						$('.admin_pwd_error').text('*'+data);
						$('.admin_pwd_error').next('input').focus();
						return;
					}
					else {
						$('#user_form')[0].reset();
						$('#userModal').modal('hide');
						$('#action').attr('disabled', false);
						$('#alert-modal').find('.modal-body').html(data);
						$('#alert-modal').modal('show');
						userdataTable.ajax.reload();
					}
					
				}
			});
		});

		$(".modal").on("hidden.bs.modal", function(){
			$('#user_form')[0].reset();
			$('#choose_user_type').css('display','none');
			$('#choose_user_type').find('[type="radio"]').attr('checked',false);
			$('#collapse_admin_pwd').find('input').attr('required',false);
			$('#collapse_admin_pwd').find('input').val('');
			$('#collapse_admin_pwd').collapse('hide');
			$('.admin_pwd_error, .pwd_error, .mobile_error, .uname_error').text('*');
			$('#user_password, #user_cpassword').attr('required', 'required');
			$('#user_id').val('');
			$('#action').attr('disabled', false);
			$('#action, #btn_action').val('Add');
			$('#userModal .modal-title').text("Add User");
        });

		$(document).on('click','.update',function () {
			var user_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url: 'users_profile_action.php',
				method: 'post',
				data: {user_id:user_id, btn_action:btn_action},
				dataType: 'json',
				success:function(data){
					$('#userModal').modal('show');
					$('#choose_user_type').css('display','block');
					$('#user_name').val(data.user_name);
					$('#user_email').val(data.user_email);
					$('#user_mobile_no').val(data.user_mobile_no);
					$('#'+data.user_type).attr('checked',true);
					$('#userModal .modal-title').text("Edit User");
					$('#user_id').val(user_id);
					$('#action').val('Update');
					$('#btn_action').val('update');
					$('#user_password').attr('required', false);
					$('#user_cpassword').attr('required', false);
				}
			});
		});

		$(document).on('click','#admin', function(){
			$('#collapse_admin_pwd').collapse('show');
			$('#collapse_admin_pwd').find('input').attr('required',true);	
			
		});

		$(document).on('click','#user', function(){
			$('#collapse_admin_pwd').find('input').val('');
			$('#collapse_admin_pwd').collapse('hide');
			$('#collapse_admin_pwd').find('input').attr('required',false);
			
		});

		$(document).on('change', '.status', function(){
			var user_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = 'change_status';
			console.log(status);
			$.ajax({
				url: 'users_profile_action.php',
				method: 'post',
				data: {user_id:user_id, status:status, btn_action:btn_action},
				success:function(data){
					$('#alert-modal').find('.modal-body').html(data);
					$('#alert-modal').modal('show');
					userdataTable.ajax.reload();
				}
			});
		});
		
		$(document).on('click', '.delete', function(){
			var user_id = $(this).attr("id");
			var btn_action = 'delete';

			if (confirm('Are you sure you want to remove this user?')) {
				$.ajax({
					url: 'users_profile_action.php',
					method: 'post',
					data: {user_id:user_id, btn_action:btn_action},
					success:function(data){
						$('#alert-modal').find('.modal-body').html(data);
						$('#alert-modal').modal('show');
						userdataTable.ajax.reload();
					}
				});
			}
			else {
				return false;
			}
		});
	});
</script>
</div>
</body>
</html>