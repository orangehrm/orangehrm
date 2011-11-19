$(document).ready(function() {
   
    //$('#addCustomer_customerId').val(customerId);
    $('#btnSave').click(function() {
          
        if(isValidForm()){          
            $('#frmEmpStatus').submit();
        }
    });
              
});

function isValidForm(){

    var validator = $("#frmEmpStatus").validate({

        rules: {
            'empStatus[name]' : {
                required:true,
                maxlength: 50
            }
        },
        messages: {
            'empStatus[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors       
            }

        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));

        }

    });
    return true;
}