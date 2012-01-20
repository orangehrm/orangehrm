$(document).ready(function() {
   
    $('#addCustomer_customerId').val(customerId);
    $('#btnSave').click(function() {
        if($('#btnSave').val() == lang_edit){
            $('.formInput').removeAttr('disabled');
            $('#btnSave').val(lang_save);
        } else if ($('#btnSave').val() == lang_save){
            if(isValidForm()){  
                $('#frmAddCustomer').submit();
            }      
        }
    });
       
    if(customerId > 0) {
        $('#addCustomerHeading').text(lang_editCustomer);
        $('.formInput').attr('disabled', 'disabled');
        $('#btnSave').val(lang_edit);
    }
       
    $('#btnCancel').click(function() {
        window.location.replace(cancelBtnUrl+'?customerId='+customerId);
    });
       
    $('#btnAdd').click(function() {
        window.location.replace(addCustomerUrl);
    });
});

function isValidForm(){
    
    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentVacancy;
        var id = parseInt(customerId,10);
        var vcCount = customerList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == customerList[j].id){
                currentVacancy = j;
            }
        }
        var i;
        vcName = $.trim($('#addCustomer_customerName').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = customerList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentVacancy != null){
            if(vcName == customerList[currentVacancy].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });

    
    var validator = $("#frmAddCustomer").validate({

        rules: {
            'addCustomer[customerName]' : {
                required:true,
                maxlength: 50,
                uniqueName: true
            },
            'addCustomer[description]' : {
                maxlength: 255
            }

        },
        messages: {
            'addCustomer[customerName]' : {
                required: lang_customerNameRequired,
                maxlength: lang_exceed50Charactors,
                uniqueName: lang_uniqueName
                
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
