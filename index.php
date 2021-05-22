<?php
include 'my_db.class.php';

//if session type is not set
if (!isset($_SESSION['type'])) {
	header("location:login.php"); //redirect to login.php page
}

include 'header.php';


/*..........users detail......*/
$sql = "CALL `count_users`();";
$stmt = $pdo->prepare($sql);
$stmt->execute();
//$this->num_rows = $stmt->rowCount();
$result = $stmt->fetchAll();
//$stmt->closeCursor();
foreach ($result as $user) {}

/*.........goods item stock details.........*/
$sql = "CALL `goods_item_stock_dtl`()";
$stmt = $pdo->prepare($sql);
$stmt->execute();
//$this->num_rows = $stmt->rowCount();
$stock_itm = $stmt->fetchAll();
$stmt->closeCursor();
foreach ($stock_itm as $stock) {}

/*..........sales invoice info.........*/
$inv_info = $db_sp->get_invoice_and_payments($pdo);
$ttl_invoice = $db_sp->num_rows;


$paid_inv = 0;
$outstanding_pay_inv = 0;
$outstanding_amt = 0;
$unpaid_inv = 0;
$unpaid_amt = 0;
$ttl_inv_worth = 0;

foreach ($inv_info as $inv) {
	$ttl_inv_worth += trim(floatval($inv['inv_fnl_ttl']));
	if ($inv['any_outstanding'] == 'no') {
					$paid_inv++;
				}
				elseif ($inv['any_outstanding'] == 'yes' && $inv['pay_amt'] != 0) {
					$outstanding_pay_inv++;
					$outstanding_amt +=$inv['outstanding_amt'];
				}
				else{
					$unpaid_inv++;
					$unpaid_amt +=$inv['inv_fnl_ttl'];
				}
}

/*............payments............*/
$sql = "CALL `payment_dtls`()";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pay_info = $stmt->fetchAll();
//$stmt->closeCursor();
foreach ($pay_info as $pay) {}

/*.......users performance tbl........*/
$sql = "CALL `users_perform`();";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user_sales = $stmt->fetchAll();
//$stmt->closeCursor();
?>
			<div class="main dashboard">
				<div align="left" class="font-weight-bold" style="font-size: 20px">DASHBOARD</div>
				<div class="row text-secondary">
					<div class="card-deck col-lg-12">
						<div class="card text-center link">
	    					<div class="card-header">
								<i class="fa fa-users text-primary" style="font-size: 30px"></i>
							</div>
		      				<div class="card-body">
		      					<div class="card-text" style="font-size: 70px"><?php echo $user['total_users'] ?></div>
		      					<a href="users.php" class="stretched-link"></a>
		      				</div>
		      				<div class="card-footer">
		      					<div class="row">
		      						<div class="col text-left">
		      							<div><?php echo $user['active_users']; ?> Active</div>
		      							<div><?php echo $user['inactive_users']; ?> Inactive</div>
		      						</div>
		      						<div class="col text-center">
		      							<div class="font-weight-bold" style="font-size: 30px">USERS</div>
		      						</div>
		      						<div class="col text-center">
		      							<div><?php echo $user['online_users']; ?> online</div>
		      						</div>
		      					</div>
		      				</div>
	    				</div>
						<div class="card text-center">
							<div class="card-header">
								<i class="fa fa-cubes text-success" style="font-size: 30px"></i>
							</div>
	      					<div class="card-body">			
	      						<div class="card-text" style="font-size: 70px"><?php echo $stock['ttl_goods_item']; ?></div>
	      						<a href="goods_item.php" class="stretched-link"></a>
	      					</div>
	      					<div class="card-footer">
	      						<div class="font-weight-bold" style="font-size: 30px">GOODS ITEM</div>
	      					</div>
	    				</div>
	    				<div class="card text-center">
    						<div class="card-header">
    						    <span class="fa-stack fa-lg">
    						        <i class="fa fa-cubes fa-stack-1x text-success" style="font-size: 30px"></i>
                                    <i class="fas fa-slash fa-stack-1x" style="color:red; font-size:30px;"></i>
                                </span>
    						</div>
     		 				<div class="card-body">
     		 					<div>
     		 						<span class="card-text" style="font-size: 70px"><?php echo $stock['out_of_stock']; ?></span><span class="small">Goods Item</span>
     		 					</div>
        						<a href="goods_item.php" class="stretched-link"></a>
      						</div>
      						<div class="card-footer">
      							<div class="font-weight-bold" style="font-size: 30px">Out Of Stock</div>
      						</div>
    					</div> 
					</div>
				</div>
   				<div class="row py-4  text-secondary">
					<div class="card-deck col-lg-12">
						<div class="card text-center">
	    					<div class="card-header" style="font-size: 30px;">
	    						<div>INVOICE <i class='fas fa-clipboard-list text-primary'></i></div>
							</div>
		      				<div class="card-body">
		      					<span class="card-text" style="font-size: 70px"><?php echo $ttl_invoice ?></span>
		      					<span class="small">Created</span>
		      					<div>
				      				<a href="sales.php" class="stretched-link"></a>
				      			</div>
		      				</div>
		      				<div class="card-footer">
		      					<div class="row">
		      						<div class="col text-left">
		      							<div><?php echo $paid_inv; ?> paid</div>
				      					<div><?php echo $outstanding_pay_inv ?> outstanding</div>
				      					<div><?php echo $unpaid_inv; ?> unpaid</div>
				      				</div>
				      				<div class="col">
		      							Invoice worth = <s>N</s><?php echo number_format($ttl_inv_worth) ?>
		      						</div>
		      					</div>
		      				</div>
	    				</div>
						<div class="card text-center">
							<div class="card-header" style="font-size: 30px">
								<div>PAYMENTS <i class="far fa-money-bill-alt text-success"></i></div>
							</div>
	      					<div class="card-body">			
	      						<span class="card-text" style="font-size: 70px"><s>N</s><?php echo number_format($pay['ttl_payments']) ?></span>
	      						<span class="small">Paid</span>
	      						<a href="payments.php" class="stretched-link"></a>
	      					</div>
	      					<div class="card-footer text-left">
	      						<div class="row">
		      						<div class="col">
		      							<div><s>N</s><?php echo number_format($pay['ttl_bank_pay']) ?> bank payment</div>
				      					<div><s>N</s><?php echo number_format($pay['ttl_cash_pay']) ?> cash payment</div>
				      				</div>
		      					</div>
	      					</div>
	    				</div>
	    				<div class="card text-center">
    						<div class="card-header" style="font-size: 30px;">
    							<div>UNPAID DEBT <span class="fa-stack fa-lg">
                                                    <i class="far fa-money-bill-alt fa-stack-1x text-success"></i>
                                                    <i class="fas fa-slash fa-stack-1x" style="color:red;"></i>
                                                </span>
                                </div>
    						</div>
     		 				<div class="card-body">
     		 					<div>
     		 						<span class="card-text" style="font-size: 70px"><s>N</s><?php echo number_format($unpaid_amt + $outstanding_amt) ?></span><span class="small">Unpaid</span>
     		 					</div>
        						<a href="sales.php" class="stretched-link"></a>
      						</div>
      						<div class="card-footer">
      							<div class="col text-left">
		      						<div><?php echo ($outstanding_pay_inv + $unpaid_inv) ?> customers/invoice</div>
				      			</div>
      						</div>
    					</div> 
					</div>
				</div>
				<div class="table-responsive my-4">
					<caption>Users Performance</caption>
					 <table class="table table-bordered table-striped">
					    <thead>
					      <tr>
					        <th>User</th>
					        <th>Invoice Created</th>
					        <th>Payments Recieved</th>
					        <th>Last Login</th>
					        <th>Login Status/Last Logout</th>
					      </tr>
					    </thead>
					    <tbody>
<?php
foreach ($user_sales as $user_info) {
	if ($user_info['user_curr_login_status'] == 'online') {
		$curr_login_status = '<span class="badge badge-success">online</span>';
	}
	else{
		$curr_login_status = '<span class="badge badge-danger">offline </span> '.$user_info['user_last_logout'].'';
	}
				echo	'<tr>
					        <td>'.$user_info['user_name'].'</td>
					        <td>'.$user_info['ttl_inv_created'].'</td>
					        <td><s>N</s> '.number_format($user_info['ttl_pay_recieved']).'</td>
					        <td>'.date_format(date_create($user_info['user_last_login']),"d/m/y H:i:s a").'</td>
							<td>'.$curr_login_status.'</td>
					    </tr>';
}

?>

					    </tbody>
					  </table>
				</div>
				<div>
					<table class="table table-striped">
						<caption>Items needing restock</caption>
					</table>
				</div>	
			</div>
	</div>
<?php
include 'footer.php';
?>
	<script type="text/javascript">
		$(document).ready(function() {
    		$('#example').DataTable();
		} );
	</script>
</body>
</html>