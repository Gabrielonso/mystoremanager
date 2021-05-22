<?php
include 'db_conn.php';

class myQueries{

	//.......query properties...
	public $column;
	public $table;
	public $condition;
	public $keyValue_pair;
	public $value;
	public $num_rows;
	public $fetch_single;

	public $curr_date;
	public $curr_time;
	public $curr_datetime;



	//sets current date
	public function curr_date($pdo){
		$stmt = $pdo->query("SELECT CURDATE()");
		$curr_date = $stmt->fetchColumn();
		return  $curr_date;
	}

	//sets current time
	public function curr_time($pdo){
		$stmt = $pdo->query("SELECT CURTIME()");
		$curr_time = $stmt->fetchColumn();
		return  $curr_time;
	}

	//sets current datetime
	public function curr_datetime($pdo){
		$stmt = $pdo->query("SELECT NOW()");
		$curr_datetime = $stmt->fetchColumn();
		return  $curr_datetime;
	}

	//sets last_insert_id
	public function last_insert_id($pdo){
		$stmt = $pdo->query("SELECT LAST_INSERT_ID()");
		$id = $stmt->fetchColumn();
		return  $id;
	}

	//generate unique invoice_no
	public function invoice_no($pdo){
		//$sql = "SELECT CONCAT('CNO-', LPAD(LAST_INSERT_ID(), 7, '0'))";
		$sql = "SELECT CONCAT('CNO-', CASE WHEN LENGTH(LAST_INSERT_ID()) < 4 THEN LPAD(LAST_INSERT_ID(), 4, '0') ELSE LAST_INSERT_ID() END)";
		$stmt = $pdo->query($sql);
		$inv_no = $stmt->fetchColumn();
		return  $inv_no;
	}

	//..........sql query methods..........


	//sql select statement
	public function select_single_no_bind($colum,$table,$condition,$pdo){
	
		$sql = "SELECT ".$colum." FROM ".$table." ".$condition."";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		//$this->num_rows = $stmt->rowCount();
		$result = $stmt->fetchColumn();
		return $result;
	
	}




	//select assoc without bind params
	public function select_assoc_no_bind($colum,$table,$condition,$pdo){
	
		$sql = "SELECT ".$colum." FROM ".$table." ".$condition."";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$this->num_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
	
	}

	//sql select table as assoc array
	public function select_assoc_bind($colum,$table,$condition,$input,$pdo){
	
		$sql = "SELECT ".$colum." FROM ".$table." ".$condition."";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($input);
		$this->num_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
	
	}

	//sql update statement
	public function update($table,$keyValue_pair,$where_condtn,$input,$pdo){
	
		$sql = "UPDATE ".$table." SET ".$keyValue_pair." WHERE ".$where_condtn."";
		$stmt = $pdo->prepare($sql);

		return $stmt->execute($input);

					
	}

	//sql insert statement
	public function insert($table,$column,$value,$input_vals,$pdo){
	
		$sql = "INSERT INTO ".$table." (".$column.") VALUES (".$value.")";
		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute($input_vals);
		return $result;				
	}

	//sql delete statement
	public function delete($table,$where_condtn,$input,$pdo){
		$sql = "DELETE FROM ".$table." WHERE ".$where_condtn."";
		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute($input);
		return $result;
	}

}

$query = new myQueries();


class my_db_action extends myQueries
{
	public $no_of_rows;
/*
	public function get_invoice_list($pdo){

		$sql = "SELECT inv.*,pay.pay_amt,pay.outstanding_amt,pay.any_outstanding FROM sales_inv_tbl inv
				LEFT JOIN
					(SELECT inv_id, SUM(pay_amt) AS pay_amt, MIN(outstanding_amt) AS outstanding_amt, any_outstanding
					FROM payments
					GROUP BY inv_id, any_outstanding) pay
				ON inv.inv_id = pay.inv_id";
			
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$this->no_of_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
	}
*/
	public function get_users($col,$query_condtn,$bind,$pdo) {
		$sql = "SELECT ".$col." FROM users_tbl WHERE ".$query_condtn."";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($bind);
		$this->no_of_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
	}

	public function get_prices($bind,$pdo) {

		$sql = "SELECT * FROM
				goods_items_tbl WHERE item_id = :item_id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($bind);
		$this->no_of_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
	}

}

$actions = new my_db_action();


class sp extends myQueries{

	public function search_goods_item($pl_holder,$bind_param,$pdo){

		$sql = "CALL search_goods_item(".$pl_holder.")";
		$stmt = $pdo->prepare($sql);
		$stmt->execute($bind_param);
		$this->num_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
		
	}

	public function fetch_all_users($pdo){

		$sql = "CALL fetch_all_users_dtl()";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$this->num_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
		
	}

	public function into_orders_tbl($pl_holders,$bind_param,$pdo){
	
		$sql = "CALL insert_cust_orders(".$pl_holders.")";
		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute($bind_param);
		return $result;
	}

	public function into_invoice_tbl($pl_holders,$bind_param,$pdo){
	
		$sql = "CALL insert_invoice_dtls(".$pl_holders.")";
		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute($bind_param);
		return $result;
	}

	public function into_payments_tbl($pl_holders,$bind_param,$pdo){
	
		$sql = "CALL insert_payments_dtls(".$pl_holders.")";
		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute($bind_param);
		return $result;
	}

	public function get_invoice_and_payments($pdo){

		$sql = "CALL invoice_n_payments ();";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$this->num_rows = $stmt->rowCount();
		$result = $stmt->fetchAll();
		return $result;
	}

	public function first_daily_update($pdo){

		$sql = "CALL `first_daily_update`();";
		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute();
		return $result;
	}
}

$db_sp = new sp();


?>
