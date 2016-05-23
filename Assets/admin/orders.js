$(function(){
    $("[name='filter[date]']").datepicker({
        format : 'yyyy-mm-dd'
    });

    $("[data-button='approve']").click(function(event){
        event.preventDefault();

        $.ajax({
            url : $(this).data('url'),
            success : function(response){
                if (response == "1"){
                    window.location.reload();
                } else {
                    $.showErrors(response);
                }
            }
        });
    });

    $("[data-button='details']").click(function(event){
        event.preventDefault();
        $.ajax({
            url : $(this).data('url'),
            success : function(response){
                $("#details-body").html(response);
                $("#details-modal").modal();
            }
        });
    });
});