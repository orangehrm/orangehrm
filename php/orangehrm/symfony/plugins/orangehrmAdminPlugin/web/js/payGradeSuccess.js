$(document).ready(function() {
   
    $('#btnSave').click(function() {    
        $('#frmPayGrade').submit();
        
    });
    
    if(payGradeId > 0){
        $('#payGrade_name').attr('disabled','disabled');
        $('btnSave').val(lang_edit);
    }
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
});

