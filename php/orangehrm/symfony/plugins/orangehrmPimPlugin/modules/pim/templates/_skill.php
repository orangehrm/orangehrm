<div id="skillMessagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 630px;">
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div class="sectionDiv" id="sectionSkill">
    <div><h3><?php echo __('Skills'); ?></h3></div>

    <div class="outerbox" id="changeSkill" style="width:500px;">
        <div class="mainHeading"><h2 id="headChangeSkill"><?php echo __('Add Skill'); ?></h2></div>
        <form id="frmSkill" action="<?php echo url_for('pim/saveDeleteSkill?empNumber=' . $empNumber . "&option=save"); ?>" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['emp_number']->render(); ?>

            <?php echo $form['code']->renderLabel(__('Skill') . ' <span class="required">*</span>'); ?>
            <?php echo $form['code']->render(array("class" => "formInputText")); ?>
            <span id="static_skill_code" style="display:none;"></span>
            <br class="clear"/>

            <?php echo $form['years_of_exp']->renderLabel(__('Years of Experience')); ?>
            <?php echo $form['years_of_exp']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
            <br class="clear"/>

            <?php echo $form['comments']->renderLabel(__('Comments')); ?>
            <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
            <br class="clear"/>

            <div class="formbuttons">
                <input type="button" class="savebutton" id="btnSkillSave" value="<?php echo __("Save"); ?>" />
                <input type="button" class="savebutton" id="btnSkillCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>
    <div class="smallText" id="skillRequiredNote"><?php echo __('Fields marked with an asterisk')?>
        <span class="required">*</span> <?php echo __('are required.')?></div>
    <br />
    <div id="actionSkill">
        <input type="button" value="<?php echo __("Add");?>" class="savebutton" id="addSkill" />&nbsp;
        <input type="button" value="<?php echo __("Delete");?>" class="savebutton" id="delSkill" />
        <br /><br />
    </div>

    <form id="frmDelSkill" action="<?php echo url_for('pim/saveDeleteSkill?empNumber=' . $empNumber . "&option=delete"); ?>" method="post">
        <div class="outerbox" id="tblSkill">
            <table width="100%" cellspacing="0" cellpadding="0" class="data-table" border="0">
                <thead>
                <tr>
                    <td class="check"><input type="checkbox" id="skillCheckAll" /></td>
                    <td><?php echo __('Skill');?></td>
                    <td><?php echo __('Years of Experience');?></td>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $skills = $form->empSkillList;//var_dump($skills->toArray());die;
                    $row = 0;

                    foreach ($skills as $skill) {                        
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $skillName = $skill->getSkill()->getSkillName();
                        ?>
                    <tr class="<?php echo $cssClass;?>">
                <td class="check"><input type="hidden" id="code_<?php echo $skill->code;?>" value="<?php echo htmlspecialchars($skill->code); ?>" />
                    <input type="hidden" id="skill_name_<?php echo $skill->code;?>" value="<?php echo htmlspecialchars($skillName); ?>" />
                <input type="hidden" id="years_of_exp_<?php echo $skill->code;?>" value="<?php echo htmlspecialchars($skill->years_of_exp); ?>" />
                <input type="hidden" id="comments_<?php echo $skill->code;?>" value="<?php echo htmlspecialchars($skill->comments); ?>" />

                <input type="checkbox" class="chkbox" value="<?php echo $skill->code;?>" name="delSkill[]"/></td>
                <td><a href="#" class="edit"><?php echo $skillName;?></a></td>
                <td><?php echo htmlspecialchars($skill->years_of_exp);?></td>
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
    var lang_addSkill = "<?php echo __('Add Skill');?>";
    var lang_editSkill = "<?php echo __('Edit Skill');?>";
    var lang_skillRequired = "<?php echo __("Skill is required");?>";
    var lang_selectSkillToDelete = "<?php echo __('Please Select At Least One Skill Item To Delete');?>";
    var lang_commentsMaxLength = "<?php echo __('Comments cannot exceed 100 characters in length');?>";
    var lang_yearsOfExpShouldBeNumber = "<?php echo __('Years of Experience should be a number');?>";

    var dateFormat  = '<?php echo $sf_user->getDateFormat();?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat());?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    //]]>
</script>

<script type="text/javascript">
//<![CDATA[

$(document).ready(function() {

    //hide add section
    $("#changeSkill").hide();
    $("#skillRequiredNote").hide();

    //hiding the data table if records are not available
    if($("div#tblSkill table.data-table .chkbox").length == 0) {
        $("#tblSkill").hide();
        $("#editSkill").hide();
        $("#delSkill").hide();
    }

    //if check all button clicked
    $("#skillCheckAll").click(function() {
        $("div#tblSkill .chkbox").removeAttr("checked");
        if($("#skillCheckAll").attr("checked")) {
            $("div#tblSkill .chkbox").attr("checked", "checked");
        }
    });

    //remove tick from the all button if any checkbox unchecked
    $("div#tblSkill .chkbox").click(function() {
        $("#skillCheckAll").removeAttr('checked');
        if($("div#tblSkill .chkbox").length == $("div#tblSkill .chkbox:checked").length) {
            $("#skillCheckAll").attr('checked', 'checked');
        }
    });

    $("#addSkill").click(function() {

        clearMessageBar();
        $('div#changeSkill label.error').hide();        
        

        //changing the headings
        $("#headChangeSkill").text(lang_addSkill);
        $("div#tblSkill .chkbox").hide();
        $("#skillCheckAll").hide();

        //hiding action button section
        $("#actionSkill").hide();

        $('#static_skill_code').hide().val("");
        $("#skill_code").show().val("");
        $("#skill_code option[class='added']").remove();
        $("#skill_major").val("");
        $("#skill_year").val("");
        $("#skill_gpa").val("");
        $("#skill_start_date").val("");
        $("#skill_end_date").val("");

        //show add form
        $("#changeSkill").show();
        $("#skillRequiredNote").show();
    });

    //clicking of delete button
    $("#delSkill").click(function(){

        clearMessageBar();

        if ($("div#tblSkill .chkbox:checked").length > 0) {
            $("#frmDelSkill").submit();
        } else {
            $("#skillMessagebar").attr('class', 'messageBalloon_notice').text(lang_selectSkillToDelete);
        }

    });

    $("#btnSkillSave").click(function() {
        clearMessageBar();

        $("#frmSkill").submit();
    });

    //form validation
    var skillValidator =
        $("#frmSkill").validate({
        rules: {
            'skill[code]': {required: true},
            'skill[years_of_exp]': {required: false, digits: true},
            'skill[comments]': {required: false, maxlength:100}
        },
        messages: {
            'skill[code]': {required: lang_skillRequired},
            'skill[years_of_exp]': {digits: lang_yearsOfExpShouldBeNumber},
            'skill[comments]': {maxlength: lang_commentsMaxLength}
        },

        errorElement : 'div',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next(".clear"));
            error.insertAfter(element.next().next(".clear"));

        }
    });

    $("#btnSkillCancel").click(function() {
        clearMessageBar();

        skillValidator.resetForm();
        
        $('div#changeSkill label.error').hide();

        $("div#tblSkill .chkbox").removeAttr("checked").show();
        
        //hiding action button section
        $("#actionSkill").show();
        $("#changeSkill").hide();
        $("#skillRequiredNote").hide();        
        $("#skillCheckAll").show();
        
        // remove any options already in use
        $("#skill_code option[class='added']").remove();
        $('#static_skill_code').hide().val("");
    });


    daymarker.bindElement("#skill_start_date", {
        onSelect: function(date){
            $("#skill_start_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#startDateBtn').click(function() {
        daymarker.show("#skill_start_date");
    });

    daymarker.bindElement("#skill_end_date", {
        onSelect: function(date){
            $("#skill_end_date").valid();
            },
            dateFormat:jsDateFormat
        });

    $('#endDateBtn').click(function() {
        daymarker.show("#skill_end_date");
    });
    
    $('form#frmDelSkill a.edit').click(function() {
        clearMessageBar();

        //changing the headings
        $("#headChangeSkill").text(lang_editSkill);

        skillValidator.resetForm();

        $('div#changeSkill label.error').hide();

        //hiding action button section
        $("#actionSkill").hide();

        //show add form
        $("#changeSkill").show();
        var code = $(this).closest("tr").find('input.chkbox:first').val();

        $('#static_skill_code').html($("#skill_name_" + code).val()).show();
        
        

        // remove any options already in use
        $("#skill_code option[class='added']").remove();

        $('#skill_code').hide().
              append($("<option class='added'></option>").
              attr("value", code).
              text($("#skill_name_" + code).val())); 
        $('#skill_code').val(code);

        $("#skill_years_of_exp").val($("#years_of_exp_" + code).val());
        $("#skill_comments").val($("#comments_" + code).val());

        $("#skillRequiredNote").show();

        $("div#tblSkill .chkbox").hide();
        $("#skillCheckAll").hide();        
    });
});

//]]>
</script>