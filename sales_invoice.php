<?php
include 'my_db.class.php';
if (!isset($_SESSION['type'])) {
	header("location:logout.php");
}
//include 'header.php';
include 'header.php';

?>

<div class="container-fluid">
<?php
/*	if (isset($_GET['add'])) {*/
	
?>
										<!-----------create invoice------------>

	<form id="invoice_form">
		<div class="table-responsive">
			<table class="table table-bordered">
				<tr>
					<td colspan="2" align="center"><h2 style="margin-top: 10.5px">Create Invoice</h2></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<div class="btn-group btn-group-sm col-sm-12">
							<a href="sales_invoice.php" target="_blank" class="btn btn-primary rounded-0">New Invoice</a>	
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="container">
							<div class="row">
								<div class="form-group col-sm-6">
									<b>To,<br>
									RECIEVER(BILL TO)</b>
									<input type="text" name="cust_name" id="cust_name" class="form-control input-sm" placeholder="Enter Reciever Name" pattern="^[^\s].+" title="Should not begin with white space" required>
									<br>
									<input type="number" name="cust_mobile_no" id="cust_mobile_no" class="form-control input-sm" placeholder="Enter Phone No.">
									<br>
									<textarea name="cust_address" id="cust_address" class="form-control" placeholder="Enter Billing Address"></textarea>
								</div>
								<div class="form-group col-sm-3">
									<label>Date</label>
									<input type="date" name="inv_date" id="inv_date" class="form-control input-sm" placeholder="Select Invoice Date" readonly>
									<input type="hidden" name="inv_time" id="inv_time">
									<input type="hidden" name="inv_datetime" id="inv_datetime">
								</div>
								<div class="form-group col-sm-3">
									<label>Invoice No</label>
									<input type="text" name="inv_no" id="inv_no" class="form-control input-sm" placeholder="Enter Invoice No." readonly>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6"></div>
							<div class="col-sm-6 text-center font-weight-bold">Add Stocks</div>
						</div>
						<div class="row">
							<div class="btn-group btn-group-sm col-sm-6"></div>
							<div class="btn-group btn-group-sm col-sm-6">
								<a href="purchase.php" target="_blank" class="btn btn-warning">In cartons</a>
								<a href="rtl_goods_store.php" target="_blank" class="btn btn-info">In pieces</a>
							</div>
						</div>
						<div class="table-responsive">
							<table id="orders_tbl" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>S/N</th>
										<th width="43%">Goods Item</th>
										<th width="20%">Quantity</th>
										<th width="17%">Price</th>
										<th width="20%">Amount</th>
										<th></th>
									</tr>
								</thead>
								
								<tr id ="row_id_1"  class="row_no">
									<td><span id="sr_no">1</span></td>
									<td class="goods_list">
										<div>
											<input type="text" name="order_item[]" id="order_item1" class="form-control input-sm order_item" required>
											<div class="list-group">
												
											</div>
											<input type="hidden" name="item_id[]" id="item_id1" class="item_id">
										</div>
										<span style="position: absolute;" class="text-danger font-weight-bold mssg"></span>
									</td>
									<td>
										<div class="input-group">
											<div class="input-group-prepend dropdown">
												<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
												</button>
		      									<div class="dropdown-menu">
													<a href="#" class="ctn dropdown-item">ctn</a>
													<a href="#" class="pcs dropdown-item">pcs</a>
												</div>
												<span class="input-group-text"></span>
											</div>									
											<input type="text" name="order_qty[]" id="order_qty1" data-srno="1" class="form-control number_only order_qty" min="0" pattern="^[0-9\.]+$" title="Remove spaces. Digit only" required>
										</div>
										<span style="position: absolute;" class="text-danger font-weight-bold mssg"></span>
										<input type="hidden" name="qty_type[]" class="qty_type">
									</td>
									<td>
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text"><s>N</s></span>
											</div>
											<input type="number" name="order_price[]" id="order_price1" data-srno="1" class="form-control input-sm number_only order_price" min="0" pattern="^[0-9\.]+$" title="Remove spaces. Digit only" required>
										</div>
										<input type="hidden" class="ctn_price">
										<input type="hidden" class="unit_price">

									</td>
									<td>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><s>N</s></span>
											</div>
											<input type="text" name="order_amt[]" id="order_amt1" data-srno="1" class="form-control input-sm order_act_amt" readonly>
										</div>
										
									</td>
									<td>
										
									</td>
								</tr>
							</table>
						</div>
						<div align="center">
							<button type="button" name="add_row" id="add_row" class="btn btn-success rounded-0">Add Item</button>
						</div>
					</td>
				</tr>
				<tr>
					<td align="left" width="70%"></td>
					<td align="right" width="30%">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text"><b>Subtotal: <s>N</s></b></span>
							</div>
      						<input id="inv_sub_ttl" type="text" class="form-control" name="inv_sub_ttl" readonly>
					    </div>
					    <div class="input-group mb-3">
					    	<div class="input-group-prepend">
					    		<span class="input-group-text"><b>Discount: <s>N</s></b></span>
					    	</div>
					      <input id="inv_dsc_ttl" type="text" class="form-control" name="inv_dsc_ttl" min="0" pattern="^[0-9\.]+$" title="Remove spaces. Digit only">
					    </div>
					    <div class="input-group mb-3">
					    	<div class="input-group-prepend">
					    		<span class="input-group-text"><b>Total: <s>N</s></b></span>
					    	</div>
					      <input id="inv_fnl_ttl" type="text" class="form-control" name="inv_fnl_ttl" readonly>
					    </div>					    
					</td>
				</tr>
				<tr>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="hidden" name="invoice_action" id="invoice_action" value="create_invoice">
						<input type="hidden" name="no_of_orders" id="no_of_orders" value="1">
						<input type="submit" name="create_invoice" id="create_invoice" class="btn btn-block btn-primary rounded-0" value="Create">
						<input type="hidden" name="invoice_id" id="invoice_id" >
					</td>
					<div class="message"></div>
				</tr>
			</table>
		</div>
	</form>
</div>
</div>
</div>
<?php
include 'footer.php';
?>
<script>
	$(document).ready(function(){

		var table = $('#orders_tbl');
		sticky_header(table);

		//resize_input_fields();
		/*...............autocomplete search goods item................*/
		$(document).on('keyup','.order_item',function(){
			var display_area = $(this).next('.list-group');
			var	search_val = $.trim($(this).val());
			display_goods_item_list(search_val,display_area);
		});

		/*........when select goods item from displayed list.......*/
		$(document).on('click', '.select_item', function (event) {
			event.preventDefault();
			var order_action = 'get_itm_dtl';
			var order_item_text_field = $(this).parent('.list-group').prev('input.order_item');
			var item_id = $(this).attr('id');
			$(this).parent('.list-group').next('.item_id').val(item_id);
			$(order_item_text_field).val($(this).text());			
			//row_id for this item
			var row_id = $(this).closest('.row_no').attr('id');
			//get this goods item prices
			var order_qty = $('#'+row_id).find('.order_qty').val();
			$.ajax({
				url: 'process_orders.php',
				method: 'post',
				data: {item_id:item_id, order_action:order_action},
				dataType: 'json',
				success: function (data) {
					//append ctn and unit prices to fields
					$("[id="+row_id+"]").find(".order_price").val(data.ctn_price);
					$("[id="+row_id+"]").find(".dropdown .input-group-text").text('ctn');
					$("[id="+row_id+"]").find(".qty_type").val('ctn');
					$("[id="+row_id+"]").find(".ctn_price").val(data.ctn_price);
					$("[id="+row_id+"]").find(".unit_price").val(data.unit_price);
					cal_fnl_ttl(count);

				} 
			});
			$(this).parent(".list-group").html('');
			$(this).parent(".list-group").css("display","none");
			order_validity_check(item_id,'ctn',$.trim(parseFloat(order_qty)),row_id);
		});

		/*.........select ctn qty_type.........*/
		$(document).on('click','.ctn', function (event) {
			event.preventDefault();
			//this item row_id
			var row_id = $(this).closest('.row_no').attr('id');
			$("[id="+row_id+"]").find(".qty_type").val('ctn');		
			var qty_type = 'ctn';
			var item_id = $("[id="+row_id+"]").find(".item_id").val();
			var order_qty = $("[id="+row_id+"]").find(".order_qty").val();
			var ctn_price = $("[id="+row_id+"]").find(".ctn_price").val();
			$("[id="+row_id+"]").find(".dropdown .input-group-text").text('ctn');
			//append ctn price to order price text field
			$("[id="+row_id+"]").find(".order_price").val(ctn_price);

			order_validity_check(item_id,qty_type,$.trim(parseFloat(order_qty)),row_id);
			/*if ($.trim(parseFloat(order_qty)) != 0 && item_id != '') {
				stock_availability_status(item_id,qty_type,order_qty,row_id);		
			}*/
			cal_fnl_ttl(count);
		});

		/*..........select pcs qty_type...........*/
		$(document).on('click','.pcs', function (event) {
			event.preventDefault();
			//this item row_id
			var row_id = $(this).closest('.row_no').attr('id');
			$("[id="+row_id+"]").find(".qty_type").val('pcs');	
			var qty_type = 'pcs';
			var item_id = $("[id="+row_id+"]").find(".item_id").val();
			var order_qty = $("[id="+row_id+"]").find(".order_qty").val();
			var unit_price = $("[id="+row_id+"]").find(".unit_price").val();
			$("[id="+row_id+"]").find(".dropdown .input-group-text").text('pcs');
			//append unit price to order price text field	
			$("[id="+row_id+"]").find(".order_price").val(unit_price);
			order_validity_check(item_id,qty_type,$.trim(parseFloat(order_qty)),row_id);
			/*if ($.trim(parseFloat(order_qty)) != '' && item_id != '') {
				stock_availability_status(item_id,qty_type,order_qty,row_id);		
			}*/
			cal_fnl_ttl(count);
		});

		/*............type in order_qty...........*/
		$(document).on('keyup', '.order_qty', function () {
			var row_id = $(this).parents('.row_no').attr('id');
			var order_qty = $(this).val();
			var item_id = $(this).parents('.row_no').find('.item_id').val();
			var qty_type = $(this).parents('.row_no').find('.qty_type').val();
			order_validity_check(item_id,qty_type,order_qty,row_id);
			//stock_availability_status(item_id,qty_type,order_qty,row_id);
			cal_fnl_ttl(count);
		});

		//........type in order_price....
		$(document).on('keyup', '.order_price', function () {
			cal_fnl_ttl(count);
		});

	<?php
		/*.............editing existing invoice..............*/
		if (isset($_GET['update']) && isset($_GET['id'])) {

			$invoice_keys = "*";
			$invoice_tbl = "sales_inv_tbl";
			$query_condtn = "WHERE inv_id = :inv_id LIMIT 1";
			$bind_inv_val['inv_id'] = addslashes(floatval(trim($_GET['id'])));

			$prev_inv_dtl = $query->select_assoc_bind($invoice_keys,$invoice_tbl,$query_condtn,$bind_inv_val,$pdo);

			foreach ($prev_inv_dtl as $p_inv_dtl) {
	?>		
				//append invoice details to text field
				$('#inv_no').val("<?php echo $p_inv_dtl['inv_no'];?>").attr("readonly", true);
				$('#inv_date').val("<?php echo $p_inv_dtl['inv_date'];?>").attr("readonly", true);
				$('#inv_time').val("<?php echo $p_inv_dtl['inv_time'];?>");
				$('#inv_datetime').val("<?php echo $p_inv_dtl['inv_datetime'];?>");
				$('#cust_name').val("<?php echo $p_inv_dtl['cust_name'];?>").attr("readonly", true);
				$('#cust_mobile_no').val("<?php echo $p_inv_dtl['cust_mobile_no'];?>");
				$('#cust_address').val("<?php echo $p_inv_dtl['cust_address'];?>");
				$('#inv_dsc_ttl').val("<?php echo $p_inv_dtl['inv_dsc_ttl'];?>");
				$('#create_invoice').val("Update");
				$('#invoice_id').val("<?php echo $p_inv_dtl['inv_id'];?>");
				$('#invoice_action').val('edit_invoice');

				//removes existing rows from tbl
				$('#orders_tbl').find('.row_no').remove();
	<?php
			}
			//from cust_orders_tbl
			$orders_keys = "*";
			$orders_tbl = "cust_orders_tbl";
			$query_condtn = "WHERE inv_id = :inv_id";
			$prev_orders = $query->select_assoc_bind($orders_keys,$orders_tbl,$query_condtn,$bind_inv_val,$pdo);

			//set unique num id
			$m = 0;

			foreach ($prev_orders as $p_order) {
				//increment $m for each row inserted
				$m = $m + 1;
	?>			
				//resize_input_fields();
				//table row
				var html_code = '';
				html_code += '<tr id ="row_id_'+<?php echo $m; ?>+'" class = "row_no">';
				html_code += '<td><span id="sr_no">'+<?php echo $m; ?>+'</span></td>';
				html_code +='<td><div><input type="text" name="order_item[]" id="order_item'+<?php echo $m; ?>+'" class="form-control input-sm order_item" required value="<?php echo htmlentities($p_order['order_item'], ENT_QUOTES) ?>">';
				html_code +='<div class="list-group"></div>';
				html_code += '<input type="hidden" name="item_id[]" class="item_id" id="item_id'+<?php echo $m; ?>+'" value ="<?php echo $p_order['item_id'] ?>"></div><span style="position: absolute;" class="text-danger font-weight-bold mssg"></span></td>';
				html_code +='<td><div class="input-group">';
				html_code += '<div class="input-group-prepend dropdown">';
				html_code += '<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"></button>';
				html_code += '<div class="dropdown-menu">';
				html_code += '<a href="#" class= "ctn dropdown-item">ctn</a><a href="#" class="pcs dropdown-item">pcs</a></div>';
					//html_code += '';
				html_code += '<span class="input-group-text"><?php echo $p_order['qty_type'] ?></span></div>';
				html_code += '<input type="text" name="order_qty[]" id="order_qty'+<?php echo $m; ?>+'" data-srno="'+<?php echo $m; ?>+'" class="form-control number_only order_qty" min="0" pattern="^[0-9\.]+$" title="Remove spaces. Digit only" required value="<?php echo $p_order['order_qty'] ?>"></div>';
				html_code += '<span style="position: absolute;" class="text-danger font-weight-bold mssg"></span><input type="hidden" name="qty_type[]" class="qty_type" value="<?php echo $p_order['qty_type'] ?>"></td>';
				html_code += '<td><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><s>N</s></span></div>';
				html_code += '<input type="text" name="order_price[]" id="order_price'+<?php echo $m; ?>+'" data-srno="'+<?php echo $m; ?>+'" class="form-control input-sm number_only order_price" min="0" pattern="^[0-9\.]+$" title="Remove spaces. Digit only" required  value="<?php echo $p_order['order_price'] ?>"></div>';
				html_code += '<input type="hidden" class="ctn_price">';
				html_code += '<input type="hidden" class="unit_price"></td>';
				html_code += '<td><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><s>N</s></span></div>';
				html_code += '<input type="text" name="order_amt[]" id="order_amt'+<?php echo $m; ?>+'" data-srno="'+<?php echo $m; ?>+'" class="form-control input-sm order_act_amt" readonly  value="<?php echo $p_order['order_amt'] ?>"></div></td>';
				html_code += '<td><button type="button" name="remove_row" id="'+<?php echo $m; ?>+'" class="btn btn-danger btn-sm remove_row">Remove</button></td></tr>';

				//append each table row
				$('#orders_tbl').append(html_code);
	<?php
			}
	?>		
			//num of rows inserted
			var no_of_rows = $('.row_no').length;
			var count = no_of_rows;
			$('#no_of_orders').val(no_of_rows);

			cal_fnl_ttl(count);
	<?php
		}
		else{
	?>
			//else num of rows = 1
			var count = 1;
	<?php	
		}
	?>
		/*................add row................*/
		$(document).on('click','#add_row',function () {
			//increment count/num of rows
			count++;
			//table row
			var html_code = '';
			html_code += '<tr id ="row_id_'+count+'" class = "row_no">';
			html_code += '<td><span id="sr_no">'+count+'</span></td>';
			html_code +='<td><div><input type="text" name="order_item[]" id="order_item'+count+'" class="form-control input-sm order_item" required>';
			html_code +='<div class="list-group"></div>';
			html_code += '<input type="hidden" name="item_id[]" class="item_id" id="item_id'+count+'"></div><span style="position: absolute;" class="text-danger font-weight-bold mssg"></span></td>';
			html_code +='<td><div class="input-group">';
			html_code += '<div class="input-group-prepend dropdown">';
			html_code += '<button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"></button>';
			html_code += '<div class="dropdown-menu">';
			html_code += '<a href="#" class= "ctn dropdown-item">ctn</a><a href="#" class="pcs dropdown-item">pcs</a></div>';
				//html_code += '';
			html_code += '<span class="input-group-text"></span></div>';
			html_code += '<input type="text" name="order_qty[]" id="order_qty'+count+'" data-srno="'+count+'" class="form-control number_only order_qty" min="0" pattern="^[0-9\.]+$" title="Remove spaces. Digit only" required></div>';
			html_code += '<span style="position: absolute;" class="text-danger font-weight-bold mssg"></span><input type="hidden" name="qty_type[]" class="qty_type"></td>';
			html_code += '<td><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><s>N</s></span></div>';
			html_code += '<input type="text" name="order_price[]" id="order_price'+count+'" data-srno="'+count+'" class="form-control input-sm number_only order_price" min="0" pattern="^[0-9\.]+$" title="Remove spaces. Digit only" required></div>';
			html_code += '<input type="hidden" class="ctn_price">';
			html_code += '<input type="hidden" class="unit_price"></td>';
			html_code += '<td><div class="input-group"><div class="input-group-prepend"><span class ="input-group-text"><s>N</s></span></div>';
			html_code +='<input type="text" name="order_amt[]" id="order_amt'+count+'" data-srno="'+count+'" class="form-control input-sm order_act_amt" readonly></td>';
			html_code += '<td><button type="button" name="remove_row" id="'+count+'" class="btn btn-danger btn-sm remove_row">Remove</button></td></tr>';

			//append tbl row and set ttl no of rows/orders
			$('#orders_tbl').append(html_code);
			var no_of_rows = $('.row_no').length;
			$('#no_of_orders').val(no_of_rows);
			cal_fnl_ttl(count);
		});

		/*............remove row............*/
		$(document).on('click', '.remove_row', function() {

			var row_id = $(this).attr("id");		
			var order_amt = $('#order_amt'+row_id).val();
			var inv_sub_ttl = $('#inv_sub_ttl').val();
			//subtract order_amt from invoice total
			var sub_result = parseFloat(inv_sub_ttl) - parseFloat(order_amt);
			$('#inv_sub_ttl').val(sub_result);
			$('#row_id_'+row_id).remove(); //remove row

			//set no of remaining orders/rows
			var no_of_rows = $('.row_no').length;
			$('#no_of_orders').val(no_of_rows);
			cal_fnl_ttl(count);
		});

		$(document).on('keyup', '#inv_dsc_ttl', function () {
			cal_fnl_ttl(count);
		});

		var inv_sub_ttl = $('#inv_sub_ttl').val();
		var inv_fnl_ttl = $('#inv_fnl_ttl').val();

		/*............calculation............*/
		function cal_fnl_ttl(count) {
				
			var inv_sub_ttl = 0;

			//for each row,
			for (j = 1; j <= count; j++) {
				var order_qty = 0;
				var order_price = 0;
				var order_amt = 0;

				order_qty = $.trim($('#order_qty'+j).val());
				//if order_qty is > 0,
				if (order_qty > 0) {
					order_price = $.trim($('#order_price'+j).val());
					//if order_price is > 0
					if (order_price > 0) {
						//calc ttl order_amt for this row 
						order_amt = parseFloat(order_qty) * parseFloat(order_price);
						//set order_amt for this row
						$('#order_amt'+j).val(order_amt);

						//calc inv_sub_ttl
						inv_sub_ttl = parseFloat(inv_sub_ttl) + parseFloat(order_amt);    
						$('#order_amt'+j).val(order_amt); 
					}
				}
			}

			var inv_dsc_ttl = $.trim($('#inv_dsc_ttl').val());
			if (inv_dsc_ttl > 0) {
				inv_fnl_ttl = parseFloat(inv_sub_ttl) - parseFloat(inv_dsc_ttl);
			}
			else{
				inv_fnl_ttl = parseFloat(inv_sub_ttl);
			}

			$('#inv_sub_ttl').val(inv_sub_ttl);
			$('#inv_fnl_ttl').val(inv_fnl_ttl);
		}

		/*.........submit invoice_form...........*/
		$(document).on('submit','#invoice_form',function (event) {
			event.preventDefault();
			//calculate
			cal_fnl_ttl(count);
			//$('#create_invoice').attr('disabled','disabled');
			$('#loading-modal').modal('show');
			var form_data = $(this).serialize();
			$.ajax({
				url: 'process_orders.php',
				method: 'post',
				data: form_data,
				//dataType: 'json',
				success:function(data) {
				    setTimeout(function () {
						$('#loading-modal').modal('hide');
					},1000);
				    
					if (data == "Invoice created sucessfully!" || data == "Invoice edited sucessfully!") {

						alert(data);

						if (confirm('Do you want to create a new invoice?')) {
							//$('#invoice_form')[0].reset();
							window.location.href = 'sales_invoice.php';
						}
						else{
							window.location.href = 'sales.php';
						}		
					}
					else if(data == "Unable to create invoice!" || data == "Unable to edit invoice!"){

						var html = '<div class="text-danger font-weight-bold">'+data+'</div>';
						$('#alert-modal').find('.modal-body').html(html);
							$('#alert-modal').modal('show');
					}
					else{
						//if a particular stock is unavailable
						var response = JSON.parse(data);
						var item_id = response.item_id;
						var order_qty = response.order_qty;
						var qty_type = response.qty_type;
						var row_id = $('input[value="'+item_id+'"]').parents().has('input[value="'+qty_type+'"]').first().attr('id');

						stock_availability_status(item_id,qty_type,order_qty,row_id);
					}
				}
			});
		});

		/*.......check item_id and qty_type if order already exist.......*/
		function order_validity_check(item_id,qty_type,order_qty,row_id){
			var row_id,item_id,qty_type;
			var existing_row_id, existing_item_id, existing_qty_type;
			var text_field = $('#'+row_id).find('.order_item');
			var no_of_iterations = 0;
			$('.row_no').each(function () {
				no_of_iterations++;
				existing_row_id = $(this).attr('id');
				existing_item_id = $('#'+existing_row_id).find('.item_id').val();
				existing_qty_type = $('#'+existing_row_id).find('.qty_type').val();
				existing_sr_no = $('#'+existing_row_id).find('#sr_no').text();
				
				if($.trim(item_id) != '' && $.trim(qty_type) != ''){
					if (item_id == existing_item_id && qty_type == existing_qty_type){
						if(row_id != existing_row_id){	
							$('#'+row_id).find('.order_item').attr('title','This order already exist in  S/N : '+existing_sr_no+'. Change order or qty type');
							$('#'+row_id).find('.order_item').parent().next('.mssg').text('This order already exist in  S/N : '+existing_sr_no+'. Change order or qty type');

							disable_btns_and_focus(text_field);
							//break iteration
							return false;
						}	
					}
					else{
						enable_btns(text_field);
					}
				}	
			});
			//on complete iteration,
			if (no_of_iterations == count){
				if ($.trim(parseFloat(order_qty)) != 0 && item_id != '') {
					//verify stock availability
					stock_availability_status(item_id,qty_type,order_qty,row_id);		
				}
			}
		}

		/*........indicate stock availability......*/
		function stock_availability_status(item_id,qty_type,order_qty,row_id) {
			var order_action = 'get_itm_dtl';	
			$.ajax({
				url: 'process_orders.php',
				method: 'post',
				data: {item_id:item_id, order_qty:order_qty, qty_type:qty_type, order_action:order_action},
				dataType: 'json',
				success: function (data) {
					var text_field = $('#'+row_id).find('.order_qty');
					//if an order_qty > curr available stock
					if (parseFloat(order_qty) > parseFloat(data.curr_stock) || data.curr_stock === null) {
						//set error title
						$('#'+row_id).find('.order_qty').attr('title','Insufficient goods in stock. Only '+data.curr_stock+' '+qty_type+' available');
						$('#'+row_id).find('.order_qty').parent().next('.mssg').text('Insufficient goods in stock. Only '+data.curr_stock+' '+qty_type+' available');
						disable_btns_and_focus(text_field);
						return false;
					}
					else{
						enable_btns(text_field);
					}
				}
			});
		}

		function disable_btns_and_focus(text_field) {
			text_field.addClass('shadow-danger');
			text_field.focus();
			$('#add_row').attr('disabled','disabled');
			$('.remove_row').attr('disabled','disabled');
			$('#create_invoice').attr('disabled','disabled');
		}

		function enable_btns(text_field){
			text_field.removeClass('shadow-danger');
			text_field.removeAttr('title');
			text_field.parent().next('.mssg').text('');
			$('#add_row').attr('disabled',false);
			$('.remove_row').attr('disabled',false);
			$('#create_invoice').attr('disabled',false);
		}
	});
</script>
</body>
</html>