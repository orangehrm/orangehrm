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

            <table  border="0" cellpadding="5" cellspacing="0" class="employeeTable">
                <tr>
                    <td><?php echo __('Employee Name') ?></td>
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
                    <td><?php echo $form['fromDate']->renderError() ?><?php echo $form['fromDate']->render(); ?></td>
                </tr>
                <tr><td><?php echo __('To') ?></td>
                    <td><?php echo $form['toDate']->renderError() ?><?php echo $form['toDate']->render(); ?></td>
                </tr>
            </table>
            <?php echo $form->renderHiddenFields(); ?>
            <div class="formbuttons">
                <td colspan="2"><input type="submit" class="viewbutton" value="<?php echo __('View') ?>"/></td>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">

    var employees = <?php echo str_replace('&quot;', "'", $employeeListAsJson) ?> ;
    var employeesArray = eval(employees);
    var errorMsge;

    $(document).ready(function() {

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
            var projectFlag = validateInput();
            if(!projectFlag) {
                $('#validationMsg').attr('class', "messageBalloon_failure");
                $('#validationMsg').html(errorMsge);
                return false;
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
                temp = true
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