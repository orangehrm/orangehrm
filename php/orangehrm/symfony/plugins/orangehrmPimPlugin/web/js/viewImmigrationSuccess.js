$(document).ready(function() {

    var issuedDate = "";
    var passportIssueDate = $("#immigration_passport_issue_date");
    var passportExpireDate = $("#immigration_passport_expire_date");
    var i9ReviewDate = $("#immigration_i9_review_date");

    function loadDefaultDateMasks() {

        if(trim(passportIssueDate.val()) == ''){
            passportIssueDate.val(displayDateFormat);
        }

        if(trim(passportExpireDate.val()) == ''){
            passportExpireDate.val(displayDateFormat);
        }

        if(trim(i9ReviewDate.val()) == ''){
            i9ReviewDate.val(displayDateFormat);
        }
    
    }

    //Load default Mask if empty
    loadDefaultDateMasks();

    $("#btnSave").click(function() {
        issuedDate = $("#immigration_passport_issue_date").val();
        $("#frmEmpImmigration").submit();
    })

    //form validation
    $("#frmEmpImmigration").validate({
        rules: {
            'immigration[number]': {required: true},
            'immigration[passport_issue_date]': {valid_date: function(){return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}}},
            'immigration[passport_expire_date]' : {valid_date: function(){return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}}, date_range: function() {return {format:datepickerDateFormat, displayFormat:displayDateFormat, fromDate:issuedDate}}},
            'immigration[i9_review_date]' : {valid_date: function(){return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}}},
            'immigration[comments]': {maxlength: 250}
        },
        messages: {
            'immigration[number]': {required: lang_numberRequired},
            'immigration[passport_issue_date]': {valid_date: lang_invalidDate},
            'immigration[passport_expire_date]' : {valid_date: lang_invalidDate, date_range: lang_issuedGreaterExpiry},
            'immigration[i9_review_date]' : {valid_date: lang_invalidDate},
            'immigration[comments]': {maxlength: lang_commentLength}
        },

        errorElement : 'label',
        errorPlacement: function(error, element) {
            error.appendTo(element.prev('label'));
            error.insertBefore(element.next(".clear"));

        }
    });
  
    //enable, dissable views on loading
    //this is to findout whether passport details already entered
    if(havePassports) {
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
        loadDefaultDateMasks();
        $('div#immigrationDataPane label.error').hide();
        $("#immigrationHeading").text(lang_addImmigrationHeading);
        $(".paddingLeftRequired").show();
        $("#immigrationDataPane").show();
        $('form#frmImmigrationDelete table.data-table input.checkbox').hide();
        $("form#frmImmigrationDelete div.actionbar").hide();
        removeEditLinks();
        $("#messagebar").attr("class", "").text('');                
    });

    //on clicking cancel button
    $("#btnCancel").click(function() {
        $('div#immigrationDataPane label.error').hide();
        
        //clearing all entered values
        var controls = new Array("number", "passport_issue_date", "seqno", "passport_expire_date", "i9_status", "country", "i9_review_date", "comments");
        $("#immigration_type_flag_1").attr("checked", "checked");
        for(i=0; i < controls.length; i++) {
            $("#immigration_" + controls[i]).val("");
        }

        $(".paddingLeftRequired").hide();
        $("#immigrationDataPane").hide();
        $('form#frmImmigrationDelete table.data-table input.checkbox').show();
        $("form#frmImmigrationDelete div.actionbar").show();
        if(canUpdate){
            addEditLinks();
        }
        $("#messagebar").attr("class", "").text('');                
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

     $('form#frmImmigrationDelete td.document a').live('click', function() {
        $('div#immigrationDataPane label.error').hide();
        
        var code = $(this).closest("tr").find('input.checkbox:first').val();
        fillDataToImmigrationDataPane(code);
        $('form#frmImmigrationDelete table.data-table input.checkbox').hide();
        $("form#frmImmigrationDelete div.actionbar").hide();
        $("#messagebar").attr("class", "").text('');        
        
        loadDefaultDateMasks();
        
     });
     
    //if check all button clicked
    $("#immigrationCheckAll").click(function() {
        $("form#frmImmigrationDelete table tbody .checkbox").removeAttr("checked");
        if($("#immigrationCheckAll").attr("checked")) {
            $("form#frmImmigrationDelete table tbody .checkbox").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $("form#frmImmigrationDelete table tbody .checkbox").click(function() {
        $("#immigrationCheckAll").removeAttr('checked');
        if($("form#frmImmigrationDelete table tbody .checkbox").length == $("form#frmImmigrationDelete table tbody .checkbox:checked").length) {
            $("#immigrationCheckAll").attr('checked', 'checked');
        }
    });     
    
    function addEditLinks() {
        // called here to avoid double adding links - When in edit mode and cancel is pressed.
        removeEditLinks();
        $('form#frmImmigrationDelete table tbody td.document').wrapInner('<a href="#"/>');
    }

    function removeEditLinks() {
        $('form#frmImmigrationDelete table tbody td.document a').each(function(index) {
            $(this).parent().text($(this).text());
        });
    }
    

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
    $("#immigrationDataPane").show();
}
