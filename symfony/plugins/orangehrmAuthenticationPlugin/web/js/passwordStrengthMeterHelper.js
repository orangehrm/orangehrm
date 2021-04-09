var currentRequest = null;
function showPasswordStrength(value, url, strengthMeterElementId, passwordOptions) {
    currentRequest = jQuery.ajax({
        type:'GET',
        url: url,
        beforeSend: function()    {
            if(currentRequest != null) {
                currentRequest.abort();
            }
        },
        data : {'password': value},
        dataType : 'json',
        success: function (data, textStatus, jqXHR) {
            var score = data.score;
            var colorClass = data.colorClass;
            var validationMsg = data.validationMsg;
            if(validationMsg){
                $('#'+strengthMeterElementId+'_help_text').text(validationMsg);
            }else{
                $('#'+strengthMeterElementId+'_help_text').text('');
            }
            showStrength(score, colorClass, strengthMeterElementId, passwordOptions, validationMsg);
        }
    });
};

function showStrength(score,colorClass, strengthMeterElementId, passwordOptions){
        $('#'+strengthMeterElementId+'_strength_meter').text(passwordOptions[score]);
        if(!$('#'+strengthMeterElementId+'_strength_meter').hasClass(colorClass)){
            $('#'+strengthMeterElementId+'_strength_meter').removeClass();
            $('#'+strengthMeterElementId+'_strength_meter').addClass(colorClass);
            $('#'+strengthMeterElementId+'_strength_meter').addClass('passwordStrengthCheck');
        }
        $('#'+strengthMeterElementId).valid();
}

