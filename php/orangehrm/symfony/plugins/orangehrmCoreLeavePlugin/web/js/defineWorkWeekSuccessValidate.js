$(document).ready(function() {
    
    $("#frmWorkWeek").validate({
        submitHandler: function(form) {
            
            var noWorkingDays = $(form).find('option:selected[value="8"]').length;
            
            if (noWorkingDays == 7) {
                $('#messageBalloonContainer').empty();
                $('#messageBalloonContainer').append("<div class=\"messageBalloon_warning\">" + lang_AtLeastOneWorkDay + "</div>");
                $('.messageBalloon_warning').css('padding-left', '10px');                
            } else {
                form.submit();
            }
        }                
    });    
                   

});