$(document).ready(function() {

    //on form loading
    $(".txtBox").attr("disabled", "disabled");
    $(".drpDown").attr("disabled", "disabled");

    $("#btnSave").click(function() {
        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == edit) {
            $(".txtBox").removeAttr("disabled");
            $(".drpDown").removeAttr("disabled");
            $("#btnSave").attr('value', save);
            return;
        }

        if($("#btnSave").attr('value') == save) {
            $("#frmEmpTaxExemptions").submit();
        }
    });

});

