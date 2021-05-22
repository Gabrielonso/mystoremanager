<?php
?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/floatthead/2.2.1/jquery.floatThead.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
	<script type="text/javascript" src="script.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var href = "<?php echo basename($_SERVER['PHP_SELF']);?>";
			$('#sidenav a[href="'+href+'"]').css({'background-color': '#e0f2f1','color': 'black'});


			$(document).on('click','#navbardrop', function(e){
				 e.preventDefault();
				$('.dropdown-menu').toggleClass('active');
			});
			$(document).on('click','.nav-item',function(){
				$(this).toggleClass('active');
				if ($(this).hasClass('active') == true) {
					$(this).find('.fa.fa-caret-down').attr('class','fa fa-caret-up');
				}
				else{
					$(this).find('.fa.fa-caret-up').attr('class','fa fa-caret-down');
					$(this).find('.collapse').collapse('hide');
					$(this).removeClass('active');
				}
			});
			$(document).on('click','.sidenav-trigger',function(){
				$('.container-fluid').toggleClass('active');
			});
		});
	</script>