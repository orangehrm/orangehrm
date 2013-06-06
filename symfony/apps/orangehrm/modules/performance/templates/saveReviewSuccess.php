<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>

<?php
$dateFormat = get_datepicker_date_format($sf_user->getDateFormat());  
$displayDateFormat = str_replace('yy', 'yyyy', $dateFormat);
?>
<div class="box">

    <div class="head">
        <h1 id="formHeading">
            <?php echo isset($reviewId) ? __('Edit Performance Review') : __('Add Performance Review'); ?>
        </h1>
    </div>

    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>

        <?php if (isset($noKpiDefined)) { ?>
            <div class = "message warning">
                <?php
                echo __('No Key Performance Indicators were found for the job title of this employee') . " " .
                '<a href="#" id="defineKpi">' . __("Define Now") . '</a>';
                ?>
                <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
            </div>
        <?php } ?>
        <form action="#" id="frmSave" name="frmSave" class="content_inner" method="post">
            <?php // echo $form ?>
            <fieldset>
                <ol>
                    <?php echo $form->render(); ?>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="saveBtn" value="<?php echo __('Save') ?>" tabindex="6" />
                    <input type="button" class="reset" id="resetBtn" 
                        value="<?php if (isset($reviewId)) echo __('Reset'); else echo __('Clear'); ?>" tabindex="7" />
                </p>
            </fieldset>
        </form>
    </div> <!-- inner -->

</div> <!-- Box -->

<script type="text/javascript">
    
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, 
            array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var required_message = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var valid_emp = '<?php echo __(ValidationMessages::INVALID); ?>';
    var dueDateMessage = '<?php echo __("Due date should be after from date"); ?>';
    
    $(document).ready(function() {
        if($('#txtPeriodFromDate-0').val() == ""){
            $('#txtPeriodFromDate-0').val(displayDateFormat)
        }
        if($('#txtPeriodToDate-0').val() == ""){
            $('#txtPeriodToDate-0').val(displayDateFormat)
        }
        if($('#txtDueDate-0').val() == ""){
            $('#txtDueDate-0').val(displayDateFormat)
        }
        
        var empdata = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>;

        $('#processing').html('');
        
        // Save button
        $('#saveBtn').click(function(){
            $('#frmSave').submit();
        });
        
        // Clear button
        $('#resetBtn').click(function(){
            if($("#resetBtn").attr('value') == 'Clear') {
                validator.resetForm();
            } else { //reset part
                validator.resetForm();
            }            
        });    
        
        $.validator.addMethod("validEmployeeName", function(value, element) {                 
            return autoFill('saveReview_employeeName_empName', 'saveReview_employeeName_empId', employees_saveReview_employeeName);
        });
        
        $.validator.addMethod("validReviewerName", function(value, element) {                 
            return autoFill('saveReview_reviewerName_empName', 'saveReview_reviewerName_empId', employees_saveReview_reviewerName);
        });
        
        $("#saveReview_employeeName_empName").result(function(event, item) {
            $(this).valid();
        });
        
        $("#saveReview_reviewerName_empName").result(function(event, item) {
            $(this).valid();
        });        

        function autoFill(selector, filler, data) {
            $("#" + filler).val("");
            var valid = false;
            $.each(data, function(index, item){
                if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                    $("#" + filler).val(item.id);
                    valid = true;
                }
            });
            return valid;
        }

        
            /* Validation */
            var validator = $('#frmSave').validate({
                rules: {
                    'saveReview[employeeName][empName]' : {
                        required: true,
                        validEmployeeName: true,
                        onkeyup: false
                    },
                    'saveReview[reviewerName][empName]' : {
                        required: true,
                        validReviewerName: true,
                        onkeyup: false
                    },
                    'saveReview[from_date]': {  
                        required: true,  
                        valid_date: function() {  
                            return {  
                                required: true,                                  
                                format:datepickerDateFormat,  
                                displayFormat:displayDateFormat  
                            }  
                        }  
                    },  
                    'saveReview[to_date]': {  
                        required: true,  
                        valid_date: function() {  
                            return {  
                                required: true,  
                                format:datepickerDateFormat,  
                                displayFormat:displayDateFormat  
                            }  
                        },  
                        date_range: function() {  
                            return {  
                                format:datepickerDateFormat,  
                                displayFormat:displayDateFormat,  
                                fromDate:$("#date_from").val()  
                            }  
                        }  
                    },
                    'saveReview[dueDate]': {
                        required: true,  
                        valid_date: function() {  
                            return {  
                                required: true,                                  
                                format:datepickerDateFormat,  
                                displayFormat:displayDateFormat  
                            }  
                        },
                        date_range: function() {  
                            return {  
                                format:datepickerDateFormat,  
                                displayFormat:displayDateFormat,  
                                fromDate:$("#date_from").val()  
                            }  
                        }
                    }
                },  
                messages: {
                    'saveReview[employeeName][empName]' : {
                        required: required_message,
                        validEmployeeName: valid_emp
                    },
                    'saveReview[reviewerName][empName]' : {
                        required: required_message,
                        validReviewerName: valid_emp
                    },
                    'saveReview[from_date]':{  
                        required: required_message,  
                        valid_date: lang_invalidDate  
                    },  
                    'saveReview[to_date]':{  
                        required: required_message,  
                        valid_date: lang_invalidDate,  
                        date_range: lang_dateError  
                    },
                    'saveReview[dueDate]':{  
                        required: required_message,  
                        valid_date: lang_invalidDate,  
                        date_range: dueDateMessage
                    }  
                } 
            });
            
            // defineKpi link click
            $('#defineKpi').click(function(){
                location.href = "<?php echo url_for('performance/saveKpi'); ?>";
            });
    }); // ready():Ends
</script>