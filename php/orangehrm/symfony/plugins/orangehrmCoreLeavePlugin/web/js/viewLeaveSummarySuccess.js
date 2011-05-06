$(document).ready(function(){

    /* making all text boxes non editable by default */
    $('.formInputText').attr("disabled", "disabled");

    /* Clearing auto-fill fields */
    $("#leaveSummary_txtEmpName").click(function(){
        $(this).attr('value', '');
        $("#leaveSummary_cmbEmpId").attr('value', 0);
    });

    /* Auto completion of employees */
    $("#leaveSummary_txtEmpName").autocomplete(empdata, {
        formatItem: function(item) {
            return item.name;
        }, matchContains:true
    }).result(function(event, item) {
        $('#leaveSummary_cmbEmpId').val(item.id);
    });

    /* *Search button */
    $('#btnSearch').click(function() {
        recheckEmpId();
        adjustEmpId();
        $('#hdnAction').val('search');
        $('#frmLeaveSummarySearch').submit();
    });

    function adjustEmpId() {

        empName = $.trim($('#leaveSummary_txtEmpName').val()).toLowerCase();

        if (empName != 'all' && $('#leaveSummary_cmbEmpId').val() == 0) {
            $('#leaveSummary_cmbEmpId').val('-1');
        }

    }

    function recheckEmpId() {

        var empDataArray = eval(empdata); // TODO: Try to replace eval()
        var empDateCount = empDataArray.length;

        var i;
        for (i=0; i<empDateCount; i++) {

            fieldName = $.trim($('#leaveSummary_txtEmpName').val()).toLowerCase();
            arrayName = empDataArray[i].name.toLowerCase();
            $('#leaveSummary_cmbEmpId').val(0);
            if (fieldName == arrayName) {
                $('#leaveSummary_cmbEmpId').val(empDataArray[i].id);
                break;
            }
        }
    }

    /* Save button */
    $('#btnSave').click(function() {
        if($('#btnSave').attr('value') == editButtonCaption) {
            $('.formInputText').removeAttr("disabled");
            $('#btnSave').attr('value', saveButtonCaption);
            return;
        }

        if($('#btnSave').attr('value') == saveButtonCaption) {
            //$('#btnSave').attr('value', "Edit");
            var flag = validateInput();
            //after the validation
            if(flag) {
                $('#hdnAction').val('save');
                $('#frmLeaveSummarySearch').submit();
            } else {
                $('#validationMsg').attr('class', "messageBalloon_failure");
            }
            return;
        }
    });

    function validateInput() {
        var flag = true;

        $(".messageBalloon_success").remove();
        $(".messageBalloon_warning").remove();
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");

        var errorStyle = "background-color:#FFDFDF;";
        $('.formInputText').each(function(){
            element = $(this);
            $(element).removeAttr('style');

            if(!(/^[0-9]+\.?[0-9]?[0-9]?$/).test($(element).val())) {
               $('#validationMsg').html(lang_not_numeric);
               $(element).attr('style', errorStyle);
               flag = false;
            } else {
               if(parseFloat($(element).val()) > 365) {
                   $('#validationMsg').html(lang_not_numeric);
                   $(element).attr('style', errorStyle);
                   flag = false;
               }
            }
        });

        return flag;
    }

    $('.formInputText').change(function() {
        var flag = validateInput();
        if(!flag) {
            $('#validationMsg').attr('class', "messageBalloon_failure");
        }
    });

    $("#summaryReset").click(function(){
        $(".messageBalloon_success").remove();
        $(".messageBalloon_warning").remove();
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");

        $('.formInputText').removeAttr('style');
    });
});