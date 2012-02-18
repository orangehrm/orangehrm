<?php echo javascript_include_tag('../orangehrmAttendancePlugin/js/viewAttendanceRecordSuccess'); ?>


<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js') ?>"></script>

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
            <h2><?php echo __('View Attendance Record'); ?></h2>
        </div>
        <br class="clear">
        <form action="<?php echo url_for("attendance/viewAttendanceRecord"); ?>" id="reportForm" method="post">

            <table  border="0" cellpadding="5" cellspacing="0" class="employeeTable">
                <tr>
                    <td><?php echo __('Employee Name').' <span class="required">*</span>'?></td>
                    <td><?php echo $form['employeeName']->renderError() ?><?php echo $form['employeeName']->render(); ?></td>
                </tr>
                <tr><td><?php echo __('Date').' <span class="required">*</span>' ?></td>
                    <td><?php echo $form['date']->renderError() ?><?php echo $form['date']->render(); ?>&nbsp;<input id="DateBtn" type="button" name="" value="" class="calendarBtn"style="display: inline;margin:0;float:none; "/></td>
                    <?php echo $form->renderHiddenFields(); ?>
                </tr>
            </table>
        </form>
    </div>
</div>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
<div id="recordsTable">

    <br class="clear">
    <div id="msg" ><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>

    <div class="outerbox" >
        <div class="maincontent">
            <div id="recordsTable1">
            </div> 
            <br class="clear">
        </div>
    </div>

</div>
<br class="clear">
<div id="punchInOut">

    <br class="clear">


</div>




<script type="text/javascript">
    
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var errorForInvalidFormat='<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>';
    var employees = <?php echo str_replace('&quot;', "'", $employeeListAsJson) ?> ;
    var employeesArray = eval(employees);
    var errorMsge;
    var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>'
    var linkForProxyPunchInOut='<?php echo url_for('attendance/proxyPunchInPunchOut'); ?>'
    var trigger='<?php echo $trigger; ?>';
    var employeeId='<?php echo $employeeId; ?>';
    var dateSelected='<?php echo $date; ?>';
    var actionRecorder='<?php echo $actionRecorder; ?>';
    var employeeSelect = '<?php echo __('Select an Employee')?>';
    var invalidEmpName = '<?php echo __('Invalid Employee Name')?>';
    var noEmployees = '<?php echo __('No Employees Available')?>';
    var typeForHints = '<?php echo __("Type for hints").'...';?>';

</script>