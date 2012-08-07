<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>

<div id="validationMsg" style="margin-left: 16px; width: 470px"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>
<div class="outerbox"  style="width: 500px">
    <div class="maincontent">
        <div class="mainHeading">
            <h2><?php echo __('Attendance Total Summary Report'); ?></h2>
        </div>
        <br class="clear">
        <form action="<?php echo url_for("time/displayAttendanceSummaryReport?reportId=" . $reportId); ?>" id="attendanceTotalSummaryReportForm" method="post">

            <table  border="0" cellpadding="5" cellspacing="0" id="attendanceSummaryReportForm">
                <tr>
                    <td><?php echo __('Employee Name') ?><span class="required">*</span></td>
                    <td><?php echo $form['empName']->render(); ?><div class="errorContainer"></div></td>
                </tr>
                <tr>
                    <td><?php echo __('Job Title') ?></td>
                    <td><?php echo $form['jobTitle']->renderError() ?><?php echo $form['jobTitle']->render(); ?></td>
                </tr>
                <tr>
                    <td><?php echo __('Sub Unit') ?></td>
                    <td><?php echo $form['subUnit']->renderError() ?><?php echo $form['subUnit']->render(); ?></td>
                </tr>
                <tr><td><?php echo __('Employment Status') ?></td>
                    <td><?php echo $form['employeeStatus']->renderError() ?><?php echo $form['employeeStatus']->render(); ?></td>
                </tr>
                <tr><td><?php echo __('From') ?></td>
                    <td><?php echo $form['fromDate']->render(); ?><div class="errorContainer"></div></td>
                </tr>
                <tr><td><?php echo __('To') ?></td>
                    <td><?php echo $form['toDate']->render(); ?><div class="errorContainer"></div></td>
                </tr>
            </table>
            <?php echo $form->renderHiddenFields(); ?>
            <div class="formbuttons">
                <td colspan="2"><input type="submit" id="viewbutton" class="viewbutton" value="<?php echo __('View') ?>"/></td>
            </div>
        </form>
    </div>
</div>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
<script type="text/javascript">

    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_emptyEmployee = '<?php echo __('Select an Employee')?>';
    var lang_required = '<?php echo __(ValidationMessages::REQUIRED);?>';
    var lang_invalid = '<?php echo __(ValidationMessages::INVALID);?>';
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeesArray = eval(employees);
    var errorMsge;
    var employeeFlag;

    $(document).ready(function() {

        if(<?php echo $lastEmpNumber; ?> == '-1'){
            $("#employee_name").val('<?php echo __('All')?>');
            $('#attendanceTotalSummary_employeeId').val('-1');
        }else{
            if ($("#employee_name").val() == '') {
                $("#employee_name").val('<?php echo __("Type for hints") . "..."; ?>').addClass("inputFormatHint");
            }

            $('#viewbutton').click(function() {
                $('#attendanceTotalSummaryReportForm input.inputFormatHint').val('');
                $('#attendanceTotalSummaryReportForm').submit();
            });

            $("#employee_name").one('focus', function() {

                if ($(this).hasClass("inputFormatHint")) {
                    $(this).val("");
                    $(this).removeClass("inputFormatHint");
                }
            });
        }

        $("#employee_name").autocomplete(employees, {

            formatItem: function(item) {

                return item.name;
            }
            ,matchContains:true
        }).result(function(event, item) {
        }
    );

        $('#attendanceTotalSummaryReportForm').submit(function(){
            $('#validationMsg').removeAttr('class');
            $('#validationMsg').html("");
            var employeeFlag = validateInput();
            if(!employeeFlag) {
                if(errorMsge) {
                    $('#validationMsg').attr('class', "messageBalloon_failure");
                    $('#validationMsg').html(errorMsge);
                }
                return false;
            }
        });

        //Validation
        $("#attendanceTotalSummaryReportForm").validate({
            rules: {
                'attendanceTotalSummary[empName]': {
                    required:true,
                    employeeValidation: true
                },
                'attendanceTotalSummary[fromDate]':{
                    valid_date: function() {
                        return {
                            format:datepickerDateFormat,
                            required:false,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'attendanceTotalSummary[toDate]':{
                    valid_date: function() {
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
                            fromDate:$('#from_date').val()
                        }
                    }
                }
            },
            messages: {
                'attendanceTotalSummary[empName]': {
                    required: lang_required,
                    employeeValidation: lang_invalid
                },
                'attendanceTotalSummary[fromDate]': {
                    valid_date: lang_invalidDate
                },
                'attendanceTotalSummary[toDate]': {
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.siblings(".errorContainer"));
            }
        });

 

        if ($("#employee_name").val() == '') {
            $("#employee_name").val('<?php echo __("Type for hints") . "..."; ?>')
            .addClass("inputFormatHint");
        }

        $('#viewbutton').click(function() {
            $('#attendanceTotalSummaryReportForm input.inputFormatHint').val('');
            $('#attendanceTotalSummaryReportForm').submit();
        });

        $("#employee_name").one('focus', function() {

            if ($(this).hasClass("inputFormatHint")) {
                $(this).val("");
                $(this).removeClass("inputFormatHint");
            }
        });
    });

    function validateInput(){
     
        var errorStyle = "background-color:#FFDFDF;";
        var empDateCount = employeesArray.length;
        var temp = false;
        var i;
        errorMsge = null;
        if(empDateCount==0){

            errorMsge = '<?php echo __("No Employees Available");?>';
            return false;
        }
        for (i=0; i < empDateCount; i++) {
            empName = $.trim($('#employee_name').val()).toLowerCase();
            arrayName = employeesArray[i].name.toLowerCase();

            if (empName == arrayName) {
                $('#attendanceTotalSummary_employeeId').val(employeesArray[i].id);
                temp = true;
                break;
            }
        }
        if(temp){
            return true;
        }
    }

    $.validator.addMethod("employeeValidation", function(value, element, params) {
        
        var empCount = employeesArray.length;
        var isValid = false;
        var empName = $('#employee_name').val();
        var inputName = $.trim(empName).toLowerCase();
        if(inputName != ""){
            var i;
            for (i=0; i < empCount; i++) {
                var arrayName = employeesArray[i].name.toLowerCase();
                if (inputName == arrayName) {
                    isValid =  true;
                    break;
                }
            }
        }
        return isValid;
    }); 
</script>
<style type="text/css" media="all">
    label.error{
        padding-left: 0px;
    }

    .paddingLeftRequired{
        font-size: 8pt;
        padding-left: 15px;
        padding-top: 5px;
    }
</style>
