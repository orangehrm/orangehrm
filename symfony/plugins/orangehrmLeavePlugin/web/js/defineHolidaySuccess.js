$(document).ready(function() {        
               
    // Back button
    $('#btnCancel').click(function(){
        window.location.href = backUrl;
    });

    $("#saveBtn").click(function(){
        $("#frmHoliday").submit();
    });

}); 

