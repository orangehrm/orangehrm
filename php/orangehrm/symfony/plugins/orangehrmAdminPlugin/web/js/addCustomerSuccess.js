$(document).ready(function() {
   
    isValidForm();
   
    $('#addCustomer_customerId').val(customerId);
    $('#btnSave').click(function() {
        if($('#btnSave').val() == lang_edit){
            $(".editable").each(function(){
                $(this).removeAttr("disabled");
            });
            $('#btnSave').val(lang_save);
        } else if ($('#btnSave').val() == lang_save){
            if(isValidForm()){  
                $('#frmAddCustomer').submit();
            }      
        }
    });
    
    $("#undeleteYes").click(function(){
        $('#frmUndeleteCustomer').submit();
        $("#undeleteDialog").toggle();
    });

    $("#undeleteNo").click(function(){
        $(this).attr('disabled', true);
        $('#addCustomer_customerName').attr('disabled', false);
        $('#frmAddCustomer').get(0).submit();
        $("#undeleteDialog").toggle();
    });
    
    $("#undeleteCancel").click(function(){
        $("#undeleteDialog").toggle();
    });
       
    if(customerId > 0) {
        $('#addCustomerHeading').text(lang_editCustomer);
        $(".editable").each(function(){
            $(this).attr("disabled", "disabled");
        });
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
        submitHandler: function(form) {            
            var deletedId = isDeletedCustomer();
            if (deletedId) {
                $('#undeleteCustomer_undeleteId').val(deletedId);          
                $('#undeleteDialogLink').click();
            } else {
                form.submit();
            }
        }

    });
    return true;
}

/**
 * Checks if current customer name value matches a deleted customer.
 * 
 * @return Customer ID if it matches a deleted customer else false.
 */
function isDeletedCustomer() {
    if ($.trim($("#addCustomer_hdnOriginalCustomerName").val()) == $.trim($("#addCustomer_customerName").val())) {
        return false;
    }

    for (var i = 0; i < deletedCustomers.length; i++) {
        if (deletedCustomers[i].name.toLowerCase() == $.trim($('#addCustomer_customerName').val()).toLowerCase()) {
            return deletedCustomers[i].id;
        }
    }
    return false;
}
