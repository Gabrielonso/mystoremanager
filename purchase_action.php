<?php
include 'my_db.class.php';
if (!isset($_SESSION['type'])) {
	header("location:login.php");
}

if (isset($_POST['btn_action'])) {
	//fetch stock details
	if ($_POST['btn_action'] == 'fetch_stock_dtls') {
		$tbl = 'goods_items_tbl itm
				JOIN rtl_stock_tbl rtl ON itm.item_id = rtl.item_id
				JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id';
		$keys= 'itm.*, rtl.rtl_curr_stock, wh.wh_curr_stock';
		$query_condtn = 'WHERE itm.item_id = :item_id';
		$bind_vals['item_id'] = floatval(trim($_POST['search_item_id']));
	
		$stock_dtl = $query->select_assoc_bind($keys,$tbl,$query_condtn,$bind_vals,$pdo);

		foreach ($stock_dtl as $stk) {
			$output['item_id'] = $stk['item_id'];
			$output['item_name'] = $stk['item_name'];
			$output['ctn_price'] = $stk['ctn_price'];
			$output['unit_price'] = $stk['unit_price'];
			$output['purchase_price'] = $stk['purchase_price'];
			$output['unit_per_ctn'] = $stk['unit_per_ctn'];
			$output['rtl_curr_stock'] = $stk['rtl_curr_stock'].' pcs';
			$output['wh_curr_stock'] = $stk['wh_curr_stock'].' ctns';

			echo json_encode($output);
		}
	}


	//.......add supplies.......
	if ($_POST['btn_action'] == 'add_supply') {
		if (floatval(trim($_POST['supply_qty'])) == ""){
			echo '*Invalid Input!';
			return;
		}
		else {
			$pdo->beginTransaction();

			$curr_datetime = $query->curr_datetime($pdo);

			$sup_tbl = "wh_supplies_tbl";
			$sup_keys = "item_id, supply_datetime, supply_qty, supply_amt";
			$sup_pl_holder = ":item_id, :supply_datetime, :supply_qty, :supply_amt";
			$bind_sup_vals['supply_datetime'] = $curr_datetime;
			$bind_sup_vals['supply_qty'] = floatval(trim($_POST['supply_qty']));
			$bind_sup_vals['supply_amt'] = floatval(trim($_POST['supply_qty'])) * floatval(trim($_POST['supply_price']));

			$bind_sup_vals['item_id'] = floatval(trim($_POST['item_id']));

			if (addslashes(trim($_POST['supplier'])) != "") {
				$sup_keys .= ", supplier";
				$sup_pl_holder .=", :supplier";	
				$bind_sup_vals['supplier'] = addslashes(trim($_POST['supplier']));
			}

			if (floatval(trim($_POST['supply_price'])) != "") {
				$sup_keys .= ", supply_price";
				$sup_pl_holder .=", :supply_price";	
				$bind_sup_vals['supply_price'] = floatval(trim($_POST['supply_price']));
			}

			$insert_supply = $query->insert($sup_tbl,$sup_keys,$sup_pl_holder,$bind_sup_vals,$pdo);

			$wh_tbl =   "goods_items_tbl itm JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id";

			$wh_key_vals = "itm.ctn_price = :ctn_price,
							itm.unit_price = :unit_price,
							itm.purchase_price = :purchase_price,
							itm.unit_per_ctn = :unit_per_ctn,
							wh.wh_stock_b4_supply = wh_curr_stock,
							wh.wh_stock_after_supply = wh_curr_stock + :supply_qty,	
							wh.wh_ins_tdy = wh_ins_tdy + :supply_qty,
							wh.wh_ins_since_strt = wh_ins_since_strt + :supply_qty,
							wh.wh_curr_stock = wh_curr_stock + :supply_qty
							";
			
			$query_condtn = 'itm.item_id = :item_id';
		


			$bind_wh_vals['purchase_price'] = floatval(trim($_POST['supply_price']));
			$bind_wh_vals['ctn_price'] = floatval(trim($_POST['ctn_price']));
			$bind_wh_vals['unit_price'] = floatval(trim($_POST['unit_price']));
			$bind_wh_vals['unit_per_ctn'] = floatval(trim($_POST['unit_per_ctn']));
			$bind_wh_vals['item_id'] = floatval(trim($_POST['item_id']));
			$bind_wh_vals['supply_qty'] = floatval(trim($_POST['supply_qty']));

			//update wh_stock_tbl
			$update_stock = $query->update($wh_tbl,$wh_key_vals,$query_condtn,$bind_wh_vals,$pdo);

				if (isset($insert_supply) && isset($update_stock)) {
					$pdo->commit();
					echo '<div class="text-success font-weight-bold">Successfully added supplies for '.addslashes(trim($_POST['item_name'])).'!</div>';
				}
				else{
					$pdo->rollBack();
					echo '<div class="text-danger font-weight-bold">Unable to update item!</div>';
				}

		}
	}

	if ($_POST['btn_action'] == 'delete') {

		$query_condtn_condtn = "supply_id = :supply_id";
		$param = [];
		$param['supply_id'] = $_POST['id'];
		$input_vals = $param;
		$delete_supply = $query->delete('wh_supplies_tbl',$query_condtn_condtn,$input_vals,$pdo);

		if (isset($delete_supply)) {
			echo '<div class="font-weight-bold text-success">Supply record has been removed</div>';
		}
		else{
			echo '<div class="font-weight-bold text-danger">Unable to remove supply record</div>';
		}
}
	
}
?>