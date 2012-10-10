<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css') ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<div id="content">

    <div id="contentContainer">

        <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
        <div id="processing"></div>
        <div class="outerbox">

            <div id="formHeading" class="mainHeading"><h2><?php echo isset($clues['id']) ? __('Edit Performance Review') : __('Add Performance Review'); ?></h2></div>

            <form action="#" id="frmSave" name="frmSave" class="content_inner" method="post">

                <?php echo $form['_csrf_token']; ?>

                <div id="formWrapper">

                    <label for="txtEmpName-0"><?php echo __('Employee Name') ?> <span class="required">*</span></label>
                    <input id="txtEmpName-0" name="txtEmpName-0" type="text" class="formInputText"
                           value="<?php echo isset($clues['empName']) ? $clues['empName'] : __('Type for hints') . '...' ?>" tabindex="1" <?php if (isset($clues['id'])) { ?>style="display:none;"<?php } ?> />
                           <?php if (isset($clues['id'])) {
                               ?>
                        <label style="width:auto;"><?php echo $clues['empName']; ?></label>
                    <?php } ?>
                    <input type="text" name="hdnEmpId-0" id="hdnEmpId-0"
                           value="<?php echo isset($clues['empId']) ? $clues['empId'] : '0' ?>" style="display:none; "/>
                    <div class="errorDiv"></div>
                    <br class="clear"/>

                    <label for="txtReviewerName-0"><?php echo __('Reviewer Name') ?> <span class="required">*</span></label>
                    <input id="txtReviewerName-0" name="txtReviewerName-0" type="text" class="formInputText"
                           value="<?php echo isset($clues['reviewerName']) ? $clues['reviewerName'] : __('Type for hints') . '...' ?>" tabindex="2" />
                    <input type="text" name="hdnReviewerId-0" id="hdnReviewerId-0"
                           value="<?php echo isset($clues['reviewerId']) ? $clues['reviewerId'] : '0' ?>" style="display:none;" />
                    <div class="errorDiv"></div>
                    <br class="clear"/>

                    <label for="txtPeriodFromDate-0"><?php echo __('From') ?> <span class="required">*</span></label>
                    <input id="txtPeriodFromDate-0" name="txtPeriodFromDate-0" type="text" class="formInputText"
                           value="<?php echo isset($clues['from']) ? set_datepicker_date_format($clues['from']) : ''; ?>" tabindex="3" />
                    <input id="fromButton" name="fromButton" class="calendarBtn" type="button" value="   " />
                    <div class="errorDiv"></div>
                    <br class="clear"/>

                    <label for="txtPeriodToDate-0"><?php echo __('To') ?> <span class="required">*</span></label>
                    <input id="txtPeriodToDate-0" name="txtPeriodToDate-0" type="text" class="formInputText"
                           value="<?php echo isset($clues['to']) ? set_datepicker_date_format($clues['to']) : ''; ?>" tabindex="4" />
                    <input id="toButton" name="toButton" class="calendarBtn" type="button" value="   " />
                    <div class="errorDiv"></div>
                    <br class="clear"/>

                    <label for="txtDueDate-0"><?php echo __('Due Date') ?> <span class="required">*</span></label>
                    <input id="txtDueDate-0" name="txtDueDate-0" type="text" class="formInputText"
                           value="<?php echo isset($clues['due']) ? set_datepicker_date_format($clues['due']) : ''; ?>" tabindex="5" />
                    <input id="dueButton" name="dueButton" class="calendarBtn" type="button" value="   " />
                    <div class="errorDiv"></div>
                    <br class="clear"/>

                    <input type="hidden" name="hdnId-0" id="hdnId-0"
                           value="<?php echo isset($clues['id']) ? $clues['id'] : '' ?>">

                </div>

                <div id="buttonWrapper" class="formbuttons">
                    <input type="button" class="savebutton" id="saveBtn" value="<?php echo __('Save') ?>" tabindex="6" />

                    <input type="button" class="savebutton" id="resetBtn" value="<?php
                    if (isset($clues['id'])) {
                        echo __('Reset');
                    } else {
                        echo __('Clear');
                    }
                    ?>" tabindex="7" />

                </div>

            </form>

        </div> <!-- outerbox: Ends -->
        <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
    </div> <!-- contentContainer: Ends -->

</div> <!-- content: Ends -->

<script type="text/javascript">

    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';

    function autoFill(selector, filler, data) {
        jQuery.each(data, function(index, item){
            if(item.name == $("#" + selector).val()) {
                $("#" + filler).val(item.id);
                return true;
            }
        });
    }

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

        /* Auto completion of employees */
        $("#txtEmpName-0").autocomplete(empdata, {
            formatItem: function(item) {
                return item.name;
            }, matchContains:"word"
        }).result(function(event, item) {
            $('#hdnEmpId-0').val(item.id);
        });

        /* Auto completion of reviewers */
        $("#txtReviewerName-0").autocomplete(empdata, {
            formatItem: function(item) {
                return item.name;
            }, matchContains:"word"
        }).result(function(event, item) {
            $('#hdnReviewerId-0').val(item.id);
        });

        $("#txtEmpName-0").change(function(){
            autoFill('txtEmpName-0', 'hdnEmpId-0', empdata);
        });

        $("#txtReviewerName-0").change(function(){
            autoFill('txtReviewerName-0', 'hdnReviewerId-0', empdata);
        });
        /* Clearing auto-fill fields */
        $("#txtEmpName-0").click(function(){ $(this).attr({ value: '' }); $("#hdnEmpId-0").attr({ value: '0' }); });
        $("#txtReviewerName-0").click(function(){ $(this).attr({ value: '' }); $("#hdnReviewerId-0").attr({ value: '0' }); });

        /* Date picker */

        $("#txtPeriodFromDate-0").datepicker({ dateFormat: datepickerDateFormat, changeMonth: true, changeYear: true});
        $('#fromButton').click(function(){
            $("#txtPeriodFromDate-0").datepicker('show');
        });

        $("#txtPeriodToDate-0").datepicker({ dateFormat: datepickerDateFormat, changeMonth: true, changeYear: true});
        $('#toButton').click(function(){
            $("#txtPeriodToDate-0").datepicker('show');
        });

        $("#txtDueDate-0").datepicker({ dateFormat: datepickerDateFormat, changeMonth: true, changeYear: true});
        $('#dueButton').click(function(){
            $("#txtDueDate-0").datepicker('show');
        });

        $('#processing').html('');

        // Save button
        $('#saveBtn').click(function(){
            $('#processing').html('');
            $('#messageBalloon_success').remove();
            $('#messageBalloon_warning').remove();
            
            var autoFields = new Array("txtEmpName-0", "txtReviewerName-0");
            var autoHidden = new Array("hdnEmpId-0", "hdnReviewerId-0");

            for(x=0; x < autoFields.length; x++) {
                $("#" + autoHidden[x]).val(0);
                for(i=0; i < empdata.length; i++) {
                    var data = empdata[i];
                    if($("#" + autoFields[x]).val() == data.name) {
                        $("#" + autoHidden[x]).val(data.id);
                        break;
                    }
                }
            }
            if($('#txtPeriodFromDate-0').val() == displayDateFormat){
                $('#txtPeriodFromDate-0').val("")
            }
            if($('#txtPeriodToDate-0').val() == displayDateFormat){
                $('#txtPeriodToDate-0').val("")
            }
            if($('#txtDueDate-0').val() == displayDateFormat){
                $('#txtDueDate-0').val("")
            }
                        
            if($('#frmSave').valid()) {
                $('#processing').html('<div class="messageBalloon_success">'+"<?php echo __('Processing') ;?>"+'...</div>');
            }            
            
            $('#frmSave').submit();
        });

        // Clear button
        $('#resetBtn').click(function(){
            $("label.error").each(function(i){
                $(this).remove();
            });
            document.forms[0].reset('');
            autoFill('txtEmpName-0', 'hdnEmpId-0', empdata);
            autoFill('txtReviewerName-0', 'hdnReviewerId-0', empdata);
        });

        /* Validation */
        var validator = $("#frmSave").validate({

            rules: {
                'txtEmpName-0': { required: true, empIdSet: true },
                'txtReviewerName-0': { required: true, reviewerIdSet: true },
                'txtPeriodFromDate-0': {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'txtPeriodToDate-0': {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    },
                    date_range: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat,
                            fromDate:$('#txtPeriodFromDate-0').val()
                        }
                    }
                },
                'txtDueDate-0': {
                    required: true,
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    },
                    date_range: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat,
                            fromDate:$('#txtPeriodFromDate-0').val()
                        }
                    }
                }
            },
            messages: {
                'txtEmpName-0':{
                    required: '<?php echo __(ValidationMessages::REQUIRED); ?>',
                    empIdSet:'<?php echo __(ValidationMessages::INVALID) ?>'
                },
                'txtReviewerName-0':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                    reviewerIdSet:'<?php echo __(ValidationMessages::INVALID) ?>'
                },
                'txtPeriodFromDate-0':{
                    required: '<?php echo __(ValidationMessages::REQUIRED) ?>',
                    valid_date: lang_invalidDate

                },
                'txtPeriodToDate-0':{
                    required: '<?php echo __(ValidationMessages::REQUIRED) ?>',
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                },
                'txtDueDate-0':{
                    required: '<?php echo __(ValidationMessages::REQUIRED) ?>',
                    valid_date: lang_invalidDate ,
                    date_range: '<?php echo __("Due date should be after from date"); ?>'
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.next().next());
            }

        });

        /* Checks whether Employee is set */
        $.validator.addMethod("empIdSet", function(value, element) {
            if ($('#hdnEmpId-0').val() == 0) {
                return false;
            } else {
                return true;
            }
        });

        /* Checks whether Reviewer is set */
        $.validator.addMethod("reviewerIdSet", function(value, element) {
            if ($('#hdnReviewerId-0').val() == 0) {
                return false;
            } else {
                return true;
            }
        });

    }); // ready():Ends

    /* Applying rounding box style */
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
</script>