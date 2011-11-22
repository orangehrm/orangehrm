$(document).ready(function() {
   
    $('#btnSave').click(function() {  
        
        if($('#btnSave').val() == lang_edit){
            $('#payGrade_name').removeAttr('disabled');
            $('#btnSave').val(lang_save);
            
        } else if ($('#btnSave').val() == lang_save){
            $('#frmPayGrade').submit();
        }        
    });
    
    $('#btnCancel').click(function() {
        window.location.replace(viewPayGradesUrl);
    });
    
    if(payGradeId > 0){
        $('#payGrade_name').attr('disabled','disabled');
        $('#btnSave').val(lang_edit);
        $('#payGrade_payGradeId').val(payGradeId);
        $('#payGradeHeading').text(lang_editPayGrade);
    }
    
    
    ///// JQuery for currency list
    
    $('#btnAddCurrency').click(function() {  
        $('#addPaneCurrency').show();
        $('#actionButtons').show();
        $('#addDeleteBtnDiv').hide();
    });
    
    $('#cancelButton').click(function(){
        $('#addPaneCurrency').hide();
        $('#actionButtons').hide();
        $('#addDeleteBtnDiv').show();
        validatorCurr.resetForm();
    });
    
    $('#btnSaveCurrency').click(function(){
        $('#frmCurrency').submit();
    });
    
    var validator = $("#frmPayGrade").validate({

        rules: {
            'payGrade[name]' : {
                required:true,
                maxlength: 50
            }
        },
        messages: {
            'payGrade[name]' : {
                required: lang_NameRequired,
                maxlength: lang_exceed50Charactors    
            }
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));

        }

    });
    
    var validatorCurr = $("#frmCurrency").validate({

        rules: {
            'payGradeCurrency[currencyName]' : {
                required:true,
                maxlength: 50
            },
            'payGradeCurrency[minSalary]' : {
                number: true,
                maxlength: 50
            },
            'payGradeCurrency[maxSalary]' : {
                number:true,
                maxlength: 50
            }
        },
        messages: {
            'payGradeCurrency[currencyName]' : {
                required: lang_currencyRequired,
                maxlength: lang_exceed50Charactors
            },
            'payGradeCurrency[minSalary]' : {
                number: lang_salaryShouldBeNumeric,
                maxlength: lang_exceed50Charactors
            },
            'payGradeCurrency[maxSalary]' : {
                number: lang_salaryShouldBeNumeric,
                maxlength: lang_exceed50Charactors
            }
        },
        errorPlacement: function(error, element) {
            error.appendTo(element.next('div.errorHolder'));

        }

    });
});

