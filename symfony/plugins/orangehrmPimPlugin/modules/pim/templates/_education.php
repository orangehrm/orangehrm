<?php
$haveEducation = count($form->empEducationList) > 0;
?>

<a name="education"></a>
<?php if (($educationPermissions->canCreate()) || ($haveEducation && $educationPermissions->canUpdate() )) { ?>
<div id="changeEducation">
    <div class="head">
        <h1 id="headChangeEducation"><?php echo __('Add Education'); ?></h1>
    </div>
    <div class="inner">
        <form id="frmEducation" action="<?php echo url_for('pim/saveDeleteEducation?empNumber=' . 
                $empNumber . "&option=save"); ?>" method="post">
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['id']->render(); ?>
            <?php echo $form['emp_number']->render(); ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['code']->renderLabel(__('Level') . ' <em>*</em>'); ?>
                        <?php echo $form['code']->render(array("class" => "formSelect")); ?>
                    </li>
                    <li>
                        <?php echo $form['institute']->renderLabel(__('Institute')); ?>
                        <?php echo $form['institute']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                    </li>
                    <li>
                        <?php echo $form['major']->renderLabel(__('Major/Specialization')); ?>
                        <?php echo $form['major']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                    </li>
                    <li>
                        <?php echo $form['year']->renderLabel(__('Year')); ?>
                        <?php echo $form['year']->render(array("class" => "formInputText", "maxlength" => 4)); ?>
                    </li>
                    <li>
                        <?php echo $form['gpa']->renderLabel(__('GPA/Score')); ?>
                        <?php echo $form['gpa']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
                    </li>
                    <li>
                        <?php echo $form['start_date']->renderLabel(__('Start Date')); ?>
                        <?php echo $form['start_date']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li>
                        <?php echo $form['end_date']->renderLabel(__('End Date')); ?>
                        <?php echo $form['end_date']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li class="required line">
                          <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="btnEducationSave" value="<?php echo __("Save"); ?>" />
                    <input type="button" class="reset" id="btnEducationCancel" value="<?php echo __("Cancel"); ?>" />
                </p>
            </fieldset>
        </form>
    </div>
</div> <!-- changeEducation -->
<?php } ?>
    
<div class="miniList" id="">

    <div class="head">
        <h1><?php echo __('Education'); ?></h1>
    </div>
    
    <div class="inner">

        <?php if ($educationPermissions->canRead()) : ?>

        <?php include_partial('global/flash_messages', array('prefix' => 'education')); ?>

        <form id="frmDelEducation" action="<?php echo url_for('pim/saveDeleteEducation?empNumber=' . 
                $empNumber . "&option=delete"); ?>" method="post">
                <?php echo $listForm ?>
            <div id="tblEducation">
                <p id="actionEducation">
                    <?php if ($educationPermissions->canCreate()) { ?>
                    <input type="button" value="<?php echo __("Add"); ?>" class="" id="addEducation" />
                    <?php } ?>
                    <?php if ($educationPermissions->canDelete()) { ?>
                    <input type="button" value="<?php echo __("Delete"); ?>" class="delete" id="delEducation" />
                    <?php } ?>
                </p>                    
                <table width="100%" cellspacing="0" cellpadding="0" class="table tablesorter">
                    <thead>
                        <tr>
                            <?php if ($educationPermissions->canDelete()) { ?>
                            <th class="check" width="2%"><input type="checkbox" id="educationCheckAll" /></th>
                            <?php } ?>
                            <th><?php echo __('Level'); ?></th>
                            <th><?php echo __('Year'); ?></th>
                            <th><?php echo __('GPA/Score'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$haveEducation) { ?>
                        <tr>
                            <?php if ($educationPermissions->canDelete()) { ?>
                            <td class="check"></td>
                            <?php } ?>
                            <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php } else { ?>         
                        <?php
                        $educations = $form->empEducationList;
                        $row = 0;
                        foreach ($educations as $education) :
                            $cssClass = ($row % 2) ? 'even' : 'odd';
                            $startDate = set_datepicker_date_format($education->startDate);
                            $endDate = set_datepicker_date_format($education->endDate);
                            $eduDesc = htmlspecialchars($education->Education->name);
                            ?>
                            <tr class="<?php echo $cssClass; ?>">
                                <td class="check">
                                    <input type="hidden" id="code_<?php echo $education->id; ?>" 
                                           value="<?php echo htmlspecialchars($education->educationId); ?>" />
                                    <input type="hidden" id="id_<?php echo $education->id; ?>" 
                                           value="<?php echo $education->id; ?>" />
                                    <input type="hidden" id="code_desc_<?php echo $education->id; ?>" 
                                           value="<?php echo $eduDesc; ?>" />
                                    <input type="hidden" id="institute_<?php echo $education->id; ?>" 
                                           value="<?php echo htmlspecialchars($education->institute); ?>" />
                                    <input type="hidden" id="major_<?php echo $education->id; ?>" 
                                           value="<?php echo htmlspecialchars($education->major); ?>" />
                                    <input type="hidden" id="year_<?php echo $education->id; ?>" 
                                           value="<?php echo htmlspecialchars($education->year); ?>" />
                                    <input type="hidden" id="gpa_<?php echo $education->id; ?>" 
                                           value="<?php echo htmlspecialchars($education->score); ?>" />
                                    <input type="hidden" id="start_date_<?php echo $education->id; ?>" 
                                           value="<?php echo $startDate; ?>" />
                                    <input type="hidden" id="end_date_<?php echo $education->id; ?>" 
                                           value="<?php echo $endDate; ?>" />
                                    <?php if ($educationPermissions->canDelete()) {?>
                                    <input type="checkbox" class="chkbox" value="<?php echo $education->id; ?>" name="delEdu[]"/>
                                    <?php } else { ?>
                                    <input type="hidden" class="chkbox" value="<?php echo $education->id;?>" name="delEdu[]"/>
                                    <?php } ?>
                                </td>
                                <td class="program">
                                    <?php if ($educationPermissions->canUpdate()) { ?>
                                    <a href="#" class="edit"><?php echo $eduDesc; ?></a>
                                    <?php } else { 
                                        echo $eduDesc;
                                    } ?>
                                </td>
                                <td><?php echo htmlspecialchars($education->year); ?></td>
                                <td><?php echo htmlspecialchars($education->score); ?></td>
                            </tr>
                            <?php
                            $row++;
                        endforeach;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </form>

        <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
        <?php endif; ?>

    </div> <!-- inner -->
        
</div><!-- miniList -->

<script type="text/javascript">
    //<![CDATA[
    var fileModified = 0;
    var lang_addEducation = "<?php echo __('Add Education'); ?>";
    var lang_editEducation = "<?php echo __('Edit Education'); ?>";
    var lang_educationRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_EndDateBeforeSatrtDate = "<?php echo __('End date should be after start date'); ?>";
    var lang_selectEducationToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
    var lang_instituteMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_majorMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_gpaMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 25)); ?>";
    var lang_yearShouldBeNumber = "<?php echo __('Should be a number'); ?>";
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    //]]>
</script>

<script type="text/javascript">
    //<![CDATA[
    var startDate = "";
    $(document).ready(function() {
        
        $('#education_code').after('<span id="static_education_code" style="display:none;"></span>');

        //hide add section
        $("#changeEducation").hide();
        $("#educationRequiredNote").hide();

        //hiding the data table if records are not available
        if($("div#tblEducation .chkbox").length == 0) {
            $('div#tblEducation .check').hide();
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
            removeEditLinks();
            clearMessageBar();
            $('div#changeEducation label.error').hide();
            
            //changing the headings
            $("#headChangeEducation").text(lang_addEducation);
            $("div#tblEducation .chkbox").hide();
            $("#educationCheckAll").hide();

            //hiding action button section
            $("#actionEducation").hide();
            $("#education_id").val("");
            $('#static_education_code').hide().val("");
            $("#education_code").show().val("");
            $("#education_institute").val("");
            $("#education_major").val("");
            $("#education_year").val("");
            $("#education_gpa").val("");
            $("#education_start_date").val(displayDateFormat);
            $("#education_end_date").val(displayDateFormat);

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
            startDate = $('#education_start_date').val();
            $("#frmEducation").submit();
        });
        
        //form validation
        var educationValidator =
            $("#frmEducation").validate({
            rules: {
                'education[code]': {required: true},
                'education[institute]': {required: false, maxlength: 100},
                'education[major]': {required: false, maxlength: 100},
                'education[year]': {required: false, digits: true},
                'education[gpa]': {required: false, maxlength: 25},
                'education[start_date]': {valid_date: function(){return {format: datepickerDateFormat, required:false, displayFormat:displayDateFormat}}},
                'education[end_date]': {valid_date: function(){return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}}, date_range: function() {return {format:datepickerDateFormat, displayFormat:displayDateFormat, fromDate:startDate}}}
            },
            messages: {
                'education[code]': {required: lang_educationRequired},
                'education[institute]': {maxlength: lang_instituteMaxLength},
                'education[major]': {maxlength: lang_majorMaxLength},
                'education[year]': {digits: lang_yearShouldBeNumber},
                'education[gpa]': {maxlength: lang_gpaMaxLength},
                'education[start_date]': {valid_date: lang_invalidDate},
                'education[end_date]': {valid_date: lang_invalidDate, date_range:lang_EndDateBeforeSatrtDate }
            }
        });
    
        function addEditLinks() {
            // called here to avoid double adding links - When in edit mode and cancel is pressed.
            removeEditLinks();
            $('div#tblEducation table tbody td.program').wrapInner('<a class="edit" href="#"/>');
        }

        function removeEditLinks() {
            $('div#tblEducation table tbody td.program a').each(function(index) {
                $(this).parent().text($(this).text());
            });
        }

        $("#btnEducationCancel").click(function() {
            clearMessageBar();
            <?php if ($educationPermissions->canUpdate()){?>
            addEditLinks();
            <?php }?>
            educationValidator.resetForm();
            $('div#changeEducation label.error').hide();
            $("div#tblEducation .chkbox").removeAttr("checked").show();

            //hiding action button section
            $("#actionEducation").show();
            $("#changeEducation").hide();
            $("#educationRequiredNote").hide();
            $("#educationCheckAll").show();

            // remove any options already in use
            $('#static_education_code').hide().val("");

            //remove if disabled while edit
            $('#education_code').removeAttr('disabled');
        });
   
        $('form#frmDelEducation a.edit').live('click', function(event) {
            event.preventDefault();
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
            $('#education_code').val($("#code_" + code).val()).hide();
            $("#education_id").val(code);
            $("#education_institute").val($("#institute_" + code).val());
            $("#education_major").val($("#major_" + code).val());
            $("#education_year").val($("#year_" + code).val());
            $("#education_gpa").val($("#gpa_" + code).val());
            $("#education_start_date").val($("#start_date_" + code).val());
            $("#education_end_date").val($("#end_date_" + code).val());
            if ($("#education_start_date").val() == '') {
                $("#education_start_date").val(displayDateFormat);
            }
            if ($("#education_end_date").val() == '') {
                $("#education_end_date").val(displayDateFormat);
            }
            $("#educationRequiredNote").show();
            $("div#tblEducation .chkbox").hide();
            $("#educationCheckAll").hide();
        });
    });

    //]]>
</script>