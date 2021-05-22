<?php
include 'my_db.class.php';


if (isset($_POST['fetch_tbl'])) {

	$draw = $_POST['draw'];
	$row = $_POST['start'];
	$row_per_page = $_POST['length'];
	if (isset($_POST['order'])) {
		$column_index = $_POST['order']['0']['column'];
		$column_name =  $_POST['columns'][$column_index]['data'];
		$column_sort_order = $_POST['order']['0']['dir'];
	}
	
	$search_val = addslashes(trim($_POST['search']['value']));

	$search_array = [];
	$cols = "*";
	//$query_condtn = "";
	if ($_POST['fetch_tbl'] == 'users_tbl') {
		$tbl="users_tbl";
		if ($_SESSION['type'] == 'master_admin') {
			$query_condtn = "WHERE user_type <> 'master_admin'";
		}
		else{
			$query_condtn = "WHERE user_type = 'user'";
		}

		$query->select_assoc_no_bind($cols,$tbl,$query_condtn,$pdo);
		$num_ttl_rows = $query->num_rows;


		if ($search_val != "") {
			$query_condtn .= " AND (user_email LIKE :user_email";
			//searches value in user_name column;
			$query_condtn .=" OR user_name LIKE :user_name";
			$query_condtn .=" OR user_mobile_no LIKE :user_mobile_no";

			//searches value in user_status column;
			$query_condtn .=" OR user_status LIKE :user_status)";

			$search_array = [
						'user_email'=> "%$search_val%",
						'user_name'=> "%$search_val%",
						'user_status'=> "%$search_val%"
					];
		}
		if (isset($column_sort_order)) {
			$sort_columns = ["user_id","user_name","user_mobile_no","user_email","user_type","user_status"];
			$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
		}
		else {
			$query_condtn .= " ORDER BY user_id DESC";
		}

		if ($row_per_page != -1) { //when page loads this condition will be true
				
			//********will display datas on the page
			$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
		}

		$query_result = $query->select_assoc_bind($cols,$tbl,$query_condtn,$search_array,$pdo);
		$num_filtered_rows = $query->num_rows;

		foreach ($query_result as $row) {
			 //will store user_status data
			$status = '';
			if ($row['user_status'] == 'active') {
				$status = '<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input status" id="'.$row['user_id'].'"  data-status="'.$row['user_status'].'" checked style="z-index: 0;">
				<label class="custom-control-label" for="'.$row['user_id'].'"><span class="badge badge-success style="z-index: 0;"">Active</span></label>
				</div>';
			}
			else {
				$status = '<div class="custom-control custom-switch">
				<input type="checkbox" class="custom-control-input status" id="'.$row['user_id'].'"  data-status="'.$row['user_status'].'" style="z-index: 0;">
				<label class="custom-control-label" for="'.$row['user_id'].'"><span class="badge badge-danger" style="z-index: 0;">Inactive</span></label>
				</div>';
			}
			$type = '';
			if ($row['user_type'] == 'master_admin') {
				$type = 'Master Admin';
			}
			else if($row['user_type'] == 'admin'){
				$type = 'Admin';
			}
			else{
				$type = 'User';
			}
			$sub_array = [];
			$sub_array[] = $row['user_id'];
			$sub_array[] = $row['user_name'];
			$sub_array[] = $row['user_mobile_no'];
			$sub_array[] = $row['user_email'];
			$sub_array[] = $type;
			$sub_array[] = $status; //store user_status
			//create update data button and store in $sub_array variable
			$sub_array[] = '<button type="button" name="update" id="'.$row['user_id'].'" class="btn btn-warning btn-sm rounded-0 update">Update</button>';
			//also create and store delete button
			$sub_array[] = '<button type="button" name="delete" id="'.$row['user_id'].'" class="btn btn-danger btn-sm rounded-0 delete" data-status="'.$row['user_status'].'">Delete</button>';

			//store $sub_array into $data array
			$data[] = $sub_array;
		}
	}





	if ($_POST['fetch_tbl'] == 'rtl_stock_tbl') {

		$joint_keys = "itm.item_id, itm.item_name,
				itm.unit_per_ctn, rtl.rtl_tdy_strt_time,
				rtl.rtl_strt_stock_tdy, rtl.rtl_curr_stock, rtl.rtl_ins_tdy, rtl.rtl_out_tdy";

		$joint_tbl=   "goods_items_tbl itm
				JOIN rtl_stock_tbl rtl ON itm.item_id = rtl.item_id";
		$query_condtn = "";

		$query->select_assoc_no_bind($joint_keys,$joint_tbl,$query_condtn,$pdo);
		$num_ttl_rows = $query->num_rows;


		if ($search_val != "") {
			$query_condtn .= " WHERE item_name LIKE :item_name";

			$search_array = [
						'item_name'=> "%$search_val%"
					];
		}
		if (isset($column_sort_order)) {
			$sort_columns = ["item_name","rtl_tdy_strt_time","rtl_strt_stock_tdy","rtl_ins_tdy","rtl_out_tdy", "rtl_curr_stock","unit_per_ctn"];
			$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
		}
		else {
			$query_condtn .= " ORDER BY item_id DESC";
		}

		if ($row_per_page != -1) { //when page loads this condition will be true
				
			//********will display datas on the page
			$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
		}

		$query_result = $query->select_assoc_bind($joint_keys,$joint_tbl,$query_condtn,$search_array,$pdo);
		$num_filtered_rows = $query->num_rows;

		foreach ($query_result as $row) {
			$sub_array = [];
			$sub_array[] = $row['item_name'];
			$sub_array[] = $row['rtl_tdy_strt_time'];
			$sub_array[] = $row['rtl_strt_stock_tdy'].' pcs';
			$sub_array[] = $row['rtl_ins_tdy'];
			$sub_array[] = $row['rtl_out_tdy'];
			$sub_array[] = $row['rtl_curr_stock'].' pcs';
			$sub_array[] = $row['unit_per_ctn'].'pcs/ctn';
			//create update data button and store in $sub_array variable
			$sub_array[] = '<button type="button" name="update_goods" id="'.$row['item_id'].'" class="btn btn-warning btn-sm update_goods">Update</button>';

			//store $sub_array into $data array
			$data[] = $sub_array;
		}
	}

	if ($_POST['fetch_tbl'] == 'wh_stock_tbl') {
		
		$joint_keys = "itm.item_id, itm.item_name,wh.wh_ins_tdy,wh.wh_out_tdy,
				wh.wh_strt_datetime,wh.wh_strt_stock, wh.wh_ins_since_strt, wh.wh_out_since_strt, wh.wh_curr_stock";

		$joint_tbl =   "goods_items_tbl itm
				LEFT JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id";
		$query_condtn = "";

		$query->select_assoc_no_bind($joint_keys,$joint_tbl,$query_condtn,$pdo);
		$num_ttl_rows = $query->num_rows;

		if ($search_val != "") {
			$query_condtn .= " WHERE item_name LIKE :item_name";

			$search_array = [
						'item_name'=> "%$search_val%"
					];
		}
		if (isset($column_sort_order)) {
			$sort_columns = ["item_name","wh_ins_tdy","wh_out_tdy","wh_strt_datetime", "wh_strt_stock" ,"wh_ins_since_strt","wh_out_since_strt", "wh_curr_stock"];
			$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
		}
		else {
			$query_condtn .= " ORDER BY item_id DESC";
		}

		if ($row_per_page != -1) { //when page loads this condition will be true
				
			//********will display datas on the page
			$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
		}

		$query_result = $query->select_assoc_bind($joint_keys,$joint_tbl,$query_condtn,$search_array,$pdo);
		$num_filtered_rows = $query->num_rows;

		foreach ($query_result as $row) {
			$sub_array = [];
			$sub_array[] = $row['item_name'];
			$sub_array[] = $row['wh_ins_tdy'];
			$sub_array[] = $row['wh_out_tdy'];
			$sub_array[] = date_format(date_create($row['wh_strt_datetime']),"d/m/y H:i:s");
			$sub_array[] = $row['wh_strt_stock'].' ctns';
			$sub_array[] = $row['wh_ins_since_strt'];
			$sub_array[] = $row['wh_out_since_strt'];
			$sub_array[] = $row['wh_curr_stock'].' ctns';
			//$sub_array[] = '<s>N</s>'.$row['ctn_price'];

			$sub_array[] = '<button type="button" name="view_edit" id="'.$row['item_id'].'" class="btn btn-primary btn-sm view_edit"><i class="far fa-eye"></i></button>';

			//store $sub_array into $data array
			$data[] = $sub_array;
		}
	}
	
	if ($_POST['fetch_tbl'] == 'sales_tbl') {
		
		$joint_keys = "inv.*,pay.pay_amt,pay.outstanding_amt,pay.any_outstanding";

		$joint_tbl =   "sales_inv_tbl inv
				LEFT JOIN
					(SELECT inv_id, SUM(pay_amt) AS pay_amt, MIN(outstanding_amt) AS outstanding_amt, any_outstanding
					FROM payments
					GROUP BY inv_id, any_outstanding) pay
				ON inv.inv_id = pay.inv_id";
		$query_condtn = "";
		$db_sp->get_invoice_and_payments($pdo);
		$num_ttl_rows = $db_sp->num_rows;

		//$query->select_assoc_no_bind($joint_keys,$joint_tbl,$query_condtn,$pdo);
		//$num_ttl_rows = $query->num_rows;
		
		if ($search_val != "") {
			$query_condtn .= " WHERE inv_no LIKE :search_val
								OR inv_date LIKE :search_val
								OR cust_name LIKE :search_val
								OR sales_person LIKE :search_val";

			$search_array = [
						'search_val'=> "%$search_val%"
					];
		}

		if (isset($column_sort_order)) {
			$sort_columns = ["inv_no","inv_date","cust_name","sales_person", "inv_sub_ttl" ,"inv_dsc_ttl","inv_fnl_ttl", "pay.any_outstanding"];
			$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
		}
		else {
			$query_condtn .= " ORDER BY inv.inv_id DESC";
		}

		if ($row_per_page != -1) { //when page loads this condition will be true
				
			//********will display datas on the page
			$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
		}

		$query_result = $query->select_assoc_bind($joint_keys,$joint_tbl,$query_condtn,$search_array,$pdo);
		$num_filtered_rows = $query->num_rows;
		if ($num_ttl_rows > 0) {
			foreach ($query_result as $row) {
				if ($row['any_outstanding'] == 'no') {
					$status = '<span>Paid</span><br><span class="badge badge-success"><s>N</s>'.number_format($row['pay_amt'],'2').'</span>';
				}
				elseif ($row['any_outstanding'] == 'yes' && $row['pay_amt'] != 0) {
					$status = '<span>Outstanding</span><br><span class="badge badge-warning"><s>N</s>'.number_format($row['outstanding_amt'],'2').'</span>';
				}
				else{
					$status = '<span>Not paid</span><br><span class="badge badge-danger"><s>N</s>'.number_format($row['inv_fnl_ttl'],'2').'</span>';
				}

				$sub_array = [];
				$sub_array[] = $row['inv_no'];
				$sub_array[] = date_format(date_create($row['inv_date']),"d/m/y");
				$sub_array[] = $row['cust_name'];
				$sub_array[] = $row['sales_person'];
				$sub_array[] = '<s>N</s>'.number_format($row['inv_sub_ttl'],'2');
				$sub_array[] = '<s>N</s>'.number_format($row['inv_dsc_ttl'],'2');
				$sub_array[] = '<s>N</s>'.number_format($row['inv_fnl_ttl'],'2');
				$sub_array[] = $status;

				$sub_array[] = '<button type="button" id="'.$row['inv_id'].'" class="pay btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#pay_modal">Pay</button>';
				$sub_array[] = '<a href="print_invoice.php?pdf=1&id='.$row['inv_id'].'" class="btn btn-outline-secondary btn-sm" title="print"><i class="fas fa-print"></i>';
				$sub_array[] = '<a href="sales_invoice.php?update=1&id='.$row['inv_id'].'" class="btn btn-outline-info btn-sm"><i class="far fa-edit"></i>';
				$sub_array[] = '<a href="'.$_SERVER['PHP_SELF'].'?delete=1&id='.$row['inv_id'].'" class="btn btn-outline-danger btn-sm delete"><i class="fas fa-trash-alt"></i>';


				//store $sub_array into $data array
				$data[] = $sub_array;
			}
		}		
	}

	if ($_POST['fetch_tbl'] == 'payments_tbl') {
		$tbl="payments pay LEFT JOIN sales_inv_tbl inv ON pay.inv_id = inv.inv_id";
		$cols = "pay.pay_id,inv.inv_no,pay.paid_by,pay.paid_to,pay.pay_amt,pay.pay_type,pay.pay_datetime";

		$query_condtn = "WHERE pay.pay_amt <> '0'";

		$query->select_assoc_no_bind($cols,$tbl,$query_condtn,$pdo);
		$num_ttl_rows = $query->num_rows;


		if ($search_val != "") {
			$query_condtn .= " AND (inv.inv_no LIKE :inv_no";
			$query_condtn .= " OR paid_by LIKE :paid_by";
			//searches value in user_name column;
			$query_condtn .=" OR paid_to LIKE :paid_to";
			//searches value in user_status column;
			$query_condtn .=" OR pay_type LIKE :pay_type";

			$query_condtn .=" OR pay_datetime LIKE :pay_datetime)";


			$search_array = [
						'inv_no'=> "%$search_val%",
						'paid_by'=> "%$search_val%",
						'paid_to'=> "%$search_val%",
						'pay_type'=> "%$search_val%",
						'pay_datetime'=> "%$search_val%"
					];
		}
		if (isset($column_sort_order)) {
			$sort_columns = ["pay_datetime","inv_no","paid_by","paid_to","pay_amt","pay_type"];
			$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
		}
		else {
			$query_condtn .= " ORDER BY pay_datetime DESC";
		}

		if ($row_per_page != -1) { //when page loads this condition will be true
				
			//********will display datas on the page
			$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
		}

		$query_result = $query->select_assoc_bind($cols,$tbl,$query_condtn,$search_array,$pdo);
		$num_filtered_rows = $query->num_rows;

		foreach ($query_result as $row) {

			$sub_array = [];
			$sub_array[] = date_format(date_create($row['pay_datetime']),"d/m/y H:i:s");
			$sub_array[] = $row['inv_no'];
			$sub_array[] = $row['paid_by'];
			$sub_array[] = $row['paid_to'];
			$sub_array[] = '<s>N</s>'.number_format($row['pay_amt'],'2');
			$sub_array[] = $row['pay_type'];
			//also create and store delete button
			$sub_array[] = '<button type="button" name="delete" id="'.$row['pay_id'].'" class="btn btn-outline-danger btn-sm delete"><i class="fas fa-trash-alt"></i></button>';

			//store $sub_array into $data array
			$data[] = $sub_array;
		}
	}

	if ($_POST['fetch_tbl'] == 'goods_itm_list') {

		$joint_keys = "itm.item_id, itm.item_name, itm.unit_per_ctn,  itm.ctn_price, itm.unit_price,
				itm.purchase_price, wh.wh_curr_stock, rtl.rtl_curr_stock
				";

		$joint_tbl=   "goods_items_tbl itm
				JOIN wh_stock_tbl wh ON  itm.item_id = wh.item_id
				JOIN rtl_stock_tbl rtl ON  itm.item_id = rtl.item_id";
		$query_condtn = "";

		$query->select_assoc_no_bind($joint_keys,$joint_tbl,$query_condtn,$pdo);
		$num_ttl_rows = $query->num_rows;


		if ($search_val != "") {
			$query_condtn .= " WHERE item_name LIKE :item_name";

			$search_array = [
						'item_name'=> "%$search_val%"
					];
		}
		if (isset($column_sort_order)) {
			$sort_columns = ["item_name","wh_curr_stock","rtl_curr_stock","unit_per_ctn","ctn_price","unit_price","purchase_price"];
			$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
		}
		else {
			$query_condtn .= " ORDER BY item_id DESC";
		}

		if ($row_per_page != -1) { //when page loads this condition will be true
				
			//********will display datas on the page
			$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
		}

		$query_result = $query->select_assoc_bind($joint_keys,$joint_tbl,$query_condtn,$search_array,$pdo);
		$num_filtered_rows = $query->num_rows;

		foreach ($query_result as $row) {
			$sub_array = [];
			$sub_array[] = $row['item_name'];
			$sub_array[] = $row['wh_curr_stock'].' ctns';
			$sub_array[] = $row['rtl_curr_stock'].' pcs';
			$sub_array[] = $row['unit_per_ctn'].' pcs/ctn';
			$sub_array[] = '<s>N</s> '.number_format($row['ctn_price']);
			$sub_array[] = '<s>N</s> '.number_format($row['unit_price']);
			$sub_array[] = '<s>N</s> '.number_format($row['purchase_price']);
			//create update data button and store in $sub_array variable
			$sub_array[] = '<button type="button" name="update_goods_item" id="'.$row['item_id'].'" class="btn btn-info btn-sm update_goods_item">Update</button>';
			$sub_array[] = '<button type="button" name="delete" id="'.$row['item_id'].'" class="btn btn-danger btn-sm delete_item">Delete</button>';

			//store $sub_array into $data array
			$data[] = $sub_array;
		}
	}

	if ($_POST['fetch_tbl'] == 'supplies_tbl') {

		$tbl = 'wh_supplies_tbl whs JOIN goods_items_tbl itm ON whs.item_id = itm.item_id';
		$keys= 'whs.supply_id,whs.supplier, whs.supply_datetime, whs.supply_qty, whs.supply_price, itm.item_name';
		/* $query_condtn = 'WHERE itm.item_id = :item_id AND itm.item_name = :item_name';
		$bind_vals['item_id'] = floatval(trim($_POST['search_item_id']));
		$bind_vals['item_name'] = addslashes(trim($_POST['search_item_name'])); */
		$query_condtn = '';
		$query->select_assoc_no_bind($keys,$tbl,$query_condtn,$pdo);

		//$query->select_assoc_no_bind($joint_keys,$joint_tbl,$query_condtn,$pdo);
		$num_ttl_rows = $query->num_rows;


		if ($search_val != "") {
			$query_condtn .= " WHERE item_name LIKE :item_name";

			$search_array = [
						'item_name'=> "%$search_val%"
					];
		}
		if (isset($column_sort_order)) {
			$sort_columns = ["item_name","supply_datetime","supplier","supply_qty","supply_price"];
			$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
		}
		else {
			$query_condtn .= " ORDER BY supply_id DESC";
		}

		if ($row_per_page != -1) { //when page loads this condition will be true
				
			//********will display datas on the page
			$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
		}

		$query_result = $query->select_assoc_bind($keys,$tbl,$query_condtn,$search_array,$pdo);
		$num_filtered_rows = $query->num_rows;

		foreach ($query_result as $row) {
			$sub_array = [];
			$sub_array[] = $row['item_name'];
			$sub_array[] = date_format(date_create($row['supply_datetime']),"d/m/y H:i:s");
			$sub_array[] = $row['supplier'];
			$sub_array[] = $row['supply_qty'].' ctn';
			$sub_array[] = '<s>N</s> '.number_format($row['supply_price']);
			$sub_array[] = '<a type="submit" name="delete" id="'.$row['supply_id'].'" class="btn btn-outline-danger btn-sm delete"><i class="fas fa-trash-alt"></i></a>';
			//store $sub_array into $data array
			$data[] = $sub_array;
		}

		
	}


	if ($_POST['fetch_tbl'] == 'orders_tbl') {
			$tbl="cust_orders_tbl ord LEFT JOIN sales_inv_tbl inv ON ord.inv_id = inv.inv_id";
			$cols = "ord.order_id, ord.order_datetime, ord.item_id, ord.order_item, ord.order_qty, ord.qty_type, ord.order_price, ord.order_amt, inv.inv_no, inv.cust_name, inv.sales_person";

			$query_condtn = "";

			$query->select_assoc_no_bind($cols,$tbl,$query_condtn,$pdo);
			$num_ttl_rows = $query->num_rows;


			if ($search_val != "") {
				$query_condtn .= " WHERE order_datetime  LIKE :order_datetime";
				$query_condtn .= " OR inv_no LIKE :inv_no";
				//searches value in user_name column;
				$query_condtn .=" OR cust_name LIKE :cust_name";
				//searches value in user_status column;
				$query_condtn .=" OR sales_person LIKE :sales_person";

				$query_condtn .=" OR order_item LIKE :order_item";


				$search_array = [
							'inv_no'=> "%$search_val%",
							'cust_name'=> "%$search_val%",
							'sales_person'=> "%$search_val%",
							'order_item'=> "%$search_val%",
							'order_datetime'=> "%$search_val%"
						];
			}
			if (isset($column_sort_order)) {
				$sort_columns = ["order_datetime","inv_no","cust_name","sales_person","order_item","order_id"];
				$query_condtn .= " ORDER BY ".$sort_columns[$column_index]." ".$column_sort_order;
			}
			else {
				$query_condtn .= " ORDER BY order_datetime DESC";
			}

			if ($row_per_page != -1) { //when page loads this condition will be true
					
				//********will display datas on the page
				$query_condtn .= " LIMIT ". $row .", ". $row_per_page;
			}

			$query_result = $query->select_assoc_bind($cols,$tbl,$query_condtn,$search_array,$pdo);
			$num_filtered_rows = $query->num_rows;

			foreach ($query_result as $row) {

				$sub_array = [];
				$sub_array[] = date_format(date_create($row['order_datetime']),"d/m/y H:i:s");
				$sub_array[] = $row['inv_no'];
				$sub_array[] = $row['cust_name'];
				$sub_array[] = $row['sales_person'];
				$sub_array[] = $row['order_item'];
				$sub_array[] = $row['order_qty'].' '.$row['qty_type'];
				$sub_array[] = '<s>N</s>'.number_format($row['order_price'],'2');
				$sub_array[] = '<s>N</s>'.number_format($row['order_amt'],'2');
				//also create and store delete button
				$sub_array[] = '<a type="submit" name="delete" id="'.$row['order_id'].'" class="btn btn-outline-danger btn-sm delete"><i class="fas fa-trash-alt"></i></a>';

				//store $sub_array into $data array
				$data[] = $sub_array;
			}
		}

	if (empty($data)) {
		$data = ["data" => ''];
		$num_filtered_rows = 1;
		$output = 	[
				"draw" 				=> 		intval($draw),
				"recordsTotal"		=>		$num_filtered_rows,
				"recordsFiltered"	=>		$num_ttl_rows,
				"data"				=>		$data

				];
	}
	else{

		$output = 	[
				"draw" 				=> 		intval($draw),
				"recordsTotal"		=>		$num_filtered_rows,
				"recordsFiltered"	=>		$num_ttl_rows,
				"data"				=>		$data

				];


	}
	
	

	echo json_encode($output);

}




?>