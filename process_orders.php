<?php
include 'my_db.class.php';

if (isset($_POST['order_action'])) {
	

	if ($_POST['order_action'] == 'get_itm_dtl') {
		$joint_keys = "itm.item_id";
		$joint_tbl = "goods_items_tbl itm";
		$query_condtn = "WHERE itm.item_id = :item_id";

		if (!isset($_POST['order_qty'])){
			$joint_keys .=", itm.ctn_price, itm.unit_price";
			//$joint_tbl .= " JOIN price_list pr ON itm.item_id = pr.item_id";
			
		}

		if (isset($_POST['order_qty']) && isset($_POST['qty_type'])) {

			if ($_POST['qty_type'] == 'pcs') {
				$joint_keys .=", rtl.rtl_curr_stock AS curr_stock";
				$joint_tbl .=" JOIN rtl_stock_tbl rtl ON itm.item_id = rtl.item_id";
			}
			if($_POST['qty_type'] == 'ctn'){
				$joint_keys .=", wh.wh_curr_stock AS curr_stock";
				$joint_tbl .=" JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id";
			}
		}

		
		$bind['item_id'] = floatval(trim($_POST['item_id']));
		$result = $query->select_assoc_bind($joint_keys,$joint_tbl,$query_condtn,$bind,$pdo);
		foreach ($result as $key => $val){
			$key = $val;
		}
		echo json_encode($key = $val);		
	}	
}



if (isset($_POST['invoice_action'])) {

	$invoice_tbl = "sales_inv_tbl";
	$orders_tbl = "cust_orders_tbl";
	$rtl_stock_tbl = "rtl_stock_tbl";
	$wh_stock_tbl = "wh_stock_tbl";
	$pay_tbl = "payments";

	$inv_keys = "inv_no, inv_date, inv_time, cust_name, cust_mobile_no, cust_address, sales_person, inv_sub_ttl, inv_dsc_ttl, inv_fnl_ttl, inv_datetime";
	$inv_val_pl_holders = " :inv_no, :inv_date, :inv_time, :cust_name, :cust_mobile_no, :cust_address, :sales_person, :inv_sub_ttl, :inv_dsc_ttl, :inv_fnl_ttl, :inv_datetime";

	$orders_tbl_keys = "inv_id, order_item, item_id, prev_order_qty, order_qty, qty_type, order_price, order_amt, order_date, order_time, order_datetime";
	$orders_pl_holders = ":inv_id, :order_item, :item_id, :prev_order_qty, :order_qty, :qty_type, :order_price, :order_amt, :order_date, :order_time, :order_datetime";

	$curr_date = $query->curr_date($pdo);
	$curr_time = $query->curr_time($pdo);
	$curr_datetime = $query->curr_datetime($pdo);

	if ($_POST['invoice_action'] == 'create_invoice') {
		

		

		if ($_POST['inv_date'] == '' || $_POST['inv_datetime'] == '') {
			$inv_date = $curr_date;
			$inv_time = $curr_time;
			$inv_datetime = $curr_datetime;
		}
		else{
			$inv_date = $_POST['inv_date'];
			$inv_time = $_POST['inv_time'];
			$inv_datetime = $_POST['inv_datetime'];
		}
		
		if (floatval($_POST['cust_address']) == '') {
			$cust_mobile_no = floatval($_POST['cust_mobile_no']);
		}
		else {
			$cust_mobile_no = $_POST['cust_mobile_no'];
		}

		$inv_no = 0;
		$cust_name = $_POST['cust_name'];
		$cust_address = $_POST['cust_address'];
		$sales_person = $_SESSION['user_name'];
		$inv_sub_ttl = $_POST['inv_sub_ttl'];
		$inv_dsc_ttl = $_POST['inv_dsc_ttl'];
		$inv_fnl_ttl = $_POST['inv_fnl_ttl'];


		$pdo->beginTransaction();

			//$params = [];
			$params['inv_no'] = $inv_no;
			$params['inv_date'] = $inv_date;
			$params['inv_time'] = $inv_time;
			$params['cust_name'] = addslashes(trim($cust_name));
			$params['cust_mobile_no'] = $cust_mobile_no;
			$params['cust_address'] = addslashes(trim($cust_address));
			$params['sales_person'] = addslashes(trim($sales_person));
			$params['inv_sub_ttl'] = floatval(trim($inv_sub_ttl));
			$params['inv_dsc_ttl'] = floatval(trim($inv_dsc_ttl));
			$params['inv_fnl_ttl'] = floatval(trim($inv_fnl_ttl));
			$params['inv_datetime'] = $inv_datetime;

			$bind_inv_val = $params;

			$insert_into_invoice_tbl = $db_sp->into_invoice_tbl($inv_val_pl_holders,$bind_inv_val,$pdo);
			//$insert_into_invoice_tbl = $query->insert($invoice_tbl,$inv_keys,$inv_val_pl_holders,$bind_inv_val,$pdo);

			$inv_id = $query->last_insert_id($pdo);
		  	$inv_no = $query->invoice_no($pdo);


	  		for ($count=0; $count < $_POST['no_of_orders'] ; $count++) {

	  			$params = [];
	  			$params['inv_id'] = $inv_id;
	  			$params['order_item'] = addslashes($_POST['order_item'][$count]);
	  			$params['item_id'] = $_POST['item_id'][$count];
	  			$params['prev_order_qty'] = 0;
	  			$params['order_qty'] = intval(trim($_POST['order_qty'][$count]));
	  			$params['qty_type'] = $_POST['qty_type'][$count];
	  			$params['order_price'] = floatval(trim($_POST['order_price'][$count]));
	  			$params['order_amt'] = floatval(trim($_POST['order_amt'][$count]));
	  			$params['order_date'] = $inv_date;
	  			$params['order_time'] = $inv_time;
	  			$params['order_datetime'] = $inv_datetime;

	  			$bind_orders_vals = $params;

	  			$insert_into_orders_tbl = $db_sp->into_orders_tbl($orders_pl_holders,$bind_orders_vals,$pdo);
	  			//$insert_into_orders_tbl = $query->insert($orders_tbl,$orders_tbl_keys,$orders_pl_holders,$bind_orders_vals,$pdo);

	  			if ($_POST['qty_type'][$count] == 'pcs') {

	  				$rtl_key = "rtl_curr_stock";
	  				$query_condtn = "WHERE item_id = '".$_POST['item_id'][$count]."'";
	  				$rtl_curr_stock = $query->select_single_no_bind($rtl_key,$rtl_stock_tbl,$query_condtn,$pdo);

	  				//return with datas if order_qty is > rtl_curr_stock
	  				if (intval(trim($_POST['order_qty'][$count])) > floatval(trim($rtl_curr_stock))) {
	  					$return['item_id'] = $_POST['item_id'][$count];
	  					$return['order_qty'] = $_POST['order_qty'][$count];
	  					$return['qty_type'] = $_POST['qty_type'][$count];
	  					echo json_encode($return);
	  					return;
	  				}
					$rtl_key_vals = "rtl_to_cust_tdy = rtl_to_cust_tdy - :order_qty,
									rtl_out_tdy = rtl_out_tdy - :order_qty,
									rtl_curr_stock = rtl_curr_stock - :order_qty
										";
					$query_condtn = "item_id = :item_id";

					$params = [];
					$params['order_qty'] = intval(trim($_POST['order_qty'][$count]));
					$params['item_id'] = $_POST['item_id'][$count];
					$bind_rtl_vals = $params;

					$update_rtl_stock_tbl = $query->update($rtl_stock_tbl,$rtl_key_vals,$query_condtn,$bind_rtl_vals,$pdo);
	  			}

	  			if ($_POST['qty_type'][$count] == 'ctn') {

	  				$wh_key = "wh_curr_stock";
	  				$query_condtn = "WHERE item_id = '".$_POST['item_id'][$count]."'";
	  				$wh_curr_stock = $query->select_single_no_bind($wh_key,$wh_stock_tbl,$query_condtn,$pdo);

	  				//return with datas if order_qty is > wh_curr_stock
	  				if (intval(trim($_POST['order_qty'][$count])) > floatval(trim($wh_curr_stock))) {
	  					$return['item_id'] = $_POST['item_id'][$count];
	  					$return['order_qty'] = $_POST['order_qty'][$count];
	  					$return['qty_type'] = $_POST['qty_type'][$count];
	  					echo json_encode($return);
	  					return;
	  				}

					$wh_key_vals ="
									wh_out_tdy = wh_out_tdy - :order_qty,
									wh_to_cust_tdy = wh_to_cust_tdy - :order_qty,
									wh_to_cust_since_strt = wh_to_cust_since_strt - :order_qty,
									wh_out_since_strt = wh_out_since_strt - :order_qty,
									wh_curr_stock = wh_curr_stock - :order_qty
									";
					$query_condtn = "item_id = :item_id";

					$params = [];
					$params['order_qty'] = floatval(trim($_POST['order_qty'][$count]));
					$params['item_id'] = $_POST['item_id'][$count];

					$bind_wh_vals = $params;
					//echo var_dump($bind_wh_val).'<br>';
					$update_wh_stocks_tbl = $query->update($wh_stock_tbl,$wh_key_vals,$query_condtn,$bind_wh_vals,$pdo);
				}
			}

			$inv_key_val = "inv_no = :inv_no";		
			$query_condtn = "inv_id = :inv_id";

			$params = [];
			$params["inv_no"] = $inv_no;
			$params["inv_id"] = $inv_id;

			$bind_inv_val = $params;

			$update_invoice_record = $query->update($invoice_tbl,$inv_key_val,$query_condtn,$bind_inv_val,$pdo);
			//echo var_dump($bind_inv_val).'<br>';
			
			//$pay_keys 	= "inv_id, pay_amt, any_outstanding, outstanding_amt, pay_date, pay_time, pay_datetime";

			$pay_pl_holders = ":inv_id, :paid_by, :paid_to, :pay_amt, :any_outstanding, :outstanding_amt, :pay_type, :pay_date, :pay_time, :pay_datetime";

			$params = [];
			$params["inv_id"] = $inv_id;
			$params["paid_by"] = '';
			$params["paid_to"] = '';
			$params["pay_amt"] = 0;
			$params["any_outstanding"] = "yes";
			$params["outstanding_amt"] = floatval(trim($inv_fnl_ttl));
			$params["pay_type"] = 'null';
			$params["pay_date"] = $inv_date;
			$params["pay_time"] = $inv_time;
			$params["pay_datetime"] = $inv_datetime;

			$bind_pay_vals = $params;
			//$insert_pay_record = $query->insert($pay_tbl,$pay_keys,$pay_pl_holders,$bind_pay_vals,$pdo);
			$insert_pay_record = $db_sp->into_payments_tbl($pay_pl_holders,$bind_pay_vals,$pdo);
			//echo var_dump($bind_pay_val).'<br>';
		if (isset($update_invoice_record) && isset($insert_pay_record)) {
			$pdo->commit();

			echo "Invoice created sucessfully!";
		}
		else {
			$pdo->rollBack();
			echo "Unable to Create Invoice";
		}
	}
/*..............................Edit Invoice................................*/
	if ($_POST['invoice_action'] == 'edit_invoice') {

		$inv_no = $_POST['inv_no'];
		$sales_person = $_SESSION['user_name'];
		$inv_date = trim($_POST['inv_date']);
		$inv_time = trim($_POST['inv_time']);
		$inv_datetime = trim($_POST['inv_datetime']);
		$inv_sub_ttl = floatval(trim($_POST['inv_sub_ttl']));
		$inv_dsc_ttl = floatval(trim($_POST['inv_dsc_ttl']));
		$inv_fnl_ttl = floatval(trim($_POST['inv_fnl_ttl']));
		//store the inv_id
		$inv_id = $_POST['invoice_id'];

		$pdo->beginTransaction();

		$condtn = "WHERE inv_id = '".$inv_id."'";
		$select_prev_orders = $query->select_assoc_no_bind($orders_tbl_keys,$orders_tbl,$condtn,$pdo);

		foreach ($select_prev_orders as $prev_order) {
			//re-insert previous orders into stocks tbl
			if ($prev_order['qty_type'] == 'pcs') {
				//update rtl_stock_tbl
		  		$rtl_key_vals = "rtl_ins_tdy = rtl_ins_tdy + :order_qty,
		  					rtl_curr_stock = rtl_curr_stock + :order_qty";
		  		$query_condtn = "item_id = :item_id";
		  		$params = [];
				$params['order_qty'] = $prev_order['order_qty'];
				$params['item_id'] = $prev_order['item_id'];
			  	$bind_rtl_vals = $params;
			  	
			  	$query->update($rtl_stock_tbl,$rtl_key_vals,$query_condtn,$bind_rtl_vals,$pdo);
			}

			if ($prev_order['qty_type'] == 'ctn') {
				//update wh_stock_tbl
		  		$wh_key_vals = "wh_ins_since_strt = wh_ins_since_strt + :order_qty,
		  						wh_ins_tdy = wh_ins_tdy + :order_qty,
		  						wh_curr_stock = wh_curr_stock + :order_qty";
		  		$query_condtn = "item_id = :item_id";
		  		$params = [];
				$params['order_qty'] = $prev_order['order_qty'];
				$params['item_id'] = $prev_order['item_id'];
			  	$bind_wh_vals = $params;
			  	
			  	$query->update($wh_stock_tbl,$wh_key_vals,$query_condtn,$bind_wh_vals,$pdo);		  				
		  	}
		}

		//for new orders in edited invoice
		for ($count=0; $count < $_POST['no_of_orders'] ; $count++){
			//find matches in prev orders 
			$match_these_keys = "inv_id, item_id, order_item, order_qty, qty_type";

	  		$match_condtn = "WHERE inv_id = '".$inv_id."'
	  					AND item_id = '".$_POST['item_id'][$count]."'
	  					AND qty_type = '".$_POST['qty_type'][$count]."'";

		  	$match_from_prev_orders = $query->select_assoc_no_bind($match_these_keys,$orders_tbl,$match_condtn,$pdo);

		  	$num_of_match = $query->num_rows;		  	

		  	$order_qty = intval(trim($_POST['order_qty'][$count]));
		  	$rtl_ins_tdy = 0;
		  	$rtl_to_cust_tdy = $order_qty;
		  	$rtl_out_tdy = $order_qty;
		  	$wh_ins_tdy = 0;
		  	$wh_out_tdy = $order_qty;
		  	$wh_ins_since_strt = 0;
		  	$wh_to_cust_since_strt = $order_qty;
		  	$wh_to_cust_tdy = $order_qty;
			$wh_out_since_strt = $order_qty;
			
			if ($num_of_match == 1) {
				//where orders match,
			  	foreach ($match_from_prev_orders as $matched_order) {
			  		$key_vals_to_update = "
		  									prev_order_qty = order_qty,
		  									order_qty = :order_qty,
		  									order_price = :order_price,
		  									order_amt = :order_amt,
		  									order_date = :order_date,
		  									order_time = :order_time,
		  									order_datetime = :order_datetime
		  									";

		  			$update_condtn = " inv_id = :inv_id 
										AND (item_id = :item_id
										AND qty_type = :qty_type)
									";

			  		$params = [];
			  		$params['inv_id'] = $inv_id;
			  		$params['item_id'] = $_POST['item_id'][$count];
			  		$params['order_qty'] = intval(trim($_POST['order_qty'][$count]));
			  		$params['qty_type'] = $_POST['qty_type'][$count];
			  		$params['order_price'] = $_POST['order_price'][$count];
			  		$params['order_amt'] = $_POST['order_amt'][$count];
			  		$params['order_date'] = $curr_date;
			  		$params['order_time'] = $curr_time;
			  		$params['order_datetime'] = $curr_datetime;

					$bind_updates = $params;
					//update prev order where match
			  		$update_prev_order = $query->update($orders_tbl,$key_vals_to_update,$update_condtn,$bind_updates,$pdo);

			  		$qty_diff = intval(trim($_POST['order_qty'][$count])) - $matched_order['order_qty'];

			  		if ($_POST['qty_type'][$count] == 'pcs') {
			  			//in rtl_stock_tbl,
			  			$keys = "rtl_tdy_strt_date,
	  							rtl_tdy_strt_time,
	  							rtl_to_cust_tdy,
	  							rtl_out_tdy,
	  							rtl_curr_stock";
	  					$condtn = "WHERE item_id = ".$_POST['item_id'][$count];
			  			$rtl_stocks = $query->select_assoc_no_bind($keys,$rtl_stock_tbl,$condtn,$pdo);
			  			foreach ($rtl_stocks as $rtl) {
			  				//return with datas if order_qty is > rtl_curr_stock
			  				if (intval(trim($_POST['order_qty'][$count])) > floatval(trim($rtl['rtl_curr_stock']))) {
			  					$return['item_id'] = $_POST['item_id'][$count];
			  					$return['order_qty'] = $_POST['order_qty'][$count];
			  					$return['qty_type'] = $_POST['qty_type'][$count];
			  					echo json_encode($return);
			  					return;
			  				}

			  				if ($curr_date >= $rtl['rtl_tdy_strt_date'] && $curr_time > $rtl['rtl_tdy_strt_time']) {
			  					$order_qty = $order_qty;
			  					$rtl_to_cust_tdy = $qty_diff;
			  					$rtl_out_tdy = $qty_diff;		
			  					$rtl_ins_tdy = $matched_order['order_qty'];
			  					if ($rtl['rtl_out_tdy'] >= 0 && $qty_diff <= 0) {
			  						$rtl_ins_tdy = $order_qty;
			  						$rtl_out_tdy = 0;
			  						$rtl_to_cust_tdy = 0;
			  					}
			  				}
			  			}
			  		}
			  		if ($_POST['qty_type'][$count] == 'ctn') {
			  			//in wh_stocks tbl
			  			$keys = "wh_strt_datetime,
			  					wh_out_tdy,
			  					wh_to_cust_tdy,
			  					wh_to_cust_since_strt,
			  					wh_out_since_strt,
			  					wh_curr_stock";
	  					$condtn = "WHERE item_id = ".$_POST['item_id'][$count];
			  			$wh_stocks = $query->select_assoc_no_bind($keys,$wh_stock_tbl,$condtn,$pdo);
			  			foreach ($wh_stocks as $whs) {
			  				//return with datas if order_qty is > wh_curr_stock
			  				if (intval(trim($_POST['order_qty'][$count])) > floatval(trim($whs['wh_curr_stock']))) {
			  					$return['item_id'] = $_POST['item_id'][$count];
			  					$return['order_qty'] = $_POST['order_qty'][$count];
			  					$return['qty_type'] = $_POST['qty_type'][$count];
			  					echo json_encode($return);
			  					return;
			  				}
			  				
			  				if ($curr_datetime > $whs['wh_strt_datetime']) {

			  					$order_qty = $order_qty;
			  					$wh_ins_tdy = $matched_order['order_qty'];
			  					$wh_ins_since_strt = $matched_order['order_qty'];
			  					$wh_to_cust_tdy = $qty_diff;
				  				$wh_out_tdy = $qty_diff;
			  					$wh_to_cust_since_strt = $qty_diff;
			  					$wh_out_since_strt = $qty_diff;
			  					if ($whs['wh_to_cust_since_strt'] >= 0 && $qty_diff <= 0) {
			  						$wh_to_cust_since_strt = 0;
			  						
			  					}
			  					if ($whs['wh_out_since_strt'] >= 0 && $qty_diff <= 0) {
			  						$wh_ins_since_strt = $order_qty;
			  						$wh_out_since_strt = 0;
			  					}

				  				if ($qty_diff <= 0) {
				  					if ($whs['wh_out_tdy'] >= 0) {
				  						$wh_ins_tdy = $order_qty;
				  						$wh_out_tdy = 0;
				  					}
				  					if ($whs['wh_to_cust_tdy'] >= 0) {
				  						$wh_to_cust_tdy = 0;
				  					}
				  				}
			  				}
			  			}
			  		}			  				
			  	}			  					  			
		  	}
			else{
			    
			    if ($_POST['qty_type'][$count] == 'pcs') {

					$keys = "rtl_curr_stock";
		  			$condtn = "WHERE item_id = ".$_POST['item_id'][$count];
				  	$rtl = $query->select_single_no_bind($keys,$rtl_stock_tbl,$condtn,$pdo);
				  	if (intval(trim($_POST['order_qty'][$count])) > floatval(trim($rtl))) {
				  		$return['item_id'] = $_POST['item_id'][$count];
				  		$return['order_qty'] = $_POST['order_qty'][$count];
				  		$return['qty_type'] = $_POST['qty_type'][$count];
				  		echo json_encode($return);
				  		return;
				  	}
				}

			  	if ($_POST['qty_type'][$count] == 'ctn') {
			  		//in wh_stocks tbl
			  		$keys = "wh_curr_stock";
	  				$condtn = "WHERE item_id = ".$_POST['item_id'][$count];
			  		$whs = $query->select_single_no_bind($keys,$wh_stock_tbl,$condtn,$pdo);
			  		if (intval(trim($_POST['order_qty'][$count])) > floatval(trim($whs))) {
			  			$return['item_id'] = $_POST['item_id'][$count];
			  			$return['order_qty'] = $_POST['order_qty'][$count];
			  			$return['qty_type'] = $_POST['qty_type'][$count];
			  			echo json_encode($return);
			  			return;
			  		}
			  	}
			  	
				//if new order does not find match
				$params = [];
		  		$params['inv_id'] = $inv_id;
		  		$params['order_item'] = addslashes($_POST['order_item'][$count]);
		  		$params['item_id'] = $_POST['item_id'][$count];
		  		$params['prev_order_qty'] = 0;
		  		$params['order_qty'] = intval(trim($_POST['order_qty'][$count]));
		  		$params['qty_type'] = $_POST['qty_type'][$count];
		  		$params['order_price'] = floatval(trim($_POST['order_price'][$count]));
		  		$params['order_amt'] = floatval(trim($_POST['order_amt'][$count]));
		  		$params['order_date'] = $curr_date;
		  		$params['order_time'] = $curr_time;
		  		$params['order_datetime'] = $curr_datetime;
		  		$bind_orders_vals = $params;
		  		//insert the new order where no match
			  	$insert_new_order = $query->insert($orders_tbl,$orders_tbl_keys,$orders_pl_holders,$bind_orders_vals,$pdo);
			}

		  	if ($_POST['qty_type'][$count] == 'pcs') {
		  		//re-update rtl_stock_tbl
		  		$rtl_key_vals = "rtl_ins_tdy = rtl_ins_tdy - :rtl_ins_tdy,
		  					rtl_to_cust_tdy = rtl_to_cust_tdy - :rtl_to_cust_tdy,
		  					rtl_out_tdy = rtl_out_tdy - :rtl_out_tdy,
		  					rtl_curr_stock = rtl_curr_stock - :order_qty";
		  		$query_condtn = "item_id = :item_id";
		  		$params = [];
			  	$params['rtl_ins_tdy'] = $rtl_ins_tdy;
			  	$params['rtl_to_cust_tdy'] = $rtl_to_cust_tdy;
			  	$params['rtl_out_tdy'] = $rtl_out_tdy;
				$params['order_qty'] = $order_qty;
				$params['item_id'] = $_POST['item_id'][$count];
			  	$bind_rtl_vals = $params;
			  	$query->update($rtl_stock_tbl,$rtl_key_vals,$query_condtn,$bind_rtl_vals,$pdo);
		  				
		  	}

		  	if ($_POST['qty_type'][$count] == 'ctn') {
		  		//re-update wh_stock tbl
		  		$wh_key_vals = "wh_ins_tdy = wh_ins_tdy - :wh_ins_tdy,
		  					wh_ins_since_strt = wh_ins_since_strt - :wh_ins_since_strt,
		  					wh_out_tdy = wh_out_tdy - :wh_out_tdy,
		  					wh_to_cust_tdy = wh_to_cust_tdy - :wh_to_cust_tdy,
		  					wh_to_cust_since_strt = wh_to_cust_since_strt - :wh_to_cust_since_strt,
		  					wh_out_since_strt = wh_out_since_strt - :wh_out_since_strt,
		  					wh_curr_stock = wh_curr_stock - :order_qty";
		  		$query_condtn = "item_id = :item_id";
		  		$params = [];
			  	$params['wh_ins_tdy'] = $wh_ins_tdy;
			  	$params['wh_ins_since_strt'] = $wh_ins_since_strt;
			  	$params['wh_out_tdy'] = $wh_out_tdy;
			  	$params['wh_to_cust_tdy'] = $wh_to_cust_tdy;
			 	$params['wh_to_cust_since_strt'] = $wh_to_cust_since_strt;
			  	$params['wh_out_since_strt'] = $wh_out_since_strt;
				$params['order_qty'] = $order_qty;
				$params['item_id'] = $_POST['item_id'][$count];
			  	$bind_wh_vals = $params;
			  	$query->update($wh_stock_tbl,$wh_key_vals,$query_condtn,$bind_wh_vals,$pdo);		  				
		  	}
	  	}

	  	$query_condtn = "inv_id = :inv_id AND order_datetime NOT BETWEEN DATE_SUB( NOW(), INTERVAL 10 SECOND) AND NOW()";
		$param = [];
		$param['inv_id'] = $inv_id;
		$bind_order_val = $param;
		//delete prev_inv_orders
		$delete_prev_inv_orders = $query->delete($orders_tbl,$query_condtn,$bind_order_val,$pdo);

		$inv_key_vals = "inv_no = :inv_no,
							inv_date = :inv_date,
							inv_time = :inv_time,
							cust_name = :cust_name,
							sales_person = :sales_person,
							inv_sub_ttl = :inv_sub_ttl,
							inv_dsc_ttl = :inv_dsc_ttl,
							inv_fnl_ttl = :inv_fnl_ttl,
							inv_datetime = :inv_datetime
							";		
		$query_condtn = "inv_id = :inv_id";
			
		$params = [];
		$params["inv_no"] = $inv_no;
		$params['inv_date'] = $curr_date;
		$params['inv_time'] = $curr_time;
		$params['cust_name'] = trim($_POST['cust_name']);
		$params['sales_person'] = $_SESSION['user_name'];				
		$params['inv_sub_ttl'] = floatval(trim($_POST['inv_sub_ttl']));
		$params['inv_dsc_ttl'] = floatval(trim($_POST['inv_dsc_ttl']));
		$params['inv_fnl_ttl'] = floatval(trim($_POST['inv_fnl_ttl']));
		$params['inv_datetime'] = $curr_datetime;
		$params["inv_id"] = $inv_id;
		$bind_inv_vals = $params;

		$update_invoice_dtl = $query->update($invoice_tbl,$inv_key_vals,$query_condtn,$bind_inv_vals,$pdo);

		$pay_key_vals = "outstanding_amt = :outstanding_amt";
		$query_condtn = "inv_id = :inv_id AND pay_amt = :pay_amt";
		$params = [];
		$params['inv_id'] = $inv_id;
		$params['outstanding_amt'] = floatval(trim($_POST['inv_fnl_ttl']));
		$params['pay_amt'] = 0;
		$bind_pay_vals = $params;
		$update_payments_record = $query->update($pay_tbl,$pay_key_vals,$query_condtn,$bind_pay_vals,$pdo);

		if (isset($update_invoice_dtl) && isset($update_payments_record)) {
			$pdo->commit();
			echo 'Invoice edited sucessfully!';
		}
		else {
			$pdo->rollBack();
			echo "Unable to edit invoice!";
		}
	}		
}










?>