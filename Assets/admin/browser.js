
$(function() {
	$("[name='filter[date]']").datepicker({
		format : 'yyyy-mm-dd'
	});
	
	$.delete({
		categories : {
			category : {
				url : "/admin/module/shop/category/do/delete.ajax"
			},
			product : {
				url : "/admin/module/shop/product/delete.ajax"
			}
		}
	});
	
	$("[data-button='statistic']").click(function(event){
		event.preventDefault();
		$.ajax({
			url : '/admin/module/shop/statistic.ajax',
			beforeSend : function(){
				// Empty function cancels loading div
			},
			success : function(response){
				$("#statistic-body").html(response);
				$("#statistic-modal").modal();
			}
		});
	});
});