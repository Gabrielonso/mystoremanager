<?php

include 'my_db.class.php';
if (!isset($_SESSION['type'])) {
	header("location:logout.php");
}
include 'header.php';
?>
<span id="alert-action"></span>
<div class="row">
	<div class="col-lg-12">
		<div class="card card-default">
			<div class="card-header">
				<div class="row">
					<div class="card-title col-lg-10 col-md-10 col-sm-8 col-xs-6" align="center">
							<h3>Today's stock record of my retail goods(in pieces)</h3>
					</div>
				</div>

				<div class="clear:both"></div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-12 table-responsive">
						<table id="rtl_itm_tbl" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th></th>
									<th></th>
									<th colspan="4" style="text-align: center;">Stock Quantity (pcs)</th>
									<th></th>
									<th></th>
								</tr>
								<tr>
									<th>Goods Item Name</th>
									<th><i class='far fa-clock'></i> Start time today</th>
									<th><i class='far fa-play-circle text-info'></i> Start Today</th>
									<th><i class='fas fa-download text-success'></i> In today after start</th>
									<th><i class='fas fa-upload text-danger'></i> Out today after start</th>
									<th><span class="glyphicon glyphicon-stats text-primary"></span> Current Stock</th>
									<th>Unit per ctn</th>
									<th>Update</th>
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
<div id="rtl_goods_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post" id="rtl_itm_form">
				<div class="modal-header">
					<h4 class="modal-title"><i class='fa fa-pencil-square-o'></i>Update Item</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body goods_item_detail font-weight-bold">
					<div class="row">
						<div class="form-group col-lg-12">
							<label for="item_name">Goods Item Name:</label><span class="mssg text-danger">*</span>
							<div class="input-group">
								<div class="input-group-prepend">
									<button class="btn btn-primary change" type="submit"><span class="glyphicon glyphicon-pencil"></span></button>
								</div>
								<input type="text" name="item_name" id="item_name" class="form-control" readonly>
							</div>
							<div class="list-group font-weight-normal">
								<!-- display autocomplete search items here -->
							</div>
						</div>
					</div>
					<div class="form-row curr_info">
						<div class="form-group col-lg-5">
							<label for="rtl_curr_stock">Current Stock in Retail:</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
		        					<button class="btn btn-primary change" type="submit">Reset</button>
		      					</div>
								<input type="number" name="rtl_curr_stock" id="rtl_curr_stock" class="form-control" readonly required>
								<input type="hidden" name="hidden_rtl_curr_stock" id="hidden_rtl_curr_stock">
							</div>
						</div>
						<div class="form-group col-lg-3">
							<label for="rtl_strt_stock_tdy">Start Qty today :</label>
								<input type="number" name="rtl_strt_stock_tdy" id="rtl_strt_stock_tdy" class="form-control" readonly>
								<input type="hidden" name="rtl_ins_tdy" id="rtl_ins_tdy">
						</div>
						<div class="form-group col-lg-4">
							<label for="rtl_tdy_strt_time">At :</label>
							<input type="hidden" name="rtl_tdy_strt_date" id="rtl_tdy_strt_date">
							<input type="text" name="rtl_tdy_strt_time" id="rtl_tdy_strt_time" class="form-control" readonly>
						</div>
					</div>
					<div>Do you want to add items to retail?</div>
					<div class="text-danger">*</div>
					<div class="form-check-inline">
						<label class="form-check-label" for="collapse_no">
		      				<input type="radio" name="add_to_rtl" class="form-check-input" required value="no" id="collapse_no">No
		    			</label>
		    		</div>
		    		<div class="form-check-inline">
						<label class="form-check-label" for="collapse_yes">
		      				<input type="radio" name="add_to_rtl" class="form-check-input" value="yes" id="collapse_yes">Yes
		    			</label>		    							
		    		</div>
					<div class="form-row collapse" id="add_rtls">
						<div class="form-group col-lg-6">
							<label for="supply_qty">How many ctns? </label><b class="mssg2 text-danger"></b>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">ctn</span>
								</div><span data-toggle="popover" data-placement="bottom" data-trigger="focus"></span>
								<input type="number" name="supply_qty" class="form-control" id="supply_qty" >
								<input type="hidden" name="wh_curr_stock" id="wh_curr_stock">
							</div>
						</div>
						<div class="form-group col-lg-6">
							<label for="unit_per_ctn">unit per ctn</label><span class="upc_err text-danger small"></span>
							<div class="input-group">
								<input type="number" name="unit_per_ctn" id="unit_per_ctn" class="form-control">
								<div class="input-group-append">
									<span class="input-group-text">pcs/ctn</span>
								</div>
							</div>
							
						</div>
					</div>
					<div class="text-center font-weight-light">Selling Price(<s>N</s>):</div>
					<div class="form-row">
						<div class="form-group col-lg-6">
							<label for="unit_price">unit price</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
		        					<button class="btn btn-primary change" type="submit"><span class="glyphicon glyphicon-pencil"></span>
		        					</button>
		        					<span class="input-group-text"><s>N</s></span>
		      					</div>
								<input type="number" name="unit_price" id="unit_price" class="form-control" readonly required>
							</div>
						</div>
						<div class="form-group col-lg-6">
							<label for="ctn_price">ctn price</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
		        					<button class="btn btn-primary change" type="submit"><span class="glyphicon glyphicon-pencil"></span>
		        					</button>
		        					<span class="input-group-text"><s>N</s></span>
		      					</div>
								<input type="number" name="ctn_price" id="ctn_price" class="form-control" readonly required>
							</div>
						</div>
					</div>
				<div class="modal-footer">
					<input type="hidden" name="item_id" id="item_id">
					<input type="hidden" name="btn_action" id="btn_action" value="update_retail">
					<input type="submit" name="action" id="action" class="btn btn-success" value="Update Record">
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
	
	$(document).ready(function(){
		var table = $('#rtl_itm_tbl');
		sticky_header(table);
		var fetch_tbl = 'rtl_stock_tbl';
		var rtl_itm_tbl = $("#rtl_itm_tbl").DataTable({
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
					"targets":[7],
					"orderable": false
				}],
			"pageLength":25			
		});

		$(document).on('keyup','#item_name',function(){
			var display_area = $(this).parent().next('.list-group');
			var	search_val = $.trim($(this).val());
			display_goods_item_list(search_val,display_area);
		});

		$(document).on('blur','#item_name',function(){
			$(this).parent().next('.list-group').css("display","none");
		});

		$(document).on('click', '.update_goods', function () {
			var item_id = $(this).attr("id");
			var btn_action = 'fetch_rtl_goods';
			$.ajax({
				url: 'rtl_goods_action.php',
				method: 'post',
				data: {item_id:item_id, btn_action:btn_action},
				dataType:'json',
				success:function(data){
					$('#rtl_goods_modal').modal('show');
					$('.curr_info').show();
					$('#item_name').val(data.item_name);				
					$('#rtl_curr_stock').val(data.rtl_curr_stock);
					$('#hidden_rtl_curr_stock').val(data.rtl_curr_stock);
					$('#rtl_strt_stock_tdy').val(data.rtl_strt_stock_tdy);
					$('#rtl_ins_tdy').val(data.rtl_ins_tdy);
					$('#rtl_tdy_strt_date').val(data.rtl_tdy_strt_date);
					$('#rtl_tdy_strt_time').val(data.rtl_tdy_strt_time);
					$('#wh_curr_stock').val(data.wh_curr_stock);
					$('#unit_per_ctn').val(data.unit_per_ctn);
					$('#unit_price').val(data.unit_price);
					$('#ctn_price').val(data.ctn_price);
					$('#item_id').val(item_id);
				}
			});
		})
		$(document).on('click','.change', function(event){
			event.preventDefault();
			var change = $(this).parent().siblings();
			$(change).attr('readonly', false);
			$(change).focus();
			$(change).blur(function () {
			$(this).attr('readonly',true);
			});

		});
		$(document).on('click','#collapse_yes', function(){
			$('#add_rtls').collapse('show');
			$('#add_rtls').find('input').attr('required',true);	
			
		});

		$(document).on('click','#collapse_no', function(){
			$('#add_rtls').find('input').val('');
			$('#add_rtls').collapse('hide');
			$('#add_rtls').find('input').attr('required',false);
			
		});
		$('[data-toggle="popover"]').popover();
		$(document).on('keyup','#supply_qty', function () {

			if (parseInt($(this).val()) > parseInt($('#wh_curr_stock').val())) {
				$(this).addClass('shadow-danger');
				$(this).prev('span').attr("data-content","Insufficient qty in stock. "+$('#wh_curr_stock').val()+" ctn avail.");
				$('[data-toggle="popover"]').popover('show');
			}
			else{
				$(this).removeClass('shadow-danger');
				$('[data-toggle="popover"]').popover('hide');
			}
		});

		$(".modal").on("hidden.bs.modal", function(){
			$('#rtl_itm_form')[0].reset();
			$('#add_rtls').collapse('hide');
			$('#add_from_store').parent().siblings('.input').remove();
			$('#action').attr('disabled', false);
			$('#supply_qty').prev('span').removeAttr("data-content");
			$('#supply_qty').removeClass('shadow-danger');
			//$('#alert-modal .modal-body').empty();

        });

        $(document).on('submit','#rtl_itm_form',function (event) {
			event.preventDefault();
			//$('#action').attr('disabled','disabled');
			var form_data = $(this).serialize();
			if($('#collapse_yes').is(':checked') && $.trim(parseFloat($('#unit_per_ctn').val())) == 0){
				$('#add_rtls').collapse('show');
				$('.upc_err').text('*Required field. Should not be less than 1');
				$('#unit_per_ctn').focus();
				return false;
			}
			else{
				$('.upc_err').text('');
			}
			$.ajax({
				url: 'rtl_goods_action.php',
				method: 'post',
				data: form_data,
				success:function(data) {
					
					if (data == "*Invalid Input!") {
						$('.mssg').text(data);
						$('#action').attr('disabled', false);
					}
					else if (data == "* Insufficient stock in store"){
						$('.mssg2').text(data);
						$('.mssg2').fadeOut(10000);
						$('#action').attr('disabled', false);
						$('#supply_qty').attr('class', 'form-control shadow-danger');
						$('#supply_qty').focus();
					}
					else{
							var html = "";
						if (data == "Goods Updated Successfully!") {
							html = '<div class="text-success font-weight-bold">'+data+'</div>';
						}
						else if(data == "Failed to update goods!"){
							html = '<div class="text-danger font-weight-bold">'+data+'</div>';
						}
						$('#rtl_itm_form')[0].reset(); //reset all form field			
						$('#rtl_goods_modal').modal('hide');
						$('#rtl_goods_modal .modal-title').html('');
						$('#alert-modal').find('.modal-title').html('Alert');
						$('#alert-modal').find('.modal-body').html(html);
						$('#alert-modal').modal('show');
						rtl_itm_tbl.ajax.reload();  //will reload userdataTable on webpage
					}

				}
			});		
		});
	});
</script>
</body>
</html>
