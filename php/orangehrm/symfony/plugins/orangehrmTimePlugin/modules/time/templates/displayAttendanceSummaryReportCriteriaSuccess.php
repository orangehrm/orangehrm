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
                    <td><?php echo $form['empName']->renderError() ?><?php echo $form['empName']->render(); ?></td>
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
<div class="paddingLeftRequired">Fields marked with an asterisk <span class="required"> * </span> are required.</div>
<script type="text/javascript">

    var employees = <?php echo str_replace('&quot;', "'", $employeeListAsJson) ?> ;
    var employeesArray = eval(employees);
    var errorMsge;
    var employeeFlag;

    $(document).ready(function() {

        if(<?php echo $lastEmpNumber; ?> == '-1'){
            $("#employee_name").val('All');
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
                $('#validationMsg').attr('class', "messageBalloon_failure");
                $('#validationMsg').html(errorMsge);
                return false;
            }
        });

        //Validation
        $("#attendanceTotalSummaryReportForm").validate({
            rules: {
                'attendanceTotalSummary[fromDate]':{required: true, validFromDateFormat: true},
                'attendanceTotalSummary[toDate]':{required: true, validToDateFormat: true, validToDate: true}
            },
            messages: {
                'attendanceTotalSummary[fromDate]': {
                    required: "From Date is required",
                    validFromDateFormat: "Please enter a date in the format yyyy-mm-dd"
                },
                'attendanceTotalSummary[toDate]': {
                    required: "To Date is required",
                    validToDateFormat: "Please enter a date in the format yyyy-mm-dd",
                    validToDate: " To field should be greater than from field/Invalid date"
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo(element.next().next().next(".errorContainer"));
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


        /* Valid from date format */
        $.validator.addMethod("validFromDateFormat", function(value, element) {
            var dt = value.toString();
            if(dt == "" || dt.toLowerCase() == "yyyy-mm-dd") {
                employeeFlag = validateInput();
                if(employeeFlag){
                    $('#from_date').val("1970-01-01");
                }
                return true;
            }
            dt = dt.split("-");
            return validateDate(parseInt(dt[2], 10), parseInt(dt[1], 10), parseInt(dt[0], 10));
        });

        /* Valid to date format */
        $.validator.addMethod("validToDateFormat", function(value, element) {
            var dt = value.toString();
            if(dt == "" || dt.toLowerCase() == "yyyy-mm-dd") {
                employeeFlag = validateInput();
                if(employeeFlag){
                    var date = new Date();
                    var currentDate = date.getFullYear()+ "-" + ( date.getMonth() + 1 ) + "-" + date.getDate();
                    $('#to_date').val(currentDate);
                }
                return true;
            }
            dt = dt.split("-");
            return validateDate(parseInt(dt[2], 10), parseInt(dt[1], 10), parseInt(dt[0], 10));
        });

        /* Valid From and To date for appropriate combination Date */
        $.validator.addMethod("validToDate", function(value, element) {

            var fromdate    =   $('#from_date').val();
            var fromDateArray = fromdate.split("-");
            //var fromdateObj = new Date(fromdate.replace(/-/g," "));
            var fromdateObj = new Date(fromDateArray[0],fromDateArray[1]-1,fromDateArray[2]);
            
            var todate      =   $('#to_date').val();
            var toDateArray = todate.split("-");
            //var todateObj   =   new Date(todate.replace(/-/g," "));
            var todateObj = new Date(toDateArray[0],toDateArray[1]-1,toDateArray[2]);

            if(fromdateObj > todateObj){
                return false;
            } else {
                return true;
            }

        });
    });

    function validateInput(){
     
        var errorStyle = "background-color:#FFDFDF;";
        var empDateCount = employeesArray.length;
        var temp = false;
        var i;

        if(empDateCount==0){

            errorMsge = "No Employees Available in System";
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
        }else if(empName == "" || empName == $.trim("Type for hints...").toLowerCase()){
            errorMsge = "Please Select an Employee";
            return false;
        }else{
            errorMsge = "Invalid Employee Name";
            return false;
        }
    }
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
