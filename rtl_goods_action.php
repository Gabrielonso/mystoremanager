<?php
include 'my_db.class.php';


if (isset($_POST['btn_action'])) {
	
	
	if ($_POST['btn_action'] == 'fetch_rtl_goods') {

		$bind_joint_val['item_id'] = intval($_POST['item_id']);

		$joint_keys = "itm.item_id,itm.item_name,itm.unit_price, itm.ctn_price, itm.unit_per_ctn,
				rtl.rtl_curr_stock,rtl.rtl_strt_stock_tdy,rtl.rtl_tdy_strt_date,
				rtl.rtl_tdy_strt_time,rtl.rtl_ins_tdy,
				wh.wh_curr_stock";

		$joint_tbls = "goods_items_tbl itm
					JOIN rtl_stock_tbl rtl ON itm.item_id = rtl.item_id
					JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id";

		$query_condtn = "WHERE itm.item_id = :item_id";


		$result = $query->select_assoc_bind($joint_keys,$joint_tbls,$query_condtn,$bind_joint_val,$pdo);

		foreach ($result as $row) {
			

			$output['item_id'] = $row['item_id'];
			$output['item_name'] = $row['item_name'];
			$output['rtl_curr_stock'] = $row['rtl_curr_stock'];
			$output['rtl_strt_stock_tdy'] = $row['rtl_strt_stock_tdy'];
			$output['rtl_tdy_strt_date'] = $row['rtl_tdy_strt_date'];
			$output['rtl_tdy_strt_time'] = $row['rtl_tdy_strt_time'];
			$output['rtl_ins_tdy'] = $row['rtl_ins_tdy'];
			$output['wh_curr_stock'] = $row['wh_curr_stock'];
			$output['unit_per_ctn'] = $row['unit_per_ctn'];
			$output['unit_price'] = $row['unit_price'];
			$output['ctn_price'] = $row['ctn_price'];

		}
		echo json_encode($output);

	}


	if ($_POST['btn_action'] == 'update_retail') {
		$pdo->beginTransaction();
		$curr_time = $query->curr_time($pdo);
		$curr_date = $query->curr_date($pdo);
		$curr_datetime = $query->curr_datetime($pdo);
		$supply_qty_in_pcs = floatval(trim($_POST['supply_qty'])) * floatval(trim($_POST['unit_per_ctn']));

		$joint_tbls = "goods_items_tbl itm JOIN rtl_stock_tbl rtl ON itm.item_id = rtl.item_id";

		$joint_key_vals = "itm.item_name = :item_name,
	   				itm.ctn_price = :ctn_price,
	   				itm.unit_price = :unit_price,
	   				rtl.rtl_curr_stock = :rtl_curr_stock + :supply_qty_in_pcs
					";
		$query_condtn = "itm.item_id = :item_id";


		$bind_joint_vals['item_id'] = intval($_POST['item_id']);
		$bind_joint_vals['item_name'] = addslashes(trim($_POST['item_name']));
		$bind_joint_vals['unit_price'] = floatval(trim($_POST['unit_price']));
		$bind_joint_vals['ctn_price'] = floatval(trim($_POST['ctn_price']));
		$bind_joint_vals['rtl_curr_stock'] = floatval(trim($_POST['rtl_curr_stock']));
		$bind_joint_vals['supply_qty_in_pcs'] = floatval(trim($supply_qty_in_pcs));


		if (floatval(trim($_POST['rtl_curr_stock'])) != floatval(trim($_POST['hidden_rtl_curr_stock']))){
			$bind_joint_vals['rtl_strt_stock_tdy'] = floatval(trim($_POST['rtl_curr_stock']));
			$bind_joint_vals['rtl_ins_tdy'] = 0;
			$bind_joint_vals['rtl_out_tdy'] = 0;
			$bind_joint_vals['rtl_tdy_strt_date'] = $curr_date;
			$bind_joint_vals['rtl_tdy_strt_time'] = $curr_time;

			$joint_key_vals .= ", rtl.rtl_strt_stock_tdy = :rtl_strt_stock_tdy,
						rtl.rtl_ins_tdy = :rtl_ins_tdy + :supply_qty_in_pcs,
				    	rtl.rtl_out_tdy = :rtl_out_tdy,
				    	rtl.rtl_tdy_strt_date = :rtl_tdy_strt_date,
				    	rtl.rtl_tdy_strt_time = :rtl_tdy_strt_time";
						
		}
		else{
			$bind_joint_vals['rtl_strt_stock_tdy'] = floatval(trim($_POST['rtl_strt_stock_tdy']));
			$bind_joint_vals['rtl_tdy_strt_date'] = $_POST['rtl_tdy_strt_date'];
			$bind_joint_vals['rtl_tdy_strt_time'] = $_POST['rtl_tdy_strt_time'];
			$joint_key_vals .= ", rtl.rtl_strt_stock_tdy = :rtl_strt_stock_tdy,
						rtl.rtl_ins_tdy = rtl_ins_tdy + :supply_qty_in_pcs,
				    	rtl.rtl_tdy_strt_date = :rtl_tdy_strt_date,
				    	rtl.rtl_tdy_strt_time = :rtl_tdy_strt_time
				    	";
		}


		if (trim($_POST['item_name']) == "") {
				
			echo "*Invalid Input!";
			return;
		}
		elseif ($_POST['add_to_rtl'] == 'no') {
			$update_goods = $query->update($joint_tbls,$joint_key_vals,$query_condtn,$bind_joint_vals,$pdo);
		}
		elseif ($_POST['add_to_rtl'] == 'yes') {

			if ($_POST['supply_qty'] > $_POST['wh_curr_stock']) {
					echo "* Insufficient stock in store";
					return;
			}
			else{
				$joint_tbls .=" JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id";


	 			$joint_key_vals .= ", 	itm.unit_per_ctn = :unit_per_ctn,
							wh.wh_out_tdy = wh_out_tdy - :supply_qty,
							wh.wh_to_rtl_tdy = wh_to_rtl_tdy - :supply_qty,
							wh.wh_to_rtl_since_strt = wh_to_rtl_since_strt - :supply_qty,
							wh.wh_out_since_strt =wh_out_since_strt - :supply_qty,
							wh.wh_curr_stock = wh_curr_stock - :supply_qty";

				$bind_joint_vals['supply_qty'] = floatval(trim($_POST['supply_qty']));
				$bind_joint_vals['unit_per_ctn'] = floatval(trim($_POST['unit_per_ctn']));

				$update_goods = $query->update($joint_tbls,$joint_key_vals,$query_condtn,$bind_joint_vals,$pdo);
			}
			
			
		}

		
		if (isset($update_goods)) {
			$pdo->commit();
			echo "Goods Updated Successfully!";
		}
		else{
			$pdo->rollBack();
			echo "Failed to update goods!";
		}
	}
}


?>