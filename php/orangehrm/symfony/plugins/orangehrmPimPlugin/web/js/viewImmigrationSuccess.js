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
            'immigration[passport_issue_date]': {required: true, dateISO: true, validdate: true, validIssuedDate: true},
            'immigration[passport_expire_date]' : {required: true, dateISO: true, validdate:true},
            'immigration[country]' : {required: true},
            'immigration[i9_review_date]' : {required: true, dateISO: true, validdate: true}
        },
        messages: {
            'immigration[number]': {required: lang_numberRequired},
            'immigration[passport_issue_date]': {required: lang_issueDateRequird, dateISO: lang_dateFormatIssue, validdate: lang_invalidIssueDate, validIssuedDate: lang_issuedGreaterExpiry},
            'immigration[passport_expire_date]' : {required: lang_expireDateRequired, dateISO: lang_dateFormatIssue, validdate: lang_invalidExpireDate},
            'immigration[country]' : {required: lang_countryRequired},
            'immigration[i9_review_date]' : {required: lang_reviewDateRequired, dateISO: lang_dateFormatIssue, validdate: lang_invalidExpireDate}
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
        $(".paddingLeftRequired").hide();
        $("#immigrationDataPane").hide();
    } else {
        $("#btnCancel").hide();
        $("#immigrationHeading").text(lang_addImmigrationHeading);
        $(".paddingLeftRequired").show();
        $("#immigrationDataPane").show();
        $("#immidrationList").hide();
    }

    //on clicking of add button
    $("#btnAdd").click(function(){
        $("#immigrationHeading").text(lang_addImmigrationHeading);
        $(".paddingLeftRequired").show();
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

        $(".paddingLeftRequired").hide();
        $("#immigrationDataPane").hide();
        $("#btnAdd").show();
    });

    //on clicking of delete button
    $("#btnDelete").click(function() {
        $("#frmImmigrationDelete").submit();
    });

    $.validator.addMethod("validdate", function(value, element) {
        if(value == "") {
            return true;
        }
        var dt = value.split("-");
        return validateDate(parseInt(dt[2], 10), parseInt(dt[1], 10), parseInt(dt[0], 10));
    });

    /* Valid From Date */
    $.validator.addMethod("validIssuedDate", function(value, element) {

        var fromdate	=	$('#immigration_passport_issue_date').val();
        fromdate = (fromdate).split("-");
        if(!validateDate(parseInt(fromdate[2],10), parseInt(fromdate[1],10), parseInt(fromdate[0],10))) {
           return false;
        }
        var fromdateObj = new Date(parseInt(fromdate[0],10), parseInt(fromdate[1],10) - 1, parseInt(fromdate[2],10));
        var todate		=	$('#immigration_passport_expire_date').val();
        todate = (todate).split("-");
        var todateObj	=	new Date(parseInt(todate[0],10), parseInt(todate[1],10) - 1, parseInt(todate[2],10));

        if(fromdateObj > todateObj){
            return false;
        }
        else{
            return true;
        }

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

    $(".paddingLeftRequired").show();
    $("#immigrationHeading").text(lang_editImmigrationHeading);
    $("#btnAdd").hide();
    $("#immigrationDataPane").show();
}

function validateDate(day, month, year) {
    var days31 = new Array(1,3,5,7,8,10,12);

    if(month > 12 || month < 1) {
        return false;
    }

    if(day == 29 && month == 2) {
        if(year % 4 == 0) {
            return true;
        }
    }

    if(month == 2 && day < 29) {
        return true;
    }
    if(day < 32 && month != 2) {
        if(day == 31) {
            flag = false;
            for(i=0; i < days31.length; i++) {
                if(days31[i] == month) {
                    flag = true;
                    break;
                }
            }
            return flag;
        }
        return true;
    }
    return false;
}