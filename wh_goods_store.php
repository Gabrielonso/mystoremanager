<?php
include 'my_db.class.php';

if (!isset($_SESSION['type'])) {
	header('location:logout.php');
}
/*
//if subuser tries to access this page it will redirect them
if ($_SESSION['type'] != 'master') {
	header('location:index.php');
}
*/
include 'header.php';

?>
<span id="alert-action"></span>
<div class="row">
	<div class="col-lg-12">
		<div class="card card-default">
			<div class="card-header">
				<div class="row">
					<div class="col-lg-12">
							<h3 class="card-title" align="center">Warehouse goods(In cartons)</h3>	
					</div>
				</div>

				<div class="clear:both"></div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12 table-responsive">
						<table id="wh_stock_tbl" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th></th>
									<th colspan="2" style="text-align: center;">Today's Record</th>
									<th colspan="4" style="text-align: center;">Total Stock Record</th>	
									<th></th>
									<th></th>
								</tr>
								<tr>
									<th>Goods Item Name</th>
									<th><i class='fas fa-download text-success'></i> Today In</th>
									<th><i class='fas fa-upload text-danger'></i> Today Out</th>
									<th>From (datetime) <i class='fas fa-calendar-alt'></i></th>
									<th><i class='far fa-play-circle text-info'></i> Start Qty</th>
									<th><i class='fas fa-download text-success'></i> In</th>
									<th><i class='fas fa-upload text-danger'></i> Out</th>
									<th><span class="glyphicon glyphicon-stats text-primary"></span> Current Stock</th>
									<th>View/<br>Edit</th>
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
<div id="wh_goods_modal" class="modal fade" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="itm_form">
				<div class="modal-header">
					<h4 class="modal-title">View/Edit warehouse stock</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body font-weight-bold">
				<div class="font-weight-bold" align="right"><a href="purchase.php" target="_blank">Add supply?</a></div>
					<div class="row">
						<div class="form-group col-lg-12" style="position:relative;">
							<label for="item_name">Goods Item Name:</label><span class="mssg text-danger font-weight-bold"></span>
							<div class="input-group mb-3" style="position:relative;">
								<div class="input-group-prepend">
									<button class="btn btn-primary reset" type="submit">
										<span class="glyphicon glyphicon-pencil"></span>
									</button>
								</div>
								<input type="text" name="item_name" id="item_name" class="form-control" required placeholder="Enter Goods Item Name...">
							</div>
							<div class="list-group font-weight-normal" style="position:absolute; top:80%;">
								<!-- displays autocomplete search items here -->
							</div>
						</div>
					</div>
					<div class="text-center font-weight-normal">Stock report from <span id="wh_strt_datetime"></span></div>
					<div class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<th width="30%">Starting Stock</th>
								<td id="wh_strt_stock"></td>
							</tr>
							<tr>
								<th>Total Recieved</th>
								<td id="wh_ins_since_strt"></td>
							</tr>
							<tr>
								<th>Total Shipped</th>
								<td id="wh_out_since_strt"></td>
							</tr>
						</table>
					</div>
					<div class="form-row">
						<div class="form-group col-lg-6">
							<label for="wh_curr_stock">Current Stock</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
							    	<button class="btn btn-primary reset" data-toggle="tooltip"  data-placement="top" type="submit" title="Resetting will change stock details">Reset</button>
							 	</div>
								<input type="number" name="wh_curr_stock" id="wh_curr_stock" class="form-control" readonly required min="0">
								<div class="input-group-append">
									<span class="input-group-text">ctns</span>
								</div>
								<input type="hidden" name="hidden_wh_curr_stock" id="hidden_wh_curr_stock">
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
							        		<button class="btn btn-primary reset" type="submit"><span class="glyphicon glyphicon-pencil"></span>
							        		</button>
							        		<s class="input-group-text">N</s>
							      		</div>
										<input type="number" name="ctn_price" id="ctn_price" class="form-control" readonly required min="0" placeholder="Enter carton price...">
									</div>
								</div>
								<div class="form-group col-lg-6">
									<label for="unit">unit</label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
							        		<button class="btn btn-primary reset" type="submit"><span class="glyphicon glyphicon-pencil"></span>
							        		</button>
							        		<s class="input-group-text">N</s>
							      		</div>
										<input type="number" name="unit_price" id="unit_price" class="form-control" readonly required min="0" placeholder="Enter unit price...">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="item_id" id="item_id">
					<input type="hidden" name="btn_action" id="btn_action" value="edit_wh_stock">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Save">
					<button type="button" data-dismiss="modal" class="btn btn-outline-danger">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
</div>
</div>
<?php
include 'footer.php';
?>
<script>
	
	$(document).ready(function(){
		var table = $('#wh_stock_tbl');
		sticky_header(table);
		var fetch_tbl = 'wh_stock_tbl';
		var wh_stock_tbl = $("#wh_stock_tbl").DataTable({
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
					"targets":[8],
					"orderable": false
				}],
			"pageLength":25			
		});

		//activate tooltip
		$('[data-toggle="tooltip"]').tooltip();

		$(document).on('keyup','#item_name',function(){
			var display_area = $(this).parent().next('.list-group');
			var	search_val = $.trim($(this).val());
			display_goods_item_list(search_val,display_area);
		});

		$(document).on('blur','#item_name',function(){
			$(this).parent().next('.list-group').css("display","none");
		});

		$(".modal").on("hidden.bs.modal", function(){
			$('#itm_form')[0].reset();
			$('.mssg').text('');
			$('#action').attr('disabled', false);

        });

		$(document).on('click', '.view_edit',function() {
			var item_id = $(this).attr("id");
			var btn_action = 'fetch_goods';
			$.ajax({
				url: 'wh_goods_action.php',
				method: 'post',
				data: {item_id:item_id, btn_action:btn_action},
				dataType:'json',
				success:function(data){
					$('#wh_goods_modal').modal('show');	
					$('#item_name').attr('readonly',true);
					$('#item_id').val(data.item_id);
					$('#item_name').val(data.item_name);
					$('#ctn_price').val(data.ctn_price);
					$('#unit_price').val(data.unit_price);
					$('#wh_strt_stock').text(data.wh_strt_stock+' ctns');
					$('#wh_strt_datetime').text(data.wh_strt_datetime);
					$('#wh_ins_since_strt').text(data.wh_ins_since_strt+' ctns');
					$('#wh_out_since_strt').text(data.wh_out_since_strt+' ctns');
					$('#wh_curr_stock').val(data.wh_curr_stock);
					$('#hidden_wh_curr_stock').val(data.wh_curr_stock);

				}
			});
		});
		
		//click on input field reset btn
		$(document).on('click','.reset',function(event){
			event.preventDefault();
			var reset = $(this).parent().siblings();
			$(reset).attr('readonly', false);
			$(reset).focus();
			$(reset).blur(function () {
				$(this).attr('readonly',true);
			});
		});


		$(document).on('submit','#itm_form',function (event) {
			event.preventDefault();
			$('#action').attr('disabled','disabled');
			var form_data = $(this).serialize();
				$.ajax({
				url: 'wh_goods_action.php',
				method: 'post',
				data: form_data,
				success:function(data) {
					if (data == "*Invalid Input!") {
						$('.mssg').text(data);
						$('#action').attr('disabled', false);		
					}
					else {	
						$('#itm_form')[0].reset();			
						$('#wh_goods_modal').modal('hide');
						$('#alert-modal .modal-title').text('Alert!');
						$('#alert-modal').modal('show');
						$('#alert-modal').find('.modal-body').html(data);
						wh_stock_tbl.ajax.reload();
					}
				}
			});	
		});
	});
</script>
</body>
</html>