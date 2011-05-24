$(document).ready(function() {

    function validateInput() {

        var flag = true;
        $('.txtBox').each(function(){
            element = $('#'+$(this).attr('id'));
                        
            if($(element).val()){
                if(!(/^[0-9]+$/).test($(element).val().trim())) {

                    $("<label class='error'>Enter an Integer</label>").insertBefore(element.next(".clear"));
                    flag = false;
                }
                else{
                    $(element.next(".error")).remove();
                }
            }
        });

        return flag;
    }

    $('.txtBox').change(function() {
        var flag = validateInput();
    
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
               
        }
        }
    });

});

