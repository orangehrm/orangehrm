$(document).ready(function() {

    //Load default Mask if empty

//    var passportIssueDate = $("#immigration_passport_issue_date");
//
//    if(trim(passportIssueDate.val()) == ''){
//        passportIssueDate.val(dateDisplayFormat);
//    }
//
//    var passportExpireDate = $("#immigration_passport_expire_date");
//
//    if(trim(passportExpireDate.val()) == ''){
//        passportExpireDate.val(dateDisplayFormat);
//    }
//
//    var i9ReviewDate = $("#immigration_i9_review_date");
//
//    if(trim(i9ReviewDate.val()) == ''){
//        i9ReviewDate.val(dateDisplayFormat);
//    }

    $("#btnSave").click(function() {
        $("#frmEmpImmigration").submit();
    })

    //form validation
    $("#frmEmpImmigration").validate({
        rules: {
            'immigration[number]': {required: true},
            'immigration[passport_issue_date]': {required: true, valid_date: function(){ return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false} } },
            'immigration[passport_expire_date]' : {required: true, valid_date: function(){ return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false} } },
            'immigration[country]' : {required: true},
            'immigration[i9_review_date]' : {required: true, valid_date: function(){ return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false} } },
            'immigration[comments]': {maxlength: 250}
        },
        messages: {
            'immigration[number]': {required: lang_numberRequired},
            'immigration[passport_issue_date]': {required: lang_issueDateRequird, valid_date: lang_invalidDate},
            'immigration[passport_expire_date]' : {required: lang_expireDateRequired, valid_date: lang_invalidDate},
            'immigration[country]' : {required: lang_countryRequired},
            'immigration[i9_review_date]' : {required: lang_reviewDateRequired, valid_date: lang_invalidDate},
            'immigration[comments]': {maxlength: lang_commentLength }
        },

        errorElement : 'label',
        errorPlacement: function(error, element) {

            error.insertBefore(element.next(".clear"));

            //these are specially for date boxes
            error.insertBefore(element.next().next(".clear"));
        }
    });

    daymarker.bindElement("#immigration_passport_issue_date",
        {onSelect: function(date){
            $("#immigration_passport_issue_date").valid();
            },
        dateFormat:jsDateFormat
        });

    $('#passportIssueDateBtn').click(function() {
        daymarker.show("#immigration_passport_issue_date");
    });

    daymarker.bindElement("#immigration_passport_expire_date",
        {onSelect: function(date){
            $("#immigration_passport_expire_date").valid();
            },
        dateFormat:jsDateFormat
        });

    $('#passportExpireDateBtn').click(function() {
        daymarker.show("#immigration_passport_expire_date");
    });

    daymarker.bindElement("#immigration_i9_review_date",
        {onSelect: function(date){
            $("#immigration_i9_review_date").valid();
            },
        dateFormat:jsDateFormat
        });

    $('#i9ReviewDateBtn').click(function() {
        daymarker.show("#immigration_i9_review_date");
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
        var ticks = $('input[@class=check]:checked').length;

        if(ticks > 1) {
            $("#frmImmigrationDelete").submit();
            return;
        }
        $("#messagebar").attr("class", "messageBalloon_notice");
        $("#messagebar").text(lang_deleteErrorMsg);

    });

    $.validator.addMethod("validdate", function(value, element) {
        if(value == "") {
            return true;
        }
        var dt = value.split("-");
        return validateDate(parseInt(dt[2], 10), parseInt(dt[1], 10), parseInt(dt[0], 10));
    });

    imageResize();

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