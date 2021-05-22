<?php
include 'my_db.class.php';

$invoice_pay_tbl = "sales_inv_tbl inv LEFT JOIN payments pay ON inv.inv_id = pay.inv_id";
$invoice_pay_keys = "inv.inv_id,inv.inv_no,inv.inv_date,inv.cust_name,inv.sales_person,inv.inv_fnl_ttl,pay.inv_id,pay.paid_to,pay.pay_amt,pay.outstanding_amt,pay.pay_type,pay.pay_date";

if (isset($_POST['btn_action'])) {

	if ($_POST['btn_action'] == 'fetch_pay_dtl') {
		
		$query_condtn = "WHERE inv.inv_id = :inv_id";
		$bind_inv_val['inv_id'] = $_POST['inv_id'];

		//select all paymnts for this invoice
		$fetch_pay_dtl = $query->select_assoc_bind($invoice_pay_keys,$invoice_pay_tbl,$query_condtn,$bind_inv_val,$pdo);

		foreach ($fetch_pay_dtl as $info) {
			$output[] = $info;
		}
		echo json_encode($output);
		
	}
	//make payment
	if ($_POST['btn_action'] == 'pay_inv') {

		if (floatval(trim($_POST['pay_amt'])) != 0) {
			$pdo->beginTransaction();
			if ($_POST['any_outstanding'] == 'yes') {
				$outstanding_amt = floatval(trim($_POST['outstanding_amt']));
			}
			elseif($_POST['any_outstanding'] == 'no'){
				$outstanding_amt = $_POST['hidden_outstanding_amt'] - floatval(trim($_POST['pay_amt']));

			}
			
			$pay_tbl = "payments";
		//	$pay_tbl_keys = "inv_id, paid_by, paid_to, pay_amt, any_outstanding, outstanding_amt, pay_type, pay_date, pay_time, pay_datetime";
			$pay_placeholders = ":inv_id, :paid_by, :paid_to, :pay_amt, :any_outstanding, :outstanding_amt, :pay_type, :pay_date, :pay_time, :pay_datetime";


	  		$payments_key_vals = "any_outstanding = :any_outstanding";
	  		$where_condtn = "inv_id = :inv_id";
	  		$inputs = ["inv_id" => $_POST['inv_id'], "any_outstanding" => $_POST['any_outstanding']];
	  		$update_previous_payment = $query->update($pay_tbl,$payments_key_vals,$where_condtn,$inputs,$pdo);

	  		$params['inv_id'] = $_POST['inv_id'];
	  		$params['paid_by'] = trim($_POST['paid_by']);
	  		$params['paid_to'] = $_SESSION['user_name'];
			$params['pay_amt'] = floatval(trim($_POST['pay_amt']));
			$params['any_outstanding'] = $_POST['any_outstanding'];
			$params['outstanding_amt'] = $outstanding_amt;
			$params['pay_type'] = $_POST['pay_type'];
			$params['pay_date'] = $query->curr_date($pdo);
			$params['pay_time'] = $query->curr_time($pdo);		
			$params['pay_datetime'] = $query->curr_datetime($pdo);

	  		$bind_pay_vals = $params;
	  		//into_payments_tbl($pl_holders,$bind_param,$pdo_conn)
			//$insert_into_pay_tbl = $query->insert($pay_tbl,$pay_tbl_keys,$pay_placeholders,$bind_pay_vals,$pdo);
			$insert_into_pay_tbl = $db_sp->into_payments_tbl($pay_placeholders,$bind_pay_vals,$pdo);


			if (isset($update_previous_payment) && isset($insert_into_pay_tbl)) {
				$pdo->commit();
				echo "Payment recieved successfully!";
			}
			else{
				$pdo->rollBack();
				echo "Payment not successful!";
			}
		}
		else{
			echo "No amount Paid!";
		}
	}

	if ($_POST['btn_action'] == 'delete_pay') {
		$pay_tbl = "payments";
		$query_condtn = "pay_id = :pay_id";
		$bind_pay_vals['pay_id'] = floatval(trim($_POST['pay_id']));
		$delete_pay = $query->delete($pay_tbl,$query_condtn,$bind_pay_vals,$pdo);
		if (isset($delete_pay)) {
			echo '<div class="text-success font-weight-bold">Payment has been successfully removed</div>';
		}
		else{
			echo '<div class="text-danger font-weight-bold">Unable to remove payment</div>';
		}

	}
}


?>