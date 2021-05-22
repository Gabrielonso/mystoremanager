<?php
include 'my_db.class.php';

if (isset($_POST['key_action'])) {

	if ($_POST['key_action'] == 'search_item') {

		$pl_holder = ':item_name';
		$bind_itm_val['item_name'] = '%'.addslashes($_POST['search_val']).'%';

		$result = $db_sp->search_goods_item($pl_holder,$bind_itm_val,$pdo);
		$num_rows = $db_sp->num_rows;

		if ($num_rows > 0) {
		foreach ($result as $row) {
			/*array_push($output, $row);*/

			echo "<a href='#' id='".$row['item_id']."' class='list-group-item list-group-item-action select_item list-group-item-primary text-dark'>".$row['item_name']."</a>";
		}
		
		}
	}
}
if (isset($_POST['btn_action'])) {

	if ($_POST['btn_action'] == 'fetch_dtls') {

		$bind_val['item_id'] = floatval(trim($_POST['item_id']));
		$prices = $actions->get_prices($bind_val,$pdo);
		foreach ($prices as $pr) {
			$output['item_id'] = $pr['item_id'];
			$output['item_name'] = $pr['item_name'];
			$output['unit_price'] = $pr['unit_price'];
			$output['ctn_price'] = $pr['ctn_price'];
			$output['unit_per_ctn'] = $pr['unit_per_ctn'];
			$output['purchase_price'] = $pr['purchase_price'];

			echo json_encode($output);
		}
	}

	if ($_POST['btn_action'] == 'update_goods_item') {

		$curr_date = $query->curr_date($pdo);
		$curr_time = $query->curr_time($pdo);
		$curr_datetime = $query->curr_datetime($pdo);
		

		if (trim($_POST['item_name']) == "") {
				
			echo "*Invalid input!";
			return;
		}
		else{
			$pdo->beginTransaction();

			$items_tbl =   "goods_items_tbl";

			$items_key_vals = " item_name = :item_name,
								ctn_price = :ctn_price,
								unit_price = :unit_price,
								purchase_price = :purchase_price,
								unit_per_ctn = :unit_per_ctn";
			$query_condtn = "item_id = :item_id";

			$bind_item_vals['item_name'] = addslashes(trim($_POST['item_name']));
			$bind_item_vals['ctn_price'] = floatval(trim($_POST['ctn_price']));
			$bind_item_vals['unit_price'] = floatval(trim($_POST['unit_price']));
			$bind_item_vals['purchase_price'] = floatval(trim($_POST['purchase_price']));
			$bind_item_vals['item_id'] = floatval(trim($_POST['item_id']));
			$bind_item_vals['unit_per_ctn'] = floatval(trim($_POST['unit_per_ctn']));


			$update_goods_item = $query->update($items_tbl,$items_key_vals,$query_condtn,$bind_item_vals,$pdo);

			if (isset($update_goods_item)) {
				$pdo->commit();
				echo '<div class="text-success font-weight-bold">Successfully updated '.addslashes(trim($_POST['item_name'])).'!</div>';
			}
			else{
				$pdo->rollBack();
				echo '<div class="text-danger font-weight-bold">Unable to update item!</div>';
			}

		}
	}


	//delete record	
	if ($_POST['btn_action'] == 'delete_record') {
		$items_tbl = "goods_items_tbl";
		$query_condtn = "item_id = :item_id";
		$bind_items_val['item_id'] = $_POST['item_id'];
		$delete_record = $query->delete($items_tbl,$query_condtn,$bind_items_val,$pdo);

		if (isset($delete_record)) {
			echo '<div class="text-success font-weight-bold">Record deleted!</div>';
		}
	}
}




?>