$(document).ready(function() {
   
    $('#addCustomer_customerId').val(customerId);
       $('#btnSave').click(function() {
          
          if(isValidForm()){          
                $('#frmAddCustomer').submit();
            }
       });
       
       $('#btnCancel').click(function() {
           window.location.replace(cancelBtnUrl);
       });
       
       $('#btnAdd').click(function() {
           window.location.replace(addCustomerUrl);
       });
});

function isValidForm(){
    
    var validator = $("#frmAddCustomer").validate({

        rules: {
            'addCustomer[customerName]' : {
                required:true,
                maxlength: 50
            },
            'addCustomer[description]' : {
                maxlength: 255
            }

        },
        messages: {
            'addCustomer[customerName]' : {
                required: lang_customerNameRequired,
                maxlength: lang_exceed50Charactors
            },
            'addCustomer[description]' : {
                 maxlength: lang_exceed255Charactors
            }

        },

        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));

        }

    });
    return true;
}
