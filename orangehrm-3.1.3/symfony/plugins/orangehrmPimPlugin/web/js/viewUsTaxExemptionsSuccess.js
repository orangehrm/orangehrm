$(document).ready(function() {

    $("#frmEmpTaxExemptions").validate({
        rules: {
            'tax[federalExemptions]' : {
                number: true, min: 0, max: 99
            },
            'tax[stateExemptions]' : {
                number: true, min: 0, max: 99
            }
        },
        messages: {
            'tax[federalExemptions]' : {
                number: lang_negativeAmount, min: lang_negativeAmount, max: lang_tooLargeAmount
            },
            'tax[stateExemptions]' :{
                number: lang_negativeAmount, min: lang_negativeAmount, max: lang_tooLargeAmount
            }
        }
    });  // End of validator

    //on form loading
    $(".txtBox").attr("disabled", "disabled");
    $(".drpDown").attr("disabled", "disabled");

    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == lang_edit) {
            $(".messageBalloon_success").remove();
            $(".txtBox").removeAttr("disabled");
            $(".drpDown").removeAttr("disabled");
            $("#btnSave").attr('value', lang_save);
            return;
        }

        if($("#btnSave").attr('value') == lang_save) {
            $("#frmEmpTaxExemptions").submit();
        }

    });

});

