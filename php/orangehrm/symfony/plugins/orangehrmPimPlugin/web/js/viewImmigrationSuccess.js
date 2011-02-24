$(document).ready(function() {

    daymarker.bindElement("#immigration_passport_issue_date", function() {});
    $('#passportIssueDateBtn').click(function() {
        daymarker.show("#immigration_passport_issue_date");
    });

    daymarker.bindElement("#immigration_passport_expire_date", function() {});
    $('#passportExpireDateBtn').click(function() {
        daymarker.show("#immigration_passport_expire_date");
    });

    daymarker.bindElement("#immigration_i9_review_date", function() {});
    $('#i9ReviewDateBtn').click(function() {
        daymarker.show("#immigration_i9_review_date");
    });

    $("#btnSave").click(function() {
        $("#frmEmpImmigration").submit();
    })

    //form validation
    $("#frmEmpImmigration").validate({
        rules: {
            'immigration[number]': {required: true},
            'immigration[passport_issue_date]': {required: true},
            'immigration[passport_expire_date]' : {required: true},
            'immigration[country]' : {required: true},
            'immigration[i9_review_date]' : { required: true}
        },
        messages: {
            'immigration[number]': {required: lang_numberRequired},
            'immigration[passport_issue_date]': {required: lang_issueDateRequired},
            'immigration[passport_expire_date]' : {required: lang_expireDateRequired},
            'immigration[country]' : {required: lang_countryRequired},
            'immigration[i9_review_date]' : { required: lang_reviewDateRequired}
        },

        errorElement : 'label',
        errorPlacement: function(error, element) {

            error.insertBefore(element.next(".clear"));

            //these are specially for date boxes
            error.insertBefore(element.next().next(".clear"));
        }
    });

    //enable, dissable views on loading
    //this is to findout whether passport details already entered
    if($(".check").length > 0) {
        $("#immigrationDataPane").hide();
    } else {
        $("#btnCancel").hide();
        $("#immigrationDataPane").show();
        $("#immidrationList").hide();
    }

    //on clicking of add button
    $("#btnAdd").click(function(){
        $("#immigrationDataPane").show();
        $("#btnAdd").hide();
    });

    //on clicking cancel button
    $("#btnCancel").click(function() {
        //clearing all entered values
        var controls = new Array("number", "passport_issue_date", "seqno", "passport_expire_date", "i9_status", "country", "i9_review_date", "comments");
        $("#immigration_type_flag_1").attr("checked", "checked");
        for(i=0; i < controls.length; i++) {
            $("#immigration_" + controls[i]).val("");
        }

        $("#immigrationDataPane").hide();
        $("#btnAdd").show();
    });

    //on clicking of delete button
    $("#btnDelete").click(function() {
        $("#frmImmigrationDelete").submit();
    });



});

//function to load data for updating
function fillDataToImmigrationDataPane(seqno) {

    var controls = new Array("number", "passport_issue_date", "passport_expire_date", "i9_status", "country", "i9_review_date", "comments");
    for(i=0; i < controls.length; i++) {
        //this is to say something like $('#immigration_number').val($("#number_" + seqno).val());
        $("#immigration_" + controls[i]).val($("#" + controls[i] + "_" + seqno).val());
    }
    $("#immigration_seqno").val(seqno);

    var typeFlag = $("#type_flag_" + seqno).val();
    $("#immigration_type_flag_" + typeFlag).attr("checked", "checked");

    $("#btnAdd").hide();
    $("#immigrationDataPane").show();
}