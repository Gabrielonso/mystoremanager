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
					<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
						<h3 class="card-title" align="center">GOODS ITEM <i class="fa fa-cubes"></i>
						</h3>	
					</div>
					<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
						<a href="create_new_item.php" target="_blank" class="btn btn-success btn-sm">Create New</a>
					</div>
				</div>
				<div class="clear:both"></div>
			</div>
			<div class="card-body">
				<div class="row"><div class="col-sm-12 table-responsive">
						<table id="goods_itm_list" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th></th>
									<th colspan="2" style="text-align: center;">Current Stock <span class="glyphicon glyphicon-stats text-success"></span></th>
									<th></th>
									<th colspan="3" style="text-align: center;">Prices (<s class="text-primary">N</s>)</th>
									<th></th>
									<th></th>
								</tr>
								<tr>
									<th>Goods Item Name</th>
									<th>In warehouse (cartons)</th>
									<th>In retail (units/pieces)</th>
									<th>unit per ctn</th>
									<th>Carton price</th>
									<th>Unit price</th>
									<th>Purchase price(ctn)</th>
									<th>Edit</th>
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
<div id="goods_itm_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="goods_itm_form">
				<div class="modal-header">
					<h4 class="modal-title">Update Item</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body font-weight-bold">
					<div class="row">
						<div class="form-group col-lg-12">
							<label for="item_name">Goods Item Name:</label><span class="mssg text-danger font-weight-bold"></span>
							<input type="text" name="item_name" id="item_name" class="form-control" required placeholder="Enter Goods Item Name...">
							<div class="list-group font-weight-normal">
									<!-- autocomplete search list goes here -->			
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-lg-6" >
							<label>Unit per ctn</label>
							<div class="input-group mb-3" style="z-index: 1">
								<input type="number" name="unit_per_ctn" id="unit_per_ctn" class="form-control unit_per_ctn">
								<div class="input-group-append">
									<span class="input-group-text">pcs/ctn</span>
								</div>
							</div>
						</div>
						<div class="form-group col-lg-6">
								<label for="purchase_price">Purchase price(ctn)</label>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
							        	<s class="input-group-text">N</s>
							      	</div>
									<input type="number" name="purchase_price" id="purchase_price" class="form-control" required min="0" placeholder="Enter purchase price...">
								</div>
						</div>
					</div>
					<div class="row prices" id="prices">
						<div class="col-lg-12">
							<div class="font-weight-light text-center">Selling price (<s>N</s>)</div>
							<div class="form-row">
								<div class="form-group col-lg-6">
									<label for="ctn_price">ctn price</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
							        		<s class="input-group-text">N</s>
							      		</div>
										<input type="number" name="ctn_price" id="ctn_price" class="form-control" required min="0" placeholder="Enter carton price...">
									</div>
								</div>
								<div class="form-group col-lg-6">
									<label for="unit">unit price</label>
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
					<input type="hidden" name="btn_action" id="btn_action" value="update_goods_item">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Save">
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
		var table = $('#goods_itm_list');
		sticky_header(table);
		var fetch_tbl = 'goods_itm_list';
		var goods_itm_list_tbl = $("#goods_itm_list").DataTable({
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
					//remove column order sorting from column index 4,5
					"targets":[7,8],
					"orderable": false
				}],
			"pageLength":25			
		});

		

		$(document).on('keyup','#item_name',function(){
			var display_area = $(this).next('.list-group');
			var	search_val = $.trim($(this).val());
			display_goods_item_list(search_val,display_area);
		});

		$(document).on('blur','#item_name',function(){
			$(this).next('.list-group').css("display","none");
		});

		$(document).on('click', '.update_goods_item', function () {
			var item_id = $(this).attr("id");
			var btn_action = 'fetch_dtls';
			$.ajax({
				url: 'goods_item_action.php',
				method: 'post',
				data: {item_id:item_id, btn_action:btn_action},
				dataType:'json',
				success:function(data){
					$('#goods_itm_modal').modal('show');	
					$('#item_name').val(data.item_name);
					$('#purchase_price').val(data.purchase_price);
					$('#unit_price').val(data.unit_price);
					$('#ctn_price').val(data.ctn_price);
					$('#unit_per_ctn').val(data.unit_per_ctn);
					$('#item_id').val(item_id);
					$('#btn_action').val('update_goods_item');
				}
			});
		})


		$(".modal").on("hidden.bs.modal", function(){
			$('#goods_itm_form')[0].reset();
			$('#action').attr('disabled', false);
			$('.mssg').text('');
			$('#item_name').removeClass('shadow-danger');

        });

		//submit user form datas
		$(document).on('submit','#goods_itm_form', function (event) {
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url: 'goods_item_action.php',
				method: 'post',
				data: form_data,
				success:function(data) {
					if (data == '*Invalid Input!') {
						$('.mssg').text(data);
						$('#item_name').focus();
						$('#item_name').addClass('shadow-danger');
						$('#action').attr('disabled', false);
					}
					else{
						$('#goods_itm_form')[0].reset(); //reset all form field
						$('#goods_itm_modal').modal('hide');
						$('#action').attr('disabled', false);
						$('#alert-modal').find('.modal-title').text('Alert!');
						$('#alert-modal').find('.modal-body').html(data);
						$('#alert-modal').modal('show');
						goods_itm_list_tbl.ajax.reload();   //reload userdataTable
					}
					
				}
			});
		});


		//deleting wh goods item
		$(document).on('click', '.delete_item', function(){
			var item_id = $(this).attr("id");
			var btn_action = 'delete_record';
			if (confirm('Are you sure you want to remove this record?')) {
				//when we click ok, this will execute
				$.ajax({
					url: 'goods_item_action.php',
					method: 'post',
					data: {item_id:item_id, btn_action:btn_action},
					success:function(data){
						$('#alert-modal .modal-title').text('Alert!');
						$('#alert-modal').modal('show');
						$('#alert-modal').find('.modal-body').html(data);
						goods_itm_list_tbl.ajax.reload();
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