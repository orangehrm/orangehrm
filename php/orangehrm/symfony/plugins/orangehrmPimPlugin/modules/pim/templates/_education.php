<div id="educationMessagebar"></div>

<div class="sectionDiv" id="sectionEducation">
    <div><h3><?php echo __('Education'); ?></h3></div>

    <div class="outerbox" id="changeEducation" style="width:500px;">
        <div class="mainHeading"><h4 id="headChangeEducation"><?php echo __('Add Education'); ?></h4></div>
        <form id="frmEducation" action="<?php echo url_for('pim/saveDeleteEducation?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['emp_number']->render(); ?>

            <?php echo $form['code']->renderLabel(__('Program') . ' <span class="required">*</span>'); ?>
            <?php echo $form['code']->render(array("class" => "formSelect")); ?>
            <span id="static_education_code" style="display:none;"></span>
            <br class="clear"/>

            <?php echo $form['major']->renderLabel(__('Major/Specialization')); ?>
            <?php echo $form['major']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
            <br class="clear"/>

            <?php echo $form['year']->renderLabel(__('Year')); ?>
            <?php echo $form['year']->render(array("class" => "formInputText", "maxlength" => 4)); ?>
            <br class="clear"/>

            <?php echo $form['gpa']->renderLabel(__('GPA/Score')); ?>
            <?php echo $form['gpa']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
            <br class="clear"/>

            <?php echo $form['start_date']->renderLabel(__('Start Date')); ?>
            <?php echo $form['start_date']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
            <input id="startDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
            <br class="clear"/>

            <?php echo $form['end_date']->renderLabel(__('End Date')); ?>
            <?php echo $form['end_date']->render(array("class" => "formInputText", "maxlength" => 10)); ?>
            <input id="endDateBtn" type="button" name="Submit" value="  " class="calendarBtn" />
            <br class="clear"/>


            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnEducationSave" value="<?php echo __("Save"); ?>" />
                <input type="button" class="savebutton" id="btnEducationCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>
    <div class="smallText" id="educationRequiredNote"><?php echo __('Fields marked with an asterisk')?>
        <span class="required">*</span> <?php echo __('are required.')?></div>
    <br />
    <div id="actionEducation">
        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addEducation" />&nbsp;
        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delEducation" />
    </div>

    <form id="frmDelEducation" action="<?php echo url_for('pim/saveDeleteEducation?empNumber=' . $empNumber . "&option=delete"); ?>" method="post">
        <div class="outerbox" id="tblEducation">
            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                <thead>
                <tr>
                    <td class="check"><input type="checkbox" id="educationCheckAll" /></td>
                    <td><?php echo __('Program');?></td>
                    <td><?php echo __('Year');?></td>
                    <td><?php echo __('GPA/Score');?></td>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $educations = $form->empEducationList;//var_dump($educations->toArray());die;
                    $row = 0;

                    foreach ($educations as $education) {                        
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        //empty($education->from_date)
                        $startDate = ohrm_format_date($education->start_date);
                        $endDate = ohrm_format_date($education->end_date);
                        $eduDesc = htmlspecialchars($education->Education->edu_uni . ", " . $education->Education->edu_deg);
                        ?>
                    <tr class="<?php echo $cssClass;?>">
                <td class="check"><input type="hidden" id="code_<?php echo $education->code;?>" value="<?php echo htmlspecialchars($education->code); ?>" />
                <input type="hidden" id="code_desc_<?php echo $education->code;?>" value="<?php echo $eduDesc; ?>" />
                <input type="hidden" id="major_<?php echo $education->code;?>" value="<?php echo htmlspecialchars($education->major); ?>" />
                <input type="hidden" id="year_<?php echo $education->code;?>" value="<?php echo htmlspecialchars($education->year); ?>" />
                <input type="hidden" id="gpa_<?php echo $education->code;?>" value="<?php echo htmlspecialchars($education->gpa); ?>" />
                <input type="hidden" id="start_date_<?php echo $education->code;?>" value="<?php echo $startDate; ?>" />
                <input type="hidden" id="end_date_<?php echo $education->code;?>" value="<?php echo $endDate; ?>" />

                <input type="checkbox" class="chkbox" value="<?php echo $education->code;?>" name="delEdu[]"/></td>
                <td class="program"><a href="#" class="edit"><?php echo $eduDesc;?></a></td>
                <td><?php echo htmlspecialchars($education->year);?></td>
                <td><?php echo htmlspecialchars($education->gpa);?></td>
                </tr>
                    <?php $row++;
                }?>
                </tbody>
            </table>
        </div>
    </form>

</div>
<script type="text/javascript">
    //<![CDATA[

    var fileModified = 0;
    var lang_addEducation = "<?php echo __('Add Education');?>";
    var lang_editEducation = "<?php echo __('Edit Education');?>";
    var lang_educationRequired = "<?php echo __("Program is required");?>";
    var lang_invalidDate = "<?php echo __("Please enter a valid date in %format% format", array('%format%'=>$sf_user->getDateFormat())) ?>";
    var lang_startDateAfterEndDate = "<?php echo __('Start date should be before end date');?>";
    var lang_selectEducationToDelete = "<?php echo __('Please Select At Least One Education Item To Delete');?>";
    var lang_majorMaxLength = "<?php echo __('Major cannot exceed 100 characters in length');?>";
    var lang_gpaMaxLength = "<?php echo __('GPA/Score cannot exceed 25 characters in length');?>";
    var lang_yearShouldBeNumber = "<?php echo __('Year should be a number');?>";

    var dateFormat  = '<?php echo $sf_user->getDateFormat();?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat());?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    //]]>
</script>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {

    //hide add section
    $("#changeEducation").hide();
    $("#educationRequiredNote").hide();

    //hiding the data table if records are not available
    if($("div#tblEducation table.data-table .chkbox").length == 0) {
        $("#tblEducation").hide();
        $("#editEducation").hide();
        $("#delEducation").hide();
    }

    //if check all button clicked
    $("#educationCheckAll").click(function() {
        $("div#tblEducation .chkbox").removeAttr("checked");
        if($("#educationCheckAll").attr("checked")) {
            $("div#tblEducation .chkbox").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $("div#tblEducation .chkbox").click(function() {
        $("#educationCheckAll").removeAttr('checked');
        if($("div#tblEducation .chkbox").length == $("div#tblEducation .chkbox:checked").length) {
            $("#educationCheckAll").attr('checked', 'checked');
        }
    });

    $("#addEducation").click(function() {

        clearMessageBar();
        $('div#changeEducation label.error').hide();        
        

        //changing the headings
        $("#headChangeEducation").text(lang_addEducation);
        $("div#tblEducation .chkbox").hide();
        $("#educationCheckAll").hide();

        //hiding action button section
        $("#actionEducation").hide();

        $('#static_education_code').hide().val("");        
        $("#education_code").show().val("");
        $("#education_code option[class='added']").remove();
        $("#education_major").val("");
        $("#education_year").val("");
        $("#education_gpa").val("");
        $("#education_start_date").val(dateDisplayFormat);
        $("#education_end_date").val(dateDisplayFormat);

        //show add form
        $("#changeEducation").show();
        $("#educationRequiredNote").show();
    });

    //clicking of delete button
    $("#delEducation").click(function(){

        clearMessageBar();

        if ($("div#tblEducation .chkbox:checked").length > 0) {
            $("#frmDelEducation").submit();
        } else {
            $("#educationMessagebar").attr('class', 'messageBalloon_notice').text(lang_selectEducationToDelete);
        }

    });

    $("#btnEducationSave").click(function() {
        clearMessageBar();

        $("#frmEducation").submit();
    });

    /* Valid From Date */
    $.validator.addMethod("validFromDate2", function(value, element) {

        var fromdate	=	$('#education_start_date').val();
        fromdate = (fromdate).split("-");

        var fromdateObj = new Date(parseInt(fromdate[0],10), parseInt(fromdate[1],10) - 1, parseInt(fromdate[2],10));
        var todate		=	$('#education_end_date').val();
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
    var educationValidator =
        $("#frmEducation").validate({
        rules: {
            'education[code]': {required: true},
            'education[major]': {required: false, maxlength: 100},
            'education[year]': {required: false, digits: true},
            'education[gpa]': {required: false, maxlength: 25},
            'education[start_date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}, validFromDate2:true},
            'education[end_date]': {valid_date: function(){return {format:jsDateFormat, displayFormat:dateDisplayFormat, required:false}}}
        },
        messages: {
            'education[code]': {required: lang_educationRequired},
            'education[major]': {maxlength: lang_majorMaxLength},
            'education[year]': {digits: lang_yearShouldBeNumber},
            'education[gpa]': {maxlength: lang_gpaMaxLength},            
            'education[start_date]': {valid_date: lang_invalidDate, validFromDate2: lang_startDateAfterEndDate},
            'education[end_date]': {valid_date: lang_invalidDate}
        },

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));

        }
    });

    $("#btnEducationCancel").click(function() {
        clearMessageBar();

        educationValidator.resetForm();
        
        $('div#changeEducation label.error').hide();

        $("div#tblEducation .chkbox").removeAttr("checked").show();
        
        //hiding action button section
        $("#actionEducation").show();
        $("#changeEducation").hide();
        $("#educationRequiredNote").hide();        
        $("#educationCheckAll").show();
        
        // remove any options already in use
        $("#education_code option[class='added']").remove();
        $('#static_education_code').hide().val("");

    });


    daymarker.bindElement("#education_start_date", {
        onSelect: function(date){
            $("#education_start_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#startDateBtn').click(function() {
        daymarker.show("#education_start_date");
    });

    daymarker.bindElement("#education_end_date", {
        onSelect: function(date){
            $("#education_end_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#endDateBtn').click(function() {
        daymarker.show("#education_end_date");
    });
    
    $('form#frmDelEducation a.edit').click(function() {
        clearMessageBar();

        //changing the headings
        $("#headChangeEducation").text(lang_editEducation);

        educationValidator.resetForm();

        $('div#changeEducation label.error').hide();

        //hiding action button section
        $("#actionEducation").hide();

        //show add form
        $("#changeEducation").show();
        var code = $(this).closest("tr").find('input.chkbox:first').val();
        
        $('#static_education_code').text($("#code_desc_" + code).val()).show();

        // remove any options already in use
        $("#education_code option[class='added']").remove();

        $('#education_code').hide().
              append($("<option class='added'></option>").
              attr("value", code).
              text($("#code_desc_" + code).val())); 

        $('#education_code').val(code);

        $("#education_major").val($("#major_" + code).val());
        $("#education_year").val($("#year_" + code).val());
        $("#education_gpa").val($("#gpa_" + code).val());
        $("#education_start_date").val($("#start_date_" + code).val());
        $("#education_end_date").val($("#end_date_" + code).val());
        
        if ($("#education_start_date").val() == '') {
            $("#education_start_date").val(dateDisplayFormat);
        }
        if ($("#education_end_date").val() == '') {
            $("#education_end_date").val(dateDisplayFormat);
        }
        
        $("#educationRequiredNote").show();

        $("div#tblEducation .chkbox").hide();
        $("#educationCheckAll").hide();        
    });
});

//]]>
</script>