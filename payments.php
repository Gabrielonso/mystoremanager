<?php
include 'my_db.class.php';
if (!isset($_SESSION['type'])) {
	header("location:logout.php");
}
include 'header.php';
?>

<div class="row">
	<div class="col-lg-12">
		<div class="card card-default">
			<div class="card-header">
				<div class="row">
					<div class="col-lg-12" align="center">
						<h3 class="card-title">Payments</h3>	
					</div>
				</div>

				<div class="clear:both"></div>
			</div>
			<div class="card-body">
				<div class="row"><div class="col-sm-12 table-responsive">
						<table id="payment_tbl" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Datetime</th>
									<th>Invoice No</th>
									<th>Paid By</th>
									<th>Paid to</th>
									<th>Amount</th>
									<th>Payment type</th>
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
		var table = $('#payment_tbl');
		sticky_header(table);

		var fetch_tbl = 'payments_tbl';
		var payments_tbl = $("#payment_tbl").DataTable({
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
					"targets":[6],
					"orderable": false
				}],
			"pageLength":25			
		});

		$(document).on('click', '.delete', function(){
			var pay_id = $(this).attr("id");
			var btn_action = 'delete_pay';
			if (confirm('Are you sure you want to remove this payment?')) {
				$.ajax({
					url: 'process_payment.php',
					method: 'post',
					data: {pay_id:pay_id, btn_action:btn_action},
					success:function(data){
						$('#alert-modal').find('.modal-body').html(data);
						$('#alert-modal').modal('show');
						payments_tbl.ajax.reload();
					}
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