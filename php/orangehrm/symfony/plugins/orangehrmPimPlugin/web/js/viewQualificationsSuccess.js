function clearMessageBar() {
    $("#mainMessagebar").text("").attr('class', "");
    $("#workExpMessagebar").text("").attr('class', "");
    $("#educationMessagebar").text("").attr('class', "");
    $("#skillMessagebar").text("").attr('class', "");
    $("#languageMessagebar").text("").attr('class', "");
    $("#licenseMessagebar").text("").attr('class', "");
}

$(document).ready(function() {
    //--this section is for work experience

    
    function addEditLinks() {
        // called here to avoid double adding links - When in edit mode and cancel is pressed.
        removeEditLinks();
        $('form#frmDelWorkExperience table tbody td.name').wrapInner('<a class="edit" href="#"/>');
    }

    function removeEditLinks() {
        $('form#frmDelWorkExperience table tbody td.name a').each(function(index) {
            $(this).parent().text($(this).text());
        });
    }
    
    //hide add work experience section
    $("#changeWorkExperience").hide();
    $("#workExpRequiredNote").hide();

    //hiding the data table if records are not available
    if($(".chkbox1").length == 0) {
        $("#tblWorkExperience").hide();
        $("#editWorkExperience").hide();
        $("#delWorkExperience").hide();
    }

    //if check all button clicked
    $("#workCheckAll").click(function() {
        $(".chkbox1").removeAttr("checked");
        if($("#workCheckAll").attr("checked")) {
            $(".chkbox1").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $(".chkbox1").click(function() {
        $("#workCheckAll").removeAttr('checked');
        if($(".chkbox1").length == $(".chkbox1:checked").length) {
            $("#workCheckAll").attr('checked', 'checked');
        }
    });

    $("#addWorkExperience").click(function() {

        removeEditLinks();
        clearMessageBar();

        //changing the headings
        $("#headChangeWorkExperience").text(lang_addWorkExperience);
        $(".chkbox1").hide();
        $("#workCheckAll").hide();        

        //hiding action button section
        $("#actionWorkExperience").hide();

        $('div#changeWorkExperience label.error').hide();

        $("#experience_seqno").val("");
        $("#experience_employer").val("");
        $("#experience_jobtitle").val("");
        $("#experience_from_date").val(dateDisplayFormat);
        $("#experience_to_date").val(dateDisplayFormat);
        $("#experience_comments").val("");

        //show add work experience form
        $("#changeWorkExperience").show();
        $("#workExpRequiredNote").show();
    });

    //clicking of delete button
    $("#delWorkExperience").click(function(){

        clearMessageBar();
        
        if ($(".chkbox1:checked").length > 0) {
            $("#frmDelWorkExperience").submit();
        } else {
            $("#workExpMessagebar").attr('class', 'messageBalloon_notice').text(lang_selectWrkExprToDelete);
        }

    });

    $("#btnWorkExpSave").click(function() {
        clearMessageBar();
        
        $("#frmWorkExperience").submit();
    });

    /* Valid From Date */
    $.validator.addMethod("validFromDate", function(value, element) {

        var fromdate	=	$('#experience_from_date').val();
        fromdate = (fromdate).split("-");

        var fromdateObj = new Date(parseInt(fromdate[0],10), parseInt(fromdate[1],10) - 1, parseInt(fromdate[2],10));
        var todate		=	$('#experience_to_date').val();
        todate = (todate).split("-");
        var todateObj	=	new Date(parseInt(todate[0],10), parseInt(todate[1],10) - 1, parseInt(todate[2],10));

        if(fromdateObj > todateObj){
            return false;
        }
        else{
            return true;
        }
    });

    //form validation
    var workExperienceValidator =
        $("#frmWorkExperience").validate({
        rules: {
            'experience[employer]': {required: true, maxlength: 100},
            'experience[jobtitle]': {required: true, maxlength: 120},
            'experience[from_date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}, validFromDate:true},
            'experience[to_date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}},
            'experience[comments]': {maxlength: 200}
        },
        messages: {
            'experience[employer]': {required: lang_companyRequired, maxlength: lang_companyMaxLength},
            'experience[jobtitle]': {required: lang_jobTitleRequired, maxlength: lang_jobTitleMaxLength},
            'experience[from_date]': {valid_date: lang_invalidDate, validFromDate: lang_fromDateLessToDate},
            'experience[to_date]': {valid_date: lang_invalidDate},
            'experience[comments]': {maxlength: lang_commentLength}
        },

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));

        }
    });
    
    $("#btnWorkExpCancel").click(function() {
        clearMessageBar();
        addEditLinks();
        
        workExperienceValidator.resetForm();
        
        $('div#changeWorkExperience label.error').hide();

        $(".chkbox1").removeAttr("checked");

        //hiding action button section
        $("#actionWorkExperience").show();

        $("#changeWorkExperience").hide();
        $("#workExpRequiredNote").hide();

        $(".chkbox1").show();
        $("#workCheckAll").show();
    });


    daymarker.bindElement("#experience_from_date", {
        onSelect: function(date){
            $("#experience_from_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#fromDateBtn').click(function() {
        daymarker.show("#experience_from_date");
    });

    daymarker.bindElement("#experience_to_date", {
        onSelect: function(date){
            $("#experience_to_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#toDateBtn').click(function() {
        daymarker.show("#experience_to_date");
    });
    
    $('form#frmDelWorkExperience table a.edit').live('click', function(event) {
        event.preventDefault();

        var seqno = $(this).closest("tr").find('input.chkbox1:first').val();
        clearMessageBar();

        //changing the headings
        $("#headChangeWorkExperience").text(lang_editWorkExperience);

        $('div#changeWorkExperience label.error').hide();

        //hiding action button section
        $("#actionWorkExperience").hide();

        //show add work experience form
        $("#changeWorkExperience").show();

        $("#experience_seqno").val(seqno);
        $("#experience_employer").val($("#employer_" + seqno).val());
        $("#experience_jobtitle").val($("#jobtitle_" + seqno).val());
        $("#experience_from_date").val($("#fromDate_" + seqno).val());
        $("#experience_to_date").val($("#toDate_" + seqno).val());
        $("#experience_comments").val($("#comment_" + seqno).val());

        if ($("#experience_from_date").val() == '') {
            $("#experience_from_date").val(dateDisplayFormat);
        }
        if ($("#experience_to_date").val() == '') {
            $("#experience_to_date").val(dateDisplayFormat);
        }

        $("#workExpRequiredNote").show();

        $(".chkbox1").hide();
        $("#workCheckAll").hide();       
    });

});

function fillDataToWorkExperienceDataPane(seqno) {


    return false;
}