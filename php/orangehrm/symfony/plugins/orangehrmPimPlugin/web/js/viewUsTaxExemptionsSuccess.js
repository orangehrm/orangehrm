$(document).ready(function() {

function validateInput() {

        var flag = true;
        $(".messageBalloon_success").remove();
        $('#messagebar').removeAttr('class');
        $('#messagebar').html("");

        var errorStyle = "background-color:#FFDFDF;";
        $('.txtBox').each(function(){
            element = $(this);
            $(element).removeAttr('style');

            if($(element).val()){
                if(!(/^[0-9]+$/).test($(element).val())) {
                    $('#messagebar').html("Enter an Integer");
                    $(element).attr('style', errorStyle);
                    flag = false;
                }
            }
        });

        return flag;
    }

    $('.txtBox').change(function() {
        var flag = validateInput();
        if(!flag) {
            $('#messagebar').attr('class', "messageBalloon_failure");
        }
        else{
            $('#messagebar').removeAttr('class');
            $('#messagebar').html("");
        }
    });

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
            if(validateInput()){
            $("#frmEmpTaxExemptions").submit();
            }
            else{
                $('#messagebar').attr('class', "messageBalloon_failure");
            }
        }
    });

});

