$(document).ready(function() {
    
    $.validator.addMethod("atleastOneWorkDay", function(value) {
        
        var valid = true;            
        var noWorkingDays = $("#frmWorkWeek").find('option:selected[value="8"]').length;

        if (noWorkingDays == 7) {
            valid = false;          
        }
        
        return valid;
    }, lang_AtLeastOneWorkDay);            
    
    $("#frmWorkWeek").validate({
        onkeyup: true,
        groups: {
            workWeek: "WorkWeek[day_length_Monday] WorkWeek[day_length_Tuesday] WorkWeek[day_length_Wednesday] WorkWeek[day_length_Thursday] WorkWeek[day_length_Friday] WorkWeek[day_length_Saturday] WorkWeek[day_length_Sunday]"
        },
        rules: {
            'WorkWeek[day_length_Monday]': {atleastOneWorkDay: true},
            'WorkWeek[day_length_Tuesday]': {atleastOneWorkDay: true},
            'WorkWeek[day_length_Wednesday]': {atleastOneWorkDay: true},
            'WorkWeek[day_length_Thursday]': {atleastOneWorkDay: true},
            'WorkWeek[day_length_Friday]': {atleastOneWorkDay: true},
            'WorkWeek[day_length_Saturday]': {atleastOneWorkDay: true},
            'WorkWeek[day_length_Sunday]': {atleastOneWorkDay: true}
        },
        highlight: function (element) {
            // do not highlight
        },
        showErrors: function(errorMap, errorList) {
            var errors = this.numberOfInvalids();
            $("div#form_error_div").remove();            
            if (errors) {            
                $("div.inner").prepend('<div class="message warning" id="form_error_div">' + lang_AtLeastOneWorkDay + '</div>');
            }
          }        
               
    });    
                   

});