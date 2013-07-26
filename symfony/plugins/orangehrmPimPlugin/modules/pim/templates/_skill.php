<?php  
$haveSkills = count($form->empSkillList)>0;
?>
       
<a name="skill"></a>
<?php if ($skillPermissions->canCreate() || ($haveSkills && $skillPermissions->canUpdate())) { ?>
<div id="changeSkill">
    <div class="head">
        <h1 id="headChangeSkill"><?php echo __('Add Skill'); ?></h1>
    </div>
                
    <div class="inner">
        <form id="frmSkill" action="<?php echo url_for('pim/saveDeleteSkill?empNumber=' . 
                $empNumber . "&option=save"); ?>" method="post">
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['emp_number']->render(); ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['code']->renderLabel(__('Skill') . ' <em>*</em>'); ?>
                        <?php echo $form['code']->render(array("class" => "formSelect")); ?>
                    </li>
                    <li>
                        <?php echo $form['years_of_exp']->renderLabel(__('Years of Experience')); ?>
                        <?php echo $form['years_of_exp']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                    </li>
                   <li class="largeTextBox">
                        <?php echo $form['comments']->renderLabel(__('Comments')); ?>
                        <?php echo $form['comments']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="btnSkillSave" value="<?php echo __("Save"); ?>" />
                    <?php if ((!$haveSkills) || ($haveSkills && $skillPermissions->canCreate()) || 
                            ($skillPermissions && $skillPermissions->canUpdate())) { ?>
                    <input type="button" class="reset" id="btnSkillCancel" value="<?php echo __("Cancel"); ?>" />
                    <?php } ?>
                </p>
            </fieldset>
        </form>
    </div>
</div> <!-- changeSkill -->
<?php } ?>
        
<div class="miniList" id="tblSkill">
    <div class="head">
        <h1><?php echo __("Skills"); ?></h1>
    </div>
            
    <div class="inner">

        <?php if ($skillPermissions->canRead()) : ?>
        <?php include_partial('global/flash_messages', array('prefix' => 'skill')); ?>

        <form id="frmDelSkill" action="<?php echo url_for('pim/saveDeleteSkill?empNumber=' . 
                $empNumber . "&option=delete"); ?>" method="post">
            <?php echo $listForm ?>
            <p id="actionSkill">
                <?php if ($skillPermissions->canCreate() ) { ?>
                <input type="button" value="<?php echo __("Add");?>" class="" id="addSkill" />
                <?php } ?>
                <?php if ($skillPermissions->canDelete() ) { ?>
                <input type="button" value="<?php echo __("Delete");?>" class="delete" id="delSkill" />
                <?php } ?>
            </p>
            <table id="" cellpadding="0" cellspacing="0" width="100%" class="table tablesorter">
                <thead>
                    <tr>
                        <?php if ($skillPermissions->canDelete()) { ?>
                        <th class="check" width="2%"><input type="checkbox" id="skillCheckAll" /></th>
                        <?php } ?>
                        <th><?php echo __('Skill'); ?></th>
                        <th><?php echo __('Years of Experience'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$haveSkills) { ?>
                    <tr>
                        <?php if ($skillPermissions->canDelete()) { ?>
                        <td class="check"></td>
                        <?php } ?>
                        <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                        <td></td>
                    </tr>
                    <?php } else { ?>                        
                    <?php
                    $skills = $form->empSkillList;
                    $row = 0;
                    foreach ($skills as $skill) :
                        $cssClass = ($row % 2) ? 'even' : 'odd';
                        $skillName = $skill->getSkill()->getName();
                        ?>
                        <tr class="<?php echo $cssClass; ?>">
                            <td class="check">
                                <input type="hidden" id="code_<?php echo $skill->skillId; ?>" 
                                       value="<?php echo htmlspecialchars($skill->skillId); ?>" />
                                <input type="hidden" id="skill_name_<?php echo $skill->skillId; ?>" 
                                       value="<?php echo htmlspecialchars($skillName); ?>" />
                                <input type="hidden" id="years_of_exp_<?php echo $skill->skillId; ?>" 
                                       value="<?php echo htmlspecialchars($skill->years_of_exp); ?>" />
                                <input type="hidden" id="comments_<?php echo $skill->skillId; ?>" 
                                       value="<?php echo htmlspecialchars($skill->comments); ?>" />
                                <?php if ($skillPermissions->canDelete()) { ?>
                                <input type="checkbox" class="chkbox" value="<?php echo $skill->skillId; ?>" 
                                       name="delSkill[]"/>
                                <?php } else { ?>
                                <input type="hidden" class="chkbox" value="<?php echo $skill->skillId; ?>" 
                                       name="delSkill[]"/>
                                <?php } ?>
                            </td>
                            <td class="name">
                                <?php if ($skillPermissions->canUpdate()) { ?>
                                <a href="#" class="edit"><?php echo htmlspecialchars($skillName); ?></a>
                                <?php
                                } else {
                                    echo htmlspecialchars($skillName);
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($skill->years_of_exp); ?></td>
                        </tr>
                        <?php
                        $row++;
                    endforeach;
                    }
                    ?>
                </tbody>
            </table>
        </form>

        <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
        <?php endif; ?>

    </div>
</div> <!-- miniList-tblSkill -->

<script type="text/javascript">
    //<![CDATA[
    var fileModified = 0;
    var lang_addSkill = "<?php echo __('Add Skill'); ?>";
    var lang_editSkill = "<?php echo __('Edit Skill'); ?>";
    var lang_skillRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_selectSkillToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
    var lang_commentsMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_yearsOfExpShouldBeNumber = "<?php echo __('Should be a number'); ?>";
    var lang_yearsOfExpMax = "<?php echo __("Should be less than %amount%", array("%amount%" => '100')); ?>";
    var canUpdate = '<?php echo $skillPermissions->canUpdate(); ?>';
    //]]>
</script>

<script type="text/javascript">
    //<![CDATA[
    
    $(document).ready(function() {
        //To hide unchanged element into hide and show the value in span while editing
        $('#skill_code').after('<span id="static_skill_code" style="display:none;"></span>');

        function addEditLinks() {
            // called here to avoid double adding links - When in edit mode and cancel is pressed.
            removeEditLinks();
            $('form#frmDelSkill table tbody td.name').wrapInner('<a class="edit" href="#"/>');
        }
        
        function removeEditLinks() {
            $('form#frmDelSkill table tbody td.name a').each(function(index) {
                $(this).parent().text($(this).text());
            });
        }
        
        //hide add section
        $("#changeSkill").hide();
        $("#skillRequiredNote").hide();
        
        //hiding the data table if records are not available
        if($("div#tblSkill .chkbox").length == 0) {
            //$("#tblSkill").hide();
            $('div#tblSkill .check').hide();
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
            
            removeEditLinks();
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
                'skill[years_of_exp]': {required: false, digits: true, max: 99},
                'skill[comments]': {required: false, maxlength:100}
            },
            messages: {
                'skill[code]': {required: lang_skillRequired},
                'skill[years_of_exp]': {digits: lang_yearsOfExpShouldBeNumber, max: lang_yearsOfExpMax},
                'skill[comments]': {maxlength: lang_commentsMaxLength}
            }
        });
        
        $("#btnSkillCancel").click(function() {
            clearMessageBar();
            if(canUpdate){
                addEditLinks();
            }
            
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
            
            //remove if disabled while edit
            $('#skill_code').removeAttr('disabled');
        });
        
        $('form#frmDelSkill a.edit').live('click', function(event) {
            event.preventDefault();
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
            
            $('#static_skill_code').text($("#skill_name_" + code).val()).show();
            
            
            
            // remove any options already in use
            $("#skill_code option[class='added']").remove();
            
            $('#skill_code').
                append($("<option class='added'></option>").
                attr("value", code).
                text($("#skill_name_" + code).val())); 
            $('#skill_code').val(code).hide();
            
            $("#skill_years_of_exp").val($("#years_of_exp_" + code).val());
            $("#skill_comments").val($("#comments_" + code).val());
            
            $("#skillRequiredNote").show();
            
            $("div#tblSkill .chkbox").hide();
            $("#skillCheckAll").hide();        
        });
    });
    
    //]]>
</script>