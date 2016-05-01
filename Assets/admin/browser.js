$(function(){
	$("[name='filter[date]']").datepicker({
		format : 'yyyy-mm-dd'
	});
	
	$("[data-button='statistic']").click(function(event){
		event.preventDefault();
        var url = $(this).data('url');
        
		$.ajax({
			url : url,
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