
function display_goods_item_list(search_val,display_area) {
	/*...............autocomplete search goods item................*/
	var key_action = 'search_item';
	if (search_val.length > 1) {

		$.ajax({
			url: 'goods_item_action.php',
			method: 'post',
			data: {key_action:key_action, search_val:search_val},
			success:function (data) {
				$(display_area).css("display","block");
				$(display_area).html(data);
			}
		});
	}
	else {
		$(display_area).css("display","none");
	}
}

function sticky_header(table) {
	$(window).scroll(function () {
		if ($(window).scrollTop() > table.offset().top) {
			table.floatThead({
				top: 54,
				zIndex : 3,
	  			responsiveContainer: function(table){
	        		return table.closest('.table-responsive');
	    		}
			});
		}
	});	
}
function resize_input_fields(){

		var window_width = $(window).width();
		if (window_width < 750) {
			$('.row_no div:nth-child(1)').width(400);
			$('.row_no .input-group').each(function(){
      			$(this).width(150);
      			
    		});
    		
		}		
}

$(document).on('click', '#toggle-password', function () {
	$(this).toggleClass('fa-eye-slash');
	if ($(this).prev('input').attr('type') == 'password') {
		$(this).prev('input').attr('type', 'text');
	}
	else {
		$(this).prev('input').attr('type', 'password');
	}
});




		

