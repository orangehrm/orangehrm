$(document).ready(function() {        

    //clearing error messages after reset
    $("#btnReset").click(function() {
        $('ul.error_list').remove();            
        $(".errorContainer").html("");
        $('label.error').remove();
        $('#frmHoliday').find('input.error, select.error').removeClass('error');
    });
               
    // Back button
    $('#btnBack').click(function(){
        window.location.href = backUrl;
    });

    $("#saveBtn").click(function(){
        $("#frmHoliday").submit();
    });

}); 

