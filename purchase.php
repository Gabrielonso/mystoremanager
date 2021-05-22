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
			<div class="card-body">
			<h4 class="text-center">Search item to view/add supplies</h4>
				<p class="text-danger font-weight-bold" id="search_error" style="padding-left:20%; margin:0;"></p>
				<div class="row">
					<div class="col-lg-2"></div>
					<div class="col-lg-8" align="center">
						<div class="form-group" style="position:relative;">
							<div class="input-group mb-3 input-group-lg" style="position:relative;">
								<input type="text" name="search_item_name" class="form-control search_item_name" placeholder="Search goods item...">
								<div class="input-group-append">
									<button class="btn btn-success search_btn">
										<i class='fas fa-plus'></i> Add Item
									</button>
								</div>
							</div>
							<div class="list-group text-left" style="position:absolute; top:100%; width:50%; z-index:3;">

							</div>
						</div>
					</div>
					<div class="col-lg-2 font-weight-bold" align="right"><a href="create_new_item.php" target="_blank" data-toggle="tooltip" title="Ensure that item does not already exist in record">Create New Item?</a></div>
				</div>
				<div class="row"><div class="col-sm-12 table-responsive">
						<table id="supplies_tbl" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Goods Item</th>
									<th>Supply datetime</th>
									<th>Supplier</th>
									<th>Supply qty</th>
									<th>Supply price</th>
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
<div id="add_supply_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="add_supply_form">
				<div class="modal-header">
					<h4 class="modal-title">Add Supply</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body font-weight-bold">
					<div class="row">
						<div class="form-group col-lg-12">
							<label for="item_name">Goods Item Name:</label>
							<input type="text" name="item_name" id="item_name" class="form-control" required placeholder="Enter Goods Item Name..." readonly>
							<div class="list-group font-weight-normal">
									<!-- autocomplete search list goes here -->			
							</div>
						</div>
					</div>
					<div id="supply_dtl">
						<div class="form-row">
							<div class="form-group col-lg-5">
								<label for="supplier">Supplier:</label>
								<div style="z-index: 1">
									<input type="text" name="supplier" id="supplier" class="form-control supplier" placeholder="Enter supplier...">
								</div>
							</div>
							<div class="form-group col-lg-3">
								<label for="supply_qty">Supply Qty</label><span class="mssg text-danger font-weight-bold" style="position:absolute; left:0; top:20%"></span>
								<input type="number" name="supply_qty" id="supply_qty" class="form-control supply_qty" min="0" placeholder="Enter supplied qty..." required>
							</div>
							<div class="form-group col-lg-4">
								<label for="supply_price">Supply price:</label>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text"><s>N</s></span>
									</div>
									<input type="number" name="supply_price" id="supply_price" class="form-control supply_price" min="0"  placeholder="Enter purchase price...">
								</div>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-lg-4">
							<label for="in_rtl">In Retail:</label>
							<input type="text" id="in_rtl" class="form-control" readonly>
						</div>
						<div class="form-group col-lg-4">
							<label for="in_wh">In Warehouse</label>
							<input type="text" id="in_wh" class="form-control" readonly>
						</div>
						<div class="form-group col-lg-4" >
							<label>Unit per ctn</label>
							<div class="input-group mb-3" style="z-index: 1">
								<input type="number" name="unit_per_ctn" id="unit_per_ctn" class="form-control unit_per_ctn">
								<div class="input-group-append">
									<span class="input-group-text">pcs/ctn</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row prices" id="prices">
						<div class="col-lg-12">
							<div class="font-weight-light text-center">Selling price (<s>N</s>)</div>
							<div class="form-row">
								<div class="form-group col-lg-6">
									<label for="ctn_price">ctn</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
							        		<s class="input-group-text">N</s>
							      		</div>
										<input type="number" name="ctn_price" id="ctn_price" class="form-control" required min="0" placeholder="Enter carton price...">
									</div>
								</div>
								<div class="form-group col-lg-6">
									<label for="unit">unit</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
							        		<s class="input-group-text">N</s>
							      		</div>
										<input type="number" name="unit_price" id="unit_price" class="form-control" required min="0" placeholder="Enter unit price...">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="item_id" id="item_id">
					<input type="hidden" name="btn_action" id="btn_action" value="add_supply">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Add">
					<button type="button" data-dismiss="modal" class="btn btn-outline-danger">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
</div>
<?php
include 'footer.php';
?>
<script>
	$(document).ready(function() {
		//activate tooltip
		 $('[data-toggle="tooltip"]').tooltip();

		//sticky header
		var table = $('#supplies_tbl');
		sticky_header(table);

		var fetch_tbl = 'supplies_tbl';
		var suppliesDataTable = $('#supplies_tbl').DataTable({
			"processing": true,    //activate processing option of DataTable's plugin
			"serverSide": true,   //activates serverSide operation
			"order": [],
			"ajax": {
					url: 'data_tables.php',
					type: 'POST',
					data: {fetch_tbl:fetch_tbl},
					dataType: 'json'
				},
			"columnDefs":[{
					//disable order sorting for column indexes
					"targets":[5],
					"orderable": false
				}],
			/* "language": {
            		"zeroRecords": "No supply record",
            		"infoEmpty": "No records available"
        			}, */
			"pageLength":25
				});

		$(document).on('keyup','.search_item_name, #item_name',function(){
			var display_area = $(this).parent().next('.list-group');
			var	search_val = $.trim($(this).val());
			display_goods_item_list(search_val,display_area);		

		});

		//select searched item
		$(document).on('click', '.select_item', function (event) {
			event.preventDefault();
			var text_field = $(this).parents('.form-group').find('.search_item_name');
			var item_id = $(this).attr('id');
			$(text_field).val($(this).text());
			$(text_field).attr('id',item_id);
			$(this).parent('.list-group').css("display","none");
			suppliesDataTable.search($(this).text()).draw();
		});

		$(document).on('keyup','#item_name',function(){
			var display_area = $(this).next('.list-group');
			var	search_val = $.trim($(this).val());
			display_goods_item_list(search_val,display_area);
		});

		$(document).on('blur','#item_name',function(){
			$(this).next('.list-group').css("display","none");
		});
	
		$(document).on('click','.search_btn', function (event) {

			var btn_action = 'fetch_stock_dtls';
			var search_item_name = $('.search_item_name').val();
			var search_item_id = $('.search_item_name').attr('id');
			if (search_item_name == '') {
				$('#search_error').text('*Invalid search');
			}
			else if ($('.search_item_name').parent().next('.list-group').css("display") == "block") {
				$('#search_error').text('*Select an item from list.');
			}
			else{

				$.ajax({
				url: 'purchase_action.php',
				method: 'post',
				data: {search_item_id:search_item_id, btn_action:btn_action},
				dataType:'json',
				success:function(data){
					$('#add_supply_modal').modal('show');
					$('#add_supply_modal .modal-title').text('Add Supply');	
					$('#item_name').val(data.item_name);				
					$('#unit_price').val(data.unit_price);
					$('#ctn_price').val(data.ctn_price);
					$('#unit_per_ctn').val(data.unit_per_ctn);
					$('#supply_price').val(data.purchase_price);
					$('#in_rtl').val(data.rtl_curr_stock);
					$('#in_wh').val(data.wh_curr_stock);
					$('#item_id').val(search_item_id);
					$('#btn_action').val('add_supply');
				}
				});
			}

		});

		$(".modal").on("hidden.bs.modal", function(){
			$('#add_supply_form')[0].reset();
			$('#action').attr('disabled', false);
			$('#unit_per_ctn').attr('required',false);
			$('.mssg').text('');
			$('#item_name').removeClass('shadow-danger');

        });

		//submit user form datas
		$(document).on('submit','#add_supply_form', function (event) {
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url: 'purchase_action.php',
				method: 'post',
				data: form_data,
				success:function(data) {
					if (data == '*Invalid Input!') {
						$('.mssg').text(data);
						$('#supply_qty').focus();
						$('#supply_qty').addClass('shadow-danger');
						$('#action').attr('disabled', false);
					}
					else{
						console.log(data);
						$('#add_supply_form')[0].reset(); //reset all form field
						$('#add_supply_modal').modal('hide');
						$('#action').attr('disabled', false);
						$('#alert-modal').find('.modal-title').text('Alert!');
						$('#alert-modal').find('.modal-body').html(data);
						$('#alert-modal').modal('show');
						supplies_tbl.ajax.reload();   //reload userdataTable
					}
					
				}
			});
		});

		$(document).on('keyup', '#supply_qty', function(){
			if($.trim($(this).val()) != ''){
				$('#unit_per_ctn').attr('required',true);
			}
			else{
				$('#unit_per_ctn').attr('required',false);
			}
		});

		$(document).on('click', '.delete', function(){
			var id = $(this).attr("id");
			var btn_action = 'delete';
			if (confirm('Are you sure you want to remove this purchase?')) {
				$.ajax({
					url: 'purchase_action.php',
					method: 'post',
					data: {id:id, btn_action:btn_action},
					success:function(data){
						$('#alert-modal').find('.modal-body').html(data);
						$('#alert-modal').modal('show');
						supplies_tbl.ajax.reload();
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