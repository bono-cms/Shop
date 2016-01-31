$(function(){
	$("[name='filter[date]']").datepicker({
		format : 'yyyy-mm-dd'
	});
	
	$.delete({
		categories : {
			category : {
				url : "/admin/module/shop/category/do/delete"
			},
			product : {
				url : "/admin/module/shop/product/delete"
			}
		}
	});
	
	$("[data-button='statistic']").click(function(event){
		event.preventDefault();
		$.ajax({
			url : '/admin/module/shop/statistic',
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