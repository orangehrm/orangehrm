$(document).ready(function() {
    //--this section is for work experience

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
        //changing the headings
        $("#headChangeWorkExperience").text(lang_addWorkExperience);
        $(".chkbox1").hide();
        $("#workCheckAll").hide();

        //hiding action button section
        $("#actionWorkExperience").hide();

        $("#experience_seqno").val("");
        $("#experience_employer").val("");
        $("#experience_jobtitle").val("");
        $("#experience_from_date").val("");
        $("#experience_to_date").val("");
        $("#experience_comments").val("");

        //show add work experience form
        $("#changeWorkExperience").show();
        $("#workExpRequiredNote").show();
    });

    //clicking of delete button
    $("#delWorkExperience").click(function(){

        if($(".chkbox1:checked").length > 0) {
            $("#frmDelWorkExperience").submit();
        }

        $("#messagebar").attr('class', 'messageBalloon_notice');
        $("#messagebar").text(lang_selectWrkExprToDelete);

    });

    $("#btnWorkExpCancel").click(function() {
        $(".chkbox1").removeAttr("checked");

        //hiding action button section
        $("#actionWorkExperience").show();

        $("#changeWorkExperience").hide();
        $("#workExpRequiredNote").hide();

        $(".chkbox1").show();
        $("#workCheckAll").show();
    });

    $("#btnWorkExpSave").click(function() {
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
    $("#frmWorkExperience").validate({
        rules: {
            'experience[employer]': {required: true},
            'experience[jobtitle]': {required: true},
            'experience[from_date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}, validFromDate:true},
            'experience[to_date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}},
            'experience[comments]': {maxlength: 250}
        },
        messages: {
            'experience[employer]': {required: lang_companyRequired},
            'experience[jobtitle]': {required: lang_jobTitleRequired},
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

    //--this section is for education

    //hide add education section
    $("#changeEducation").hide();

    //hiding the data table if records are not available
    if($(".chkbox2").length == 0) {
        $("#tblEducation").hide();
        $("#editEducation").hide();
        $("#delEducation").hide();
    }

    //if check all button clicked
    $("#educationCheckAll").click(function() {
        $(".chkbox2").removeAttr("checked");
        if($("#educationCheckAll").attr("checked")) {
            $(".chkbox2").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $(".chkbox2").click(function() {
        $("#educationCheckAll").removeAttr('checked');
        if($(".chkbox2").length == $(".chkbox2:checked").length) {
            $("#educationCheckAll").attr('checked', 'checked');
        }
    });

    $("#addEducation").click(function() {
        //changing the headings
        $("#headChangeEducation").text("Add Education");

        //hiding action button section
        $("#actionEducation").hide();

        $("#education").val("");
        $("#major").val("");
        $("#score").val("");
        $("#startDate").val("");
        $("#endDate").val("");

        //show add education form
        $("#changeEducation").show();
    });

    $("#btnEducationCancel").click(function() {
        $(".chkbox2").removeAttr("checked");

        //hiding action button section
        $("#actionEducation").show();

        $("#changeEducation").hide();
    });

    $("#editEducation").click(function() {
        //changing the headings
        $("#headChangeEducation").text("Edit Education");

        //hiding action button section
        $("#actionEducation").hide();

        //show add work experience form
        $("#changeEducation").show();

        $("#education").val(1);
        $("#major").val("Applied Math");
        $("#score").val("4.2");
        $("#startDate").val("2009-01-01");
        $("#endDate").val("2009-12-31");

    });

    //--this section is for skills
    //-------------------------------

    //hide add education section
    $("#changeSkills").hide();

    //hiding the data table if records are not available
    if($(".chkbox3").length == 0) {
        $("#tblSkills").hide();
        $("#editSkills").hide();
        $("#delSkills").hide();
    }

    //if check all button clicked
    $("#skillsCheckAll").click(function() {
        $(".chkbox3").removeAttr("checked");
        if($("#skillsCheckAll").attr("checked")) {
            $(".chkbox3").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $(".chkbox3").click(function() {
        $("#skillsCheckAll").removeAttr('checked');
        if($(".chkbox3").length == $(".chkbox3:checked").length) {
            $("#skillsCheckAll").attr('checked', 'checked');
        }
    });

    $("#addSkills").click(function() {
        //changing the headings
        $("#headChangeSkills").text("Add Skills");

        //hiding action button section
        $("#actionSkills").hide();

        $("#skills").val("");
        $("#experience").val("");
        $("#skillcomment").val("");

        //show add education form
        $("#changeSkills").show();
    });

    $("#btnSkillsCancel").click(function() {
        $(".chkbox3").removeAttr("checked");

        //hiding action button section
        $("#actionSkills").show();

        $("#changeSkills").hide();
    });

    $("#editSkills").click(function() {
        //changing the headings
        $("#headChangeSkills").text("Edit Skills");

        //hiding action button section
        $("#actionSkills").hide();

        //show add work experience form
        $("#changeSkills").show();

        $("#skills").val("Flying Kite");
        $("#experience").val("3");
        $("#skillcomment").val("Young for Ever");

    });

    //--this section is for language
    //-------------------------------

    //hide add education section
    $("#changeLang").hide();

    //hiding the data table if records are not available
    if($(".chkbox4").length == 0) {
        $("#tblLang").hide();
        $("#editLang").hide();
        $("#delLang").hide();
    }

    //if check all button clicked
    $("#langCheckAll").click(function() {
        $(".chkbox4").removeAttr("checked");
        if($("#langCheckAll").attr("checked")) {
            $(".chkbox4").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $(".chkbox4").click(function() {
        $("#langCheckAll").removeAttr('checked');
        if($(".chkbox4").length == $(".chkbox4:checked").length) {
            $("#langCheckAll").attr('checked', 'checked');
        }
    });

    $("#addLang").click(function() {
        //changing the headings
        $("#headChangeLang").text("Add Language");

        //hiding action button section
        $("#actionLang").hide();

        $("#language").val("");
        $("#fluency").val("");
        $("#competency").val("0");

        //show add education form
        $("#changeLang").show();
    });

    $("#btnLangCancel").click(function() {
        $(".chkbox4").removeAttr("checked");

        //hiding action button section
        $("#actionLang").show();

        $("#changeLang").hide();
    });

    $("#editLang").click(function() {
        //changing the headings
        $("#headChangeLang").text("Edit Language");

        //hiding action button section
        $("#actionLang").hide();

        //show add form
        $("#changeLang").show();

        $("#language").val("1");
        $("#fluency").val("3");
        $("#competency").val("3");

    });

    //--this section is for licenses
    //-------------------------------

    //hide add education section
    $("#changeLicenses").hide();

    //hiding the data table if records are not available
    if($(".chkbox5").length == 0) {
        $("#tblLicenses").hide();
        $("#editLicenses").hide();
        $("#delLicenses").hide();
    }

    //if check all button clicked
    $("#licensesCheckAll").click(function() {
        $(".chkbox5").removeAttr("checked");
        if($("#licensesCheckAll").attr("checked")) {
            $(".chkbox5").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $(".chkbox5").click(function() {
        $("#licensesCheckAll").removeAttr('checked');
        if($(".chkbox5").length == $(".chkbox5:checked").length) {
            $("#licensesCheckAll").attr('checked', 'checked');
        }
    });

    $("#addLicenses").click(function() {
        //changing the headings
        $("#headChangeLicenses").text("Add License");

        //hiding action button section
        $("#actionLicenses").hide();

        $("#license").val("");
        $("#startDate").val("");
        $("#endDate").val("");

        //show add education form
        $("#changeLicenses").show();
    });

    $("#btnLicensesCancel").click(function() {
        $(".chkbox5").removeAttr("checked");

        //hiding action button section
        $("#actionLicenses").show();

        $("#changeLicenses").hide();
    });

    $("#editLicenses").click(function() {
        //changing the headings
        $("#headChangeLicenses").text("Edit Language");

        //hiding action button section
        $("#actionLicenses").hide();

        //show add form
        $("#changeLicenses").show();

        $("#license").val("1");
        $("#licStartDate").val("2007-06-12");
        $("#licEndDate").val("2011-06-21");

    });
    imageResize();
});

function fillDataToWorkExperienceDataPane(seqno) {

    //changing the headings
    $("#headChangeWorkExperience").text(lang_editWorkExperience);

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

    $("#workExpRequiredNote").show();

    $(".chkbox1").hide();
    $("#workCheckAll").hide();
}