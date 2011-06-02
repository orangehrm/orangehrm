$(document).ready(function() {

    hideShowReportingMethodOther()

    if ($("#reportto_name").val() == '') {
        $("#reportto_name").val(typeForHints)
        .addClass("inputFormatHint");
    }

    $('#reportto_reportingModeType').change(function() {
        hideShowReportingMethodOther();
    });

    $("#reportto_name").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
     

    //Auto complete
    $("#reportto_name").autocomplete(employees, {
        formatItem: function(item) {
            return item.name;
        },
        matchContains:true
    }).result(function(event, item) {
        $("#reportto_selectedEmployee").val(item.id);
    });

    $('#btnSaveReportTo').click(function() { 
        $('#frmAddReportTo').submit();
    });



});

function hideShowReportingMethodOther() {
    if ($('#reportto_reportingModeType').val() != -1 ) {
        $('#pleaseSpecify').hide();
    } else {
        $('#pleaseSpecify').show();
    }
}
