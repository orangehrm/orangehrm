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

    var fromDate = "";

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
        $('div#sectionWorkExperience .check').hide();
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
        $("#experience_from_date").val(displayDateFormat);
        $("#experience_to_date").val(displayDateFormat);
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
        fromDate = $('#experience_from_date').val();
        $("#frmWorkExperience").submit();
    });

    //form validation
    var workExperienceValidator =
    $("#frmWorkExperience").validate({
        rules: {
            'experience[employer]': {
                required: true,
                maxlength: 100
            },
            'experience[jobtitle]': {
                required: true,
                maxlength: 100
            },
            'experience[from_date]': {
                valid_date: function(){
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                }
            },
            'experience[to_date]': {
                valid_date: function(){
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                },
                date_range: function() {
                    return {
                        format:datepickerDateFormat,
                        displayFormat:displayDateFormat,
                        fromDate:fromDate
                    }
                }
            },
            'experience[comments]': {
                maxlength: 200
            }
        },
        messages: {
            'experience[employer]': {
                required: lang_companyRequired,
                maxlength: lang_companyMaxLength
            },
            'experience[jobtitle]': {
                required: lang_jobTitleRequired,
                maxlength: lang_jobTitleMaxLength
            },
            'experience[from_date]': {
                valid_date: lang_invalidDate
            },
            'experience[to_date]': {
                valid_date: lang_invalidDate,
                date_range: lang_fromDateLessToDate
            },
            'experience[comments]': {
                maxlength: lang_commentLength
            }
        }
    });
    
    $("#btnWorkExpCancel").click(function() {
        clearMessageBar();
        if(canEdit){
            addEditLinks();
        }
        
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
            $("#experience_from_date").val(displayDateFormat);
        }
        if ($("#experience_to_date").val() == '') {
            $("#experience_to_date").val(displayDateFormat);
        }

        $("#workExpRequiredNote").show();

        $(".chkbox1").hide();
        $("#workCheckAll").hide();
    });

});

function fillDataToWorkExperienceDataPane(seqno) {


    return false;
}