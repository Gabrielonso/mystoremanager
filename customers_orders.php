<?php
include 'my_db.class.php';
if (!isset($_SESSION['type'])) {
	header("location:logout.php");
}

if (isset($_POST['btn_action']) && isset($_POST['id'])) {
//removes invoice with invoice_id from list of all_created_invoice
	if ($_POST['btn_action'] == 'delete') {
		$where_condtn = "order_id = :order_id";
		$param = [];
		$param['order_id'] = $_POST['id'];
		$input_vals = $param;
		$delete_order = $query->delete('cust_orders_tbl',$where_condtn,$input_vals,$pdo);

		if (isset($delete_order)) {
			echo '<script>alert("Order has been deleted!")</script>';
		}
	}
	

}

include 'header.php';
?>

<div class="row">
	<div class="col-lg-12">
		<div class="card card-default">
			<div class="card-header">
				<div class="row">
					<div class="col-lg-12" align="center">
						<h3 class="card-title">Customers' Orders</h3>	
					</div>
				</div>

				<div class="clear:both"></div>
			</div>
			<div class="card-body">
				<div class="row"><div class="col-sm-12 table-responsive">
						<table id="orders_tbl" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Order Datetime</th>
									<th>Invoice No</th>
									<th>Customer name</th>
									<th>Sales person</th>
									<th>Bought item</th>
									<th>Qty</th>
									<th>Order price</th>
									<th>Order amount</th>
									<th>Delete</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php
include 'footer.php';
?>
<script type="text/javascript">
	$(document).ready(function() {
		var table = $('#orders_tbl');
		sticky_header(table);

		var fetch_tbl = 'orders_tbl';
		var orders_tbl = $("#orders_tbl").DataTable({
			"processing": true,    //activate processing option of DataTable's plugin
			"serverSide": true,   //activates serverSide operation
			"order": [],
			//in DataTabe,we write ajax
			"ajax": {
					url: 'data_tables.php',
					type: 'POST',
					data: {fetch_tbl:fetch_tbl},
					dataType: 'json'
				},
			"columnDefs":[{
					//remove column order sorting from column index 4,5
					"targets":[5,6,7,8],
					"orderable": false
				}],
			"pageLength":25			
		});

		$(document).on('click', '.delete', function(){
			var id = $(this).attr("id");
			var btn_action = 'delete';
			if (confirm('Are you sure you want to remove this order?')) {
				$.ajax({
					url: 'customers_orders.php',
					method: 'post',
					data: {id:id, btn_action:btn_action}

				});
			}
			else {
				return false;
			}
		});	
	});
</script>
</body>
</html>