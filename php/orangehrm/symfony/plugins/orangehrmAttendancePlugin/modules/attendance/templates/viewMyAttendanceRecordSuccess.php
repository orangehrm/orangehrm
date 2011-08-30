<?php echo javascript_include_tag('viewMyAttendanceRecordSuccess'); ?>

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
            <h2><?php echo __('My Attendance Records'); ?></h2>
        </div>
        <br class="clear">
        <form action="<?php echo url_for("attendance/viewAttendanceRecord"); ?>" id="reportForm" method="post">

            <table  border="0" cellpadding="5" cellspacing="0" class="employeeTable">

                <tr><td><?php echo __('Date') ?></td>
                    <td><?php echo $form['date']->renderError() ?><?php echo $form['date']->render(); ?>&nbsp;<input id="DateBtn" type="button" name="" value="" class="calendarBtn"style="display: inline;margin:0;float:none; "/></td>
                    <?php echo $form->renderHiddenFields(); ?>
                </tr>
            </table>
        </form>
    </div>
</div>

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


<script type="text/javascript">
    //  dateTimeFormat = YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format;
    var dateFormat        = '<?php echo $sf_user->getDateFormat(); ?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat()); ?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    var errorForInvalidFormat='<?php echo __('Time should be in yyyy-MM-dd HH:mm format'); ?>';
    var errorMsge;
    var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>';
    var employeeId='<?php echo $employeeId; ?>';
    var actionRecorder='<?php echo $actionRecorder; ?>';
    var dateSelected='<?php echo $date; ?>';
    var trigger='<?php echo $trigger; ?>';


</script>