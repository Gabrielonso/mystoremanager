<?php
include 'my_db.class.php';

if (isset($_POST['btn_action'])) {

	$joint_tbl = "goods_items_tbl itm JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id";

	$joint_keys = "itm.item_id,itm.item_name,
					itm.unit_per_ctn,
					itm.ctn_price,itm.unit_price,itm.purchase_price,
					wh.wh_strt_stock,wh.wh_strt_datetime,wh.wh_ins_since_strt,wh.wh_out_since_strt,wh.wh_curr_stock";
	$query_condtn = "WHERE itm.item_id = :item_id";

	$bind_joint_keys['item_id'] = intval($_POST['item_id']);

	$result = $query->select_assoc_bind($joint_keys,$joint_tbl,$query_condtn,$bind_joint_keys,$pdo);
	foreach ($result as $row){
	

		if ($_POST['btn_action'] == 'fetch_goods') {
			
				$output['item_id'] = $row['item_id'];
				$output['item_name'] = $row['item_name'];
				$output['unit_per_ctn'] = $row['unit_per_ctn'];
				$output['purchase_price'] = $row['purchase_price'];
				$output['ctn_price'] = $row['ctn_price'];
				$output['unit_price'] = $row['unit_price'];
				$output['wh_strt_datetime'] = date_format(date_create($row['wh_strt_datetime']),"D, M d Y h:i:s a");
				$output['wh_strt_stock'] = $row['wh_strt_stock'];
				$output['wh_ins_since_strt'] = $row['wh_ins_since_strt'];
				$output['wh_out_since_strt'] = $row['wh_out_since_strt'];
				$output['wh_curr_stock'] = $row['wh_curr_stock'];

			
			echo json_encode($output);
		}
	}
	
	//edit/update wh stock
	if ($_POST['btn_action'] == 'edit_wh_stock') {
		$pdo->beginTransaction();
		$curr_date = $query->curr_date($pdo);
		$curr_time = $query->curr_time($pdo);
		$curr_datetime = $query->curr_datetime($pdo);
		
		if (trim($_POST['item_name']) == "") {
				
			echo "*Invalid Input!";
			return;
		}
		else{

			$joint_tbl = "goods_items_tbl itm ";

			$joint_key_vals ="itm.item_name = :item_name,
						itm.ctn_price = :ctn_price,
						itm.unit_price = :unit_price";
			$query_condtn = "itm.item_id = :item_id";

			$bind_joint_vals['item_id'] = intval(trim($_POST['item_id']));			
			$bind_joint_vals['item_name'] = addslashes(trim($_POST['item_name']));
			$bind_joint_vals['ctn_price'] = floatval(trim($_POST['ctn_price']));
			$bind_joint_vals['unit_price'] = floatval(trim($_POST['unit_price']));

			if (isset($_POST['hidden_wh_curr_stock']) && floatval(trim($_POST['wh_curr_stock'])) != floatval(trim($_POST['hidden_wh_curr_stock']))){

				$joint_tbl .= " JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id";

				$joint_key_vals .= ", wh.wh_curr_stock = :wh_curr_stock,
						    wh.wh_strt_stock = :wh_strt_stock,
						    wh.wh_ins_since_strt = :wh_ins_since_strt,
							wh.wh_to_rtl_since_strt = :wh_to_rtl_since_strt,
							wh.wh_to_cust_since_strt = :wh_to_cust_since_strt,
						    wh.wh_out_since_strt = :wh_out_since_strt,
						    wh.wh_strt_datetime = :wh_strt_datetime";

				$bind_joint_vals['wh_curr_stock'] = floatval(trim($_POST['wh_curr_stock']));
				$bind_joint_vals['wh_strt_stock'] = floatval(trim($_POST['wh_curr_stock']));
				$bind_joint_vals['wh_ins_since_strt'] = 0;
				$bind_joint_vals['wh_to_rtl_since_strt'] = 0;
				$bind_joint_vals['wh_to_cust_since_strt'] = 0;
				$bind_joint_vals['wh_out_since_strt'] = 0;
				$bind_joint_vals['wh_strt_datetime'] = $curr_datetime;

				
								
			}
			
			$update_record = $query->update($joint_tbl,$joint_key_vals,$query_condtn,$bind_joint_vals,$pdo);

			if (isset($update_record)) {
				$pdo->commit();
				echo '<div class="text-success font-weight-bold">Successfully updated '.addslashes(trim($_POST['item_name'])).'!</div>';
			}
			else {
				$pdo->rollBack();
				echo '<div class="text-danger font-weight-bold">Failed to update record!</div>';
			}
			
		}
	}

}








?>