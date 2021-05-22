<?php
include 'my_db.class.php';
if (!isset($_SESSION['type'])) {
	header("location:logout.php");
}
include 'header.php';
?>

<div class="create_new_item container">
	<div class="row">
		<div class="col-lg-12">
			<div class="col-lg-8 mx-auto">
				<div class="card" >
					<div class="row">
						<div class="col-lg-12">
							<div class="card-header">
								<h4 class="card-title" align="center">CREATE NEW ITEM</h4>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="card-body">
								<form method="post" id="itm_form">
									<div class="col-lg-12 font-weight-bold">
										<div class="row">
											<div class="form-group col-lg-12">
											<p class="text-danger small font-weight-normal" style="padding:0; margin:0;">*Important* <br>Ensure that item does not already exist in record</p>
												<label for="item_name">Goods Item Name:</label>
													<span class="mssg text-danger font-weight-bold"></span>
													<input type="text" name="item_name" id="item_name" class="form-control" required placeholder="Enter Goods Item Name...">
													<div class="list-group font-weight-normal">
											<!-- display autocomplete search items here -->
													</div>
											</div>
										</div>
										<div align="center" class="font-weight-light" style="text-decoration:underline;">Supply Detail</div>
										<div class="form-row">
											<div class="form-group col-lg-5">
												<label for="supplier">Supplier:</label>
													<input type="text" name="supplier" id="supplier" class="form-control supplier" placeholder="Enter Supplier...">
											</div>
											<div class="form-group col-lg-3">
												<label for="supply_qty">Qty Purchased:</label>
												<input type="number" name="supply_qty" id="supply_qty" class="form-control supply_qty" min="0" placeholder="Enter purchase qty...">
											</div>
											<div class="form-group col-lg-4">
												<label for="supply_price">Purchase price:</label>
												<div class="input-group mb-3">
													<div class="input-group-prepend">
														<span class="input-group-text"><s>N</s></span>
													</div>
													<input type="number" name="supply_price" id="supply_price" class="form-control supply_price" min="0" placeholder="Enter purchase price...">
												</div>
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-lg-4">
												<label for="unit_per_ctn">Unit per ctn :</label>
												<div class="input-group mb-3">
													<input type="number" name="unit_per_ctn" class="form-control unit_per_ctn" id="unit_per_ctn">
													<div class="input-group-append">
														<span class="input-group-text">pcs/ctn</span>
													</div>
												</div>
												
											</div>
										</div>
										<div align="center" class="font-weight-light" style="text-decoration:underline;">Selling price (<s>N</s>)</div>
										<div class="form-row">
											<div class="form-group col-lg-6">
												<label for="ctn_price">ctn</label>
												<div class="input-group mb-3">
													<div class="input-group-prepend">
							        					<s class="input-group-text">N</s>
							      					</div>
													<input type="number" name="ctn_price" id="ctn_price" class="form-control" min="0" placeholder="Enter carton price..." required>
												</div>
											</div>
											<div class="form-group col-lg-6">
												<label for="unit_price">unit</label>
												<div class="input-group mb-3">
													<div class="input-group-prepend">
							        					<s class="input-group-text">N</s>
							      					</div>
													<input type="number" name="unit_price" id="unit_price" class="form-control" min="0" placeholder="Enter unit price..." required>
												</div>
											</div>
										</div>
									</div>
										<div align="right">
											<input type="hidden" name="action" id="action" value="Create">
											<input type="hidden" name="btn_action" id="btn_action" value="create_new">
											<input type="submit" name="save" id="save" class="btn btn-success" value="Save">
										</div>
									</div>
								</form>
							</div>
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
<script>
	$(document).ready(function(){

		$(document).on('keyup','#item_name',function(){
			var display_area = $(this).next('.list-group');
			var	search_val = $.trim($(this).val());
			display_goods_item_list(search_val,display_area);
		});

		$(document).on('blur','#item_name',function(){
			$(this).next('.list-group').css("display","none");
		});
		//submit form datas
		$(document).on('submit','#itm_form',function (event) {
			event.preventDefault();
			//encode form datas
			var form_data = $(this).serialize();
			$.ajax({
				url:'create_new_item_action.php',
				method: 'post',
				data: form_data,
				success:function(data) {	
					if (data == "*Invalid Input!") {
						$('.mssg').text(data);
						$('#item_name').focus();
						$('#item_name').addClass('shadow-danger');				
					}
					else {	
						$('#itm_form')[0].reset();
						$('#alert-modal').modal('show');
						$('#alert-modal').find('.modal-body').html(data);
					}
				}
			});
			
		});
		
		$(document).on('keyup', '#supply_qty', function(){
			//set unit_per_ctn input
			if($.trim($(this).val()) != ''){
				$('.unit_per_ctn').attr('required',true);
			}
			else{
				$('.unit_per_ctn').attr('required',false);
			}
		});
	});
</script>