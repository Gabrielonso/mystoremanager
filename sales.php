<?php
include 'my_db.class.php';
if (!isset($_SESSION['type'])) {
	header("location:logout.php");
}


if (isset($_GET['delete']) && isset($_GET['id'])) {
//removes invoice with invoice_id from list of all_created_invoice
	$where_condtn = "inv_id = :inv_id";
	$param = [];
	$param['inv_id'] = $_GET['id'];
	$input_vals = $param;
	$delete_invoice = $query->delete('sales_inv_tbl',$where_condtn,$input_vals,$pdo);

	if (isset($delete_invoice)) {
		echo '<script>alert("Invoice has been deleted!")</script>';
	}
}
include 'header.php';

?>
<span id="alert-action"></span>
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="card-title col-lg-8">
						<h3 align="center">Invoice List</h3>
					</div>
					<div class="col-lg-4" align="right">
						<a href="sales_invoice.php" class="btn btn-info btn-md">Create Invoice</a>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table id="inv_tbl" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Invoice No.</th>
									<th>Invoice Date</th>
									<th>Customer Name</th>
									<th>Sales Person</th>
									<th>SubTotal</th>
									<th width="10%">Discount</th>
									<th width="13%">Total</th>
									<th>status</th>
									<th width="3%">Pay</th>
									<th width="3%">PDF</th>
									<th width="3%">Edit</th>
									<th width="3%">Delete</th>
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
<div id="pay_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<form class="pay_form" id="pay_form">
			<div class="modal-content">
				<div class="modal-header">
			      	<h4 class="modal-title">Enter Payment</h4>
			        <button type="button" class="close" data-dismiss="modal">&times;</button>
			    </div>
			    <div class="modal-body">    
			        <div class="table-responsive">
						<table class="table pay_form_tbl">
							<table class="table table-bordered">
								<label>Invoice Details</label>
								<tr>
									<th width="30%">Invoice No:</th>
									<td><span id="inv_no"></span></td>
								</tr>
								<tr>
									<th>Invoice Date:</th>
									<td><span id="inv_date"></span></td>
								</tr>
								<tr>
									<th>Customer Name:</th>
									<td><span id="cust_name"></span></td>
									<input type="hidden" name="paid_by" id="hidden_cust_name">
								</tr>
								<tr>
									<th>Sales Person:</th>
									<td><span id="sales_person"></span></td>
								</tr>
								<tr>
									<th>Invoice Total: </th>
									<td><s><b>N</b></s> <span id="inv_fnl_ttl"></span></td>
								</tr>								
							</table>
							<br>
							<div class="text-center font-weight-light">Make Payment</div>
							<hr>
							<div class="row">
								<div class="form-group col-lg-4">
									<label for="pay_amt" class="font-weight-bold">Enter payment amount:</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<s class="input-group-text"><b>N</b></s>
										</div>	
	   								 	<input type="number" name="pay_amt" class="form-control" id="pay_amt" required>
	 								</div>
								</div>
								<div class="form-group col-lg-4">
									<div class="font-weight-bold">Payment Type</div>
									<label class="radio-inline">
										<input type="radio" name="pay_type" value="cash" id="cash_pay">Cash
									</label>
									<label class="radio-inline">
										<input type="radio" name="pay_type" value="bank" id="bank_pay">Bank
									</label>
								</div>
								<div class="form-group col-lg-4">
									<div class="font-weight-bold">Any Outstanding Payment?</div>
									<label class="radio-inline">
										<input type="radio" name="any_outstanding" value="no" id="no_outstanding">No
									</label>
									<label class="radio-inline">
										<input type="radio" name="any_outstanding" value="yes" id="yes_outstanding">Yes
									</label>
									<div class="collapse form-group" id="fill_outstanding">
										<label for="outstanding_amt" class="font-weight-bold">
											Enter Outstanding Amount:
										</label>
										<div class="input-group mb-3">
		    								<div class="input-group-prepend">
		    									<s class="input-group-text"><b>N</b></s>
		    								</div>	
		   								 	<input type="number" name="outstanding_amt" class="form-control" id="outstanding_amt">
		   								 	<input type="hidden" name="hidden_outstanding_amt" id="hidden_outstanding_amt">
		 								</div>
									</div>
								</div>
							</div>
							<table class="table table-bordered" id="payment_dtls">
								<label>Payment Details</label>
								<thead>
									<tr>
										<th>Amount Paid</th>
										<th>Date</th>
										<th>Payment Type</th>
										<th>Paid To</th>
									</tr>
								</thead>
								<tbody class="pay_info">
									<!-- display all payments info here -->
								</tbody>
							</table>
						</table>
					</div>    
			    </div>
			    <div class="modal-footer">
			      	<input type="hidden" name="inv_id" id="inv_id">
					<input type="hidden" name="btn_action" id="btn_action" value="Add">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Pay">
					<button type="button" data-dismiss="modal" class="btn btn-outline-danger">Close</button>
			    </div>
			</div>
		</form>
	</div>
</div>
</div>	
<?php
include 'footer.php';
?>
<script type="text/javascript">
	
	$(document).ready(function() {

		var table = $('#inv_tbl');
		sticky_header(table);

		<?php if (isset($_GET['alert'])){?>
		$('#alert-modal')find('.modal-body').html('<div class="text-success font-weight-bold">'+<?php echo $_GET['alert'] ;
		?>+'</div>');
		$('#alert-modal').modal('show');
		<?php
		}
		?>
		var fetch_tbl = 'sales_tbl';
		var inv_tbl = $("#inv_tbl").DataTable({
			"processing": true,
			"serverSide": true,
			"order": [],
			"ajax": {
					url: 'data_tables.php',
					type: 'POST',
					data: {fetch_tbl:fetch_tbl},
					dataType: 'json'
				},
			"columnDefs":[{
					"targets":[8,9,10,11],
					"orderable": false
				}],
			"pageLength":25			
		});
		

/*		var inv_tbl = $('#inv_tbl').DataTable({
			"order" :[],
			"columnDefs": [{
				"targets": [8,9,10],
				"orderable": false
			}],
			"pageLength": 25
		});
*/

		$(document).on('click','.pay', function(){
			var inv_id = $(this).attr('id');
			var btn_action = 'fetch_pay_dtl';
			$.ajax({
				url: 'process_payment.php',
				method: 'post',
				data: {inv_id:inv_id, btn_action:btn_action},
				dataType: 'json',
				success:function(data) {
					var ttl_pay_amt = 0;
					for(i in data){
						
						$('#inv_no').text(data[0].inv_no);			
						$('#inv_date').text(data[0].inv_date);
						$('#cust_name').text(data[0].cust_name);
						$('#hidden_cust_name').val(data[0].cust_name);
						$('#sales_person').text(data[0].sales_person);
						$('#inv_fnl_ttl').text(data[0].inv_fnl_ttl);
						$('#inv_id').val(inv_id);					
						$('#btn_action').val('pay_inv');
						//each of all previous payment
						var payment_dtl = "";
						payment_dtl += "<tr><td><s>N</s> "+parseFloat(data[i].pay_amt)+"</td>";
						payment_dtl += "<td>"+data[i].pay_date+"</td>";
						payment_dtl += "<td>"+data[i].pay_type+"</td>";
						payment_dtl += "<td>"+data[i].paid_to+"</td></tr>";
						//append previous payment detail to tbl
						$('.pay_info').append(payment_dtl);
						//sums all previous payments made
						ttl_pay_amt += parseFloat(data[i].pay_amt);
					}

					$('#hidden_outstanding_amt').val(parseFloat(data[0].outstanding_amt-ttl_pay_amt));
					$('#pay_amt').attr('placeholder',parseFloat(data[0].outstanding_amt-ttl_pay_amt));
					$('#pay_modal').modal('show');
				}
			});
			
		});

		$(document).on('keyup','#pay_amt', function(){
			if ($.trim($(this).val()) != '' || $.trim($(this).val()) <= 0) {
				$('#no_outstanding').attr('required',true);
				$('#cash_pay').attr('required',true);
				//set outstanding_amt placeholder
				var outstanding_amt = $(this).attr('placeholder') - $(this).val();
				$('#outstanding_amt').attr('placeholder',outstanding_amt);
			}
			else{
				$('#no_outstanding').attr('required',false);
				$('#cash_pay').attr('required',false);
				$('#outstanding_amt').attr('placeholder',$(this).attr('placeholder'));
			}
		});

		$(document).on('click','#yes_outstanding', function(){
			$('#fill_outstanding').collapse('show');
			$('#fill_outstanding').find('input').attr('required',true);

			
		});

		$(document).on('click','#no_outstanding', function(){
			$('#fill_outstanding').collapse('hide');
			$('#fill_outstanding').find('input').attr('required',false);
			
		});

		$(".modal").on("hidden.bs.modal", function(){
			$('#pay_form')[0].reset();
			$('#fill_outstanding').collapse('hide');
			$('.pay_info').html('');
        });

        $(document).on('submit','#pay_form',function (event) {
			event.preventDefault();
			//$('#action').attr('disabled','disabled');
			var form_data = $(this).serialize();
				$.ajax({
				url: 'process_payment.php',
				method: 'post',
				data: form_data,
				success:function(data) {
					var html = "";
					if(data == "Payment recieved successfully!"){
						html = '<div class="font-weight-bold text-success">'+data+'</div>';
					}
					else{
						html = '<div class="font-weight-bold text-danger">'+data+'</div>';
					}
					$('#pay_form')[0].reset(); //reset all form field			
					$('#pay_modal').modal('hide');
					$('#alert-modal').find('.modal-title').html('Alert');
					$('#alert-modal').find('.modal-body').html(html);
					$('#alert-modal').modal('show');
					//$('#alert-action').fadeIn().html(html);
					inv_tbl.ajax.reload();  //will reload userdataTable on webpage
				}
			});	
		});
	});
</script>
</body>
</html>
