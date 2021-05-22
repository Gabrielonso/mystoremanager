<?php

include 'my_db.class.php';

if (isset($_POST['btn_action'])) {
	
	//creates new goods item
	if ($_POST['btn_action'] == 'create_new') {
		$pdo->beginTransaction();

			$curr_time = $query->curr_time($pdo);
			$curr_date = $query->curr_date($pdo);
			$curr_datetime = $query->curr_datetime($pdo);

			if (trim($_POST['item_name']) == '') {
				
				echo "*Invalid Input!";
				return;
			}
			else {

		/*.........goods items tbl.........*/
				$itm_tbl = "goods_items_tbl";
				$itm_keys = "item_name, ctn_price, unit_price, purchase_price, unit_per_ctn";
				$itm_pl_holder = ":item_name, :ctn_price, :unit_price, :purchase_price, :unit_per_ctn";
				$bind_itm_vals['item_name'] = addslashes(trim($_POST['item_name']));
				$bind_itm_vals['ctn_price'] = floatval(trim($_POST['ctn_price']));
				$bind_itm_vals['unit_price'] = floatval(trim($_POST['unit_price']));
				$bind_itm_vals['purchase_price'] = floatval(trim($_POST['supply_price']));
				$bind_itm_vals['unit_per_ctn'] = floatval(trim($_POST['unit_per_ctn']));

				$insert = $query->insert($itm_tbl,$itm_keys,$itm_pl_holder,$bind_itm_vals,$pdo);
		//get item id after insert
				$item_id = $query->last_insert_id($pdo);




		/*.........if supplies are added.........*/			
				
				if (floatval(trim($_POST['supply_qty'])) != "") {
					//...wh_table...
					$sup_tbl = "wh_supplies_tbl";
					$sup_keys = "item_id, supply_datetime, supply_qty, supply_amt";
					$sup_pl_holder = ":item_id, :supply_datetime, :supply_qty, :supply_amt";
					$bind_sup_vals['supply_datetime'] = $curr_datetime;
					$bind_sup_vals['supply_qty'] = floatval(trim($_POST['supply_qty']));
					$bind_sup_vals['supply_amt'] = floatval(trim($_POST['supply_qty'])) * floatval(trim($_POST['supply_price']));
					$bind_sup_vals['item_id'] = $item_id;

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

					$insert = $query->insert($sup_tbl,$sup_keys,$sup_pl_holder,$bind_sup_vals,$pdo);
				}


		/*.........wh stock tbl.........*/
				$wh_tbl = "wh_stock_tbl";
				$wh_keys = "item_id, wh_stock_after_supply,wh_ins_tdy, wh_strt_stock,
							 wh_strt_datetime, wh_curr_stock";
				$wh_pl_holder = ":item_id, :wh_stock_after_supply, :wh_ins_tdy, :wh_strt_stock,
								 :wh_strt_datetime, :wh_curr_stock";

				$bind_wh_vals['item_id'] = $item_id;
				$bind_wh_vals['wh_stock_after_supply'] = floatval(trim($_POST['supply_qty']));
				$bind_wh_vals['wh_ins_tdy'] = floatval(trim($_POST['supply_qty']));
				$bind_wh_vals['wh_strt_stock'] = floatval(trim($_POST['supply_qty']));
				$bind_wh_vals['wh_curr_stock'] = floatval(trim($_POST['supply_qty']));
				$bind_wh_vals['wh_strt_datetime'] = $curr_datetime;

				$insert = $query->insert($wh_tbl,$wh_keys,$wh_pl_holder,$bind_wh_vals,$pdo);

		/*.............rtl stocks............*/
				$rtl_stock_tbl = "rtl_stock_tbl";
				$rtl_stock_keys = "item_id, rtl_tdy_strt_date, rtl_tdy_strt_time";
				$rtl_stock_pl_holder = ":item_id, :rtl_tdy_strt_date, :rtl_tdy_strt_time";
				$bind_rtl_stock_vals['item_id'] = $item_id;
				$bind_rtl_stock_vals['rtl_tdy_strt_date'] = $curr_date;
				$bind_rtl_stock_vals['rtl_tdy_strt_time'] = $curr_time;

				$insert = $query->insert($rtl_stock_tbl,$rtl_stock_keys,$rtl_stock_pl_holder,$bind_rtl_stock_vals,$pdo);



				if (isset($insert)) {
					$pdo->commit();
					echo '<div class="text-success font-weight-bold">New record for '.addslashes(trim($_POST['item_name'])).' has been created!</div>';
				}
				else {
					$pdo->rollBack();
					echo '<div class="text-danger font-weight-bold">Failed to create new record!</div>';
				}
			}
	}
}





?>