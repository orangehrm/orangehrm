<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>'
    var lang_startDateAfterEndDate = "<?php echo __('End date should be after start date'); ?>";
    var lang_View_Details =  "<?php echo __('View Details'); ?>";
    var lang_Hide_Details =  "<?php echo __('Hide Details'); ?>";
    var lang_max_char_terminated_reason =  "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var  lang_max_char_terminated_note =  "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_terminatedReasonRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_activateEmployement = "<?php echo __("Activate Employment"); ?>";
    var lang_terminateEmployement = "<?php echo __("Terminate Employment"); ?>";
    var lang_editTerminateEmployement = "<?php echo __("Edit Employment Termination"); ?>";
    var activateEmployementUrl = '<?php echo url_for('pim/activateEmployement?empNumber=' . $empNumber); ?>';

    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var fileModified = 0;

    //]]>
</script>


<div class="box pimPane" id="">
    
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber, 'form' => $form));?>
    
    <div class="">
        <div class="head">
            <h1><?php echo __('Job'); ?></h1>
        </div> <!-- head -->
        
        <div class="inner">
            <?php if ($allowTerminate || $allowActivate || $jobInformationPermission->canRead()) : ?>
            
            <?php include_partial('global/flash_messages', array('prefix' => 'jobdetails')); ?>
            
            <form id="frmEmpJobDetails" method="post" enctype="multipart/form-data" action="<?php echo url_for('pim/viewJobDetails'); ?>">
                <fieldset>
                    <?php if ($jobInformationPermission->canRead()) : ?>
                    <?php echo $form['_csrf_token']; ?>
                    <?php echo $form['emp_number']->render(); ?>
                    <ol>
                        <li>
                            <?php echo $form['job_title']->renderLabel(__('Job Title')); ?>
                            <?php echo $form['job_title']->render(array("class" => "formSelect")); ?>    
                        </li>
                        <li>
                            <label><?php echo __("Job Specification"); ?></label>
                            <?php
                            $specAttachment = $form->jobSpecAttachment;
                            $specId = (!empty($specAttachment)) ? $specAttachment->getId() : "";
                            if (!empty($specId)) {
                                $linkHtml = "<span id=\"fileLink\"><a target=\"_blank\" class=\"fileLink\" href=\"";
                                $linkHtml .= url_for('admin/viewJobSpec?attachId=' . $specId);
                                $linkHtml .= "\">{$specAttachment->getFileName()}</a></span>";
                                echo $linkHtml;
                            } else {
                                echo "<span id=\"fileLink\"><label id=\"notDefinedLabel\">" . __('Not Defined') . "</label></span>";
                            }
                            ?>
                        </li>
                        <li>
                            <?php echo $form['emp_status']->renderLabel(__('Employment Status')); ?>
                            <?php echo $form['emp_status']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li>
                            <?php echo $form['eeo_category']->renderLabel(__('Job Category')); ?>
                            <?php echo $form['eeo_category']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li>
                            <?php echo $form['joined_date']->renderLabel(__('Joined Date')); ?>
                            <?php echo $form['joined_date']->render(array("class" => "formDateInput")); ?>
                        </li>
                        <li>
                            <?php echo $form['sub_unit']->renderLabel(__('Sub Unit')); ?>
                            <?php echo $form['sub_unit']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li>
                            <?php echo $form['location']->renderLabel(__('Location')); ?>
                            <?php echo $form['location']->render(array("class" => "formSelect")); ?>
                        </li>
                        <li>
                            <h2><?php echo __('Employment Contract'); ?></h2>
                        </li>
                        <li>
                            <?php echo $form['contract_start_date']->renderLabel(__('Start Date')); ?>
                            <?php echo $form['contract_start_date']->render(array("class" => "formDateInput")); ?>
                        </li>
                        <li>
                            <?php echo $form['contract_end_date']->renderLabel(__('End Date')); ?>
                            <?php echo $form['contract_end_date']->render(array("class" => "formDateInput")); ?>
                        </li>
                            <?php
                            if (empty($form->attachment)) {
                                echo "<li class=\"contractEdidMode\">";
                                echo $form['contract_file']->renderLabel('Contract Details');
                                echo $form['contract_file']->render();
                                echo "<label class=\"fieldHelpBottom\">" . __(CommonMessages::FILE_LABEL_SIZE) . "</label>";
                                echo "</li>";
                            } else {
                                $attachment = $form->attachment;
                                $linkHtml = "<a title=\"{$attachment->description}\" target=\"_blank\" class=\"fileLink\" href=\"";
                                $linkHtml .= url_for('pim/viewAttachment?empNumber=' . $empNumber . '&attachId=' . $attachment->attach_id);
                                $linkHtml .= "\">{$attachment->filename}</a>";
                                echo "<li class=\"contractEdidMode\">";
                                echo $form['contract_update']->renderLabel(__('Contract Details'));
                                echo $linkHtml;
                                echo "</li>";
                                
                                echo "<li id=\"radio\" class=\"radio noLabel contractEdidMode\">";
                                echo $form['contract_update']->render();
                                echo "</li>";
                                
                                echo "<li id=\"fileUploadSection\" class=\"noLabel\">";
                                    echo $form['contract_file']->renderLabel(' ');
                                    echo $form['contract_file']->render();
                                    echo "<label class=\"fieldHelpBottom\">" . __(CommonMessages::FILE_LABEL_SIZE) . "</label>";
                                echo "</li>";
                            }
                            ?>
                        <li class="contractReadMode">
                            <?php
                            echo "<label>" . __('Contract Details') . "</label>";
                            if (empty($form->attachment)) {
                                echo "<label id=\"notDefinedLabel\">" . __('Not Defined') . "</label>";
                            } else {
                                echo $linkHtml;
                            }
                            ?>
                        </li>
                    </ol>
                    <?php endif; ?>
                    
                    <p>
                        <?php if ($jobInformationPermission->canUpdate()) : ?>
                        <input type="button" class="" id="btnSave" value="<?php echo __("Edit"); ?>" />
                        <?php endif; ?>  
                        <?php
                        $empTermination = $form->empTermination;
                        $allowed = FALSE;
                        if (!empty($empTermination)) {
                            $allowed = $allowActivate;
                            $terminatedId = $empTermination->getId();
                            $btnTitle = __("Activate Employment");
                            $label = __("Terminated on") . " : " . set_datepicker_date_format($empTermination->getDate());
                        } else {
                            $allowed = $allowTerminate;
                            $btnTitle = __("Terminate Employment");
                        }
                        ?>
                        <?php if ($allowed) { ?>
                        <input type="button" class="reset" id="btnTerminateEmployement" value="<?php echo $btnTitle; ?>" />
                        <a id="terminateModal" class="btn2" data-toggle="modal" href="#terminateEmployement" target="_blank"></a>
                        <?php } ?>
                        <?php if ($allowActivate) { ?>
                            <label id="terminatedDate">
                                <a class="btn2" data-toggle="modal" href="#terminateEmployement" ><?php echo $label; ?></a>
                            </label>      
                        <?php } else {
                            if ($jobInformationPermission->canRead()) {
                            ?>
                            <label id="terminatedDate">
                                <a class="btn2" data-toggle="modal" href="#terminateEmployement" ><?php echo $label; ?></a>
                            </label>      
                        <?php 
                            }
                        }
                        ?>
                    </p>
                </fieldset>
            </form>
            
            <?php else : ?>
            <div><?php echo __(CommonMessages::RESTRICTED_SECTION); ?></div>
            <?php endif; ?>
        </div> <!-- inner -->
    </div>
    
    <?php 
    echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => CustomField::SCREEN_JOB)); 
    echo include_component('pim', 'attachments', array('empNumber' => $empNumber, 'screen' => EmployeeAttachment::SCREEN_JOB)); 
    ?>
    
</div> <!-- Box -->

<!-- Terminate Employment box HTML: Begins -->
<?php if ($allowTerminate || $allowActivate || $jobInformationPermission->canRead()) { ?>
<div class="modal hide" id="terminateEmployement">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Terminate Employment') ?></h3>
    </div>
    <div class="modal-body">
        <form id="frmTerminateEmployement" method="post" 
              action="<?php echo url_for('pim/terminateEmployement?empNumber=' . $empNumber.'&terminatedId='.$terminatedId); ?>">
            <?php echo $employeeTerminateForm['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $employeeTerminateForm['reason']->renderLabel(__('Reason') . ' <em>*</em>'); ?>
                        <?php echo $employeeTerminateForm['reason']->render(array("class" => "formSelect")); ?>
                    </li>
                    <li>
                        <?php echo $employeeTerminateForm['date']->renderLabel(__('Date') . ' <em>*</em>'); ?>
                        <?php echo $employeeTerminateForm['date']->render(array("class" => "formDateInput")); ?>
                    </li>
                    <li class="largeTextBox">
                        <?php echo $employeeTerminateForm['note']->renderLabel(__('Note')); ?>
                        <?php echo $employeeTerminateForm['note']->render(array("class" => "formTxtArea")); ?>
                    </li>
                    <span id="errorHolder"></span>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <?php if ($allowTerminate || $allowActivate) { ?>
        <input type="button" id="dialogConfirm" class="btn" value="<?php echo __('Confirm'); ?>" />
        <?php } ?>
        <input type="button"  id="dialogCancel" name="dialogCancel" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<form id="activeEmploymentForm" name="activeEmploymentForm" method="post" action="<?php echo url_for('pim/activateEmployement?empNumber=' . $empNumber); ?>">
    <?php echo $activeEmploymentForm ?>
</form>
<?php } ?>
<!-- Terminate Employment box HTML: Ends -->

<script type="text/javascript">
    //<![CDATA[
    
    var firstPart = '<?php echo url_for('admin/viewJobSpec?attachId='); ?>';
    var notDefinedLabel = '<?php echo __('Not Defined'); ?>';
    
    var stratDate = "";
    
    function showHideViewDetailsLink() {
        
        if ($("#job_spec_desc").val() != '' || $("#job_spec_duties").val() != '') {
            $('#viewDetailsLink').show();
        } else {
            $('#viewDetailsLink').hide();
        }
    }
    
    function clearErrors() {
        $('#frmTerminateEmployement').validate().resetForm();
    }
    
    $(document).ready(function() {
        
        $('#btnTerminateEmployement').click(function(){
            if($(this).val() == lang_terminateEmployement){
                clearErrors();
                $('#terminate_date_Button').removeAttr('disabled')
               $('#terminateModal').click();
            }
            else{
                $('#activeEmploymentForm').submit();
                //window.location.replace(activateEmployementUrl);
            }
        })
        
        $('#dialogConfirm').click(function(){
            if($('#terminate_date').val() == datepickerDateFormat){
                $('#terminate_date').val("")
            }
            if($('#frmTerminateEmployement').valid()){
                $('#frmTerminateEmployement').submit()
            }
        });
        
        $('#dialogCancel').click(function(){
            clearErrors();
        });
        
        $("#job_job_title").change(function() {
            
            var jobTitle = this.options[this.selectedIndex].value;
            
            if(jobTitle != ""){
                
                var specUrl = '<?php echo url_for('admin/getJobSpecificationJson?jobTitleId='); ?>' + jobTitle;
                
                $.getJSON(specUrl, function(data) {
                    
                    var specId = "";
                    var fileName = "";
                    
                    if (data) {
                        specId = (data.specId != null) ? data.specId : specId;
                        fileName = data.fileName;
                    }
                    
                    if(specId != ""){
                        $('#fileLink').html("<a target=\"_blank\" class=\"fileLink\" href=\""+ firstPart + specId + "\">"+fileName+ "</a>")
                    }else{
                        $('#fileLink').html("<label id=\"notDefinedLabel\">"+notDefinedLabel+"</label>")
                    }
                    
                });}
        });
        
        /* Form validation */
        $("#frmEmpJobDetails").validate({
            rules: {
                'job[terminated_date]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat} } },
                'job[termination_reason]': { maxlength: 250 },
                'job[joined_date]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat} } },
                'job[contract_start_date]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat}}},
                'job[contract_end_date]': { required: false, valid_date: function(){ return {format:datepickerDateFormat, required:false, displayFormat:displayDateFormat} }, date_range: function() {return {format:datepickerDateFormat, displayFormat:displayDateFormat, fromDate:stratDate}}}
            },
            messages: {
                'job[terminated_date]': { valid_date: lang_invalidDate },
                'job[termination_reason]': { maxlength: lang_max_char_terminated_reason },
                'job[joined_date]': { valid_date: lang_invalidDate },
                'job[contract_start_date]': { valid_date: lang_invalidDate},
                'job[contract_end_date]': { valid_date: lang_invalidDate, date_range:lang_startDateAfterEndDate}
            }
        });
        
        $("#frmTerminateEmployement").validate({
            rules: {
                'terminate[reason]': { required: true },
                'terminate[note]': { maxlength: 250 },
                'terminate[date]': { required: true, valid_date: function(){ return {format:datepickerDateFormat, displayFormat:displayDateFormat} } }
            },
            messages: {
                'terminate[reason]': { required: lang_terminatedReasonRequired },
                'terminate[note]': { maxlength: lang_max_char_terminated_note },
                'terminate[date]': { required: lang_invalidDate, valid_date: lang_invalidDate }
            }
        });
        
        
        
        var readonlyFlag = 0;
        <?php if (!$jobInformationPermission->canUpdate()) { ?>
            readonlyFlag = 1;
        <?php } ?>
        
        var list = new Array(
        '#job_job_title', '#job_emp_status', '#job_terminated_date', 
        '.calendarBtn', '#job_termination_reason', '#job_eeo_category',
        '#job_joined_date', '#job_sub_unit', '#job_location',
        '#contract_file', 'ul.radio_list input',
        '#job_contract_start_date', '#job_contract_end_date',
        '#job_contract_file');
        for(i=0; i < list.length; i++) {
            $(list[i]).attr("disabled", "disabled");
        }
        
        $('#job_joined_date,#job_contract_start_date,#job_contract_end_date').datepicker('disable');
        
        <?php if (empty($form->attachment)) { ?>
            $('#job_contract_update_3').attr('checked', 'checked');
        <?php } ?>
        
        $('#fileUploadSection').hide();
        
        $("#job_contract_update_3").click(function () {
            $('#fileUploadSection').show();
        });
        $("#job_contract_update_2").click(function () {
            $('#fileUploadSection').hide();
        });
        $("#job_contract_update_1").click(function () {
            $('#fileUploadSection').hide();
        });
                
        $('.contractEdidMode').hide();
        
        $("#btnSave").click(function() {
            
            $('.contractEdidMode').show();
            $('.contractReadMode').hide();
            
            if ( !readonlyFlag) {  
                //if user clicks on Edit make all fields editable                                     
                if($("#btnSave").attr('value') == edit) {
                    for(i=0; i < list.length; i++) {
                        $(list[i]).removeAttr("disabled");
                    }
                    
                    $('#job_joined_date,#job_contract_start_date,#job_contract_end_date').datepicker('enable');
                    
                    $("#btnSave").attr('value', save);
                    
                    <?php if (empty($form->attachment)) { ?>
                        $('#job_contract_update_1').attr('disabled', 'disabled');
                        $('#job_contract_update_2').attr('disabled', 'disabled');
                        $('#job_contract_update_3').attr('checked', 'checked');
                    <?php } ?>
                    
                    return;
                }
                
                if($("#btnSave").attr('value') == save) {
                    
                    
                    if ($('#job_emp_status').val() != 'EST000') {
                        $('#job_terminated_date').val('');
                        $('#job_termination_reason').val('');
                    }
                    stratDate = $('#job_contract_start_date').val();
                    $("#frmEmpJobDetails").submit();
                }
            }
        });
        
        $('a#viewDetailsLink').click(function() {
            var linkText = $('div#job_spec_details').is(':visible') ? lang_View_Details: lang_Hide_Details;
            $(this).text(linkText);
            
            $('div#job_spec_details').toggle();
        });

        /* Hiding showing viewDetailsLink at loading */
        showHideViewDetailsLink();
        
        /*
         * Ajax call to fetch job specification for selected job
         */
        $("#job_job_title").change(function() {
            
            var jobTitle = this.options[this.selectedIndex].value;
            
            // don't check if not selected
            if (jobTitle == '0' || jobTitle == '') {
                $("#viewDetailsLink").hide();
                $('a#viewDetailsLink').text(lang_View_Details);
                return;
            }
            
        });
        
    });
    
    //]]>
</script>

