
<?php echo stylesheet_tag('../orangehrmAttendancePlugin/css/proxyPunchInOutSuccess'); ?>
<?php echo javascript_include_tag('proxyPunchInPunchOutSuccess'); ?>

<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<div id="validationMsg"   style="margin-left: 16px; width: 470px"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>

<div class="outerbox"  style="width: 500px">
    <div class="maincontent">
        <div id="punchInOutForm">

            <?php if (($action['PunchIn'])): ?>

                <div class="mainHeading">
                    <h2><?php echo __('Punch In'); ?></h2>
                </div>
            <?php endif; ?>

            <?php if ($action['PunchOut']): ?>
                <div class="mainHeading">
                    <h2><?php echo __('Punch Out'); ?></h2>
                </div>
            <?php endif; ?>

            <br class="clear">
            <form  id="punchTimeForm" method="post">
                <table class="punchTable" border="0" cellpadding="5" cellspacing="0">
                    <tbody>
                        <?php echo $form['_csrf_token']; ?>

                        <tr>
                            <td><?php echo $form['date']->renderLabel() ?></td>
                            <td> <?php echo $form['date']->renderError() ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['date']->render(); ?>&nbsp;<input id="DateBtn" type="button" name="" value="" class="calendarBtn"style="display: inline;margin:0;float:none; "/></td></td></tr>
                        <tr><td> <?php echo $form['time']->renderLabel() ?></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['time']->renderError() ?><?php echo $form['time']->render(); ?><span class="timeFormatHint">HH:MM</span></td></tr>
                        <tr><td> <?php echo $form['timezone']->renderLabel() ?></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['timezone']->renderError() ?><?php echo $form['timezone']->render(); ?></td></tr>
                        <tr><td style="vertical-align: top" > <?php echo $form['note']->renderLabel() ?></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['note']->renderError() ?><?php echo $form['note']->render(); ?></td></tr>


                        <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, $sf_data->getRaw('allowedActions'))) : ?>
                            <tr> <td></td> <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="punchInbutton" name="button" id="btnPunch"
                                                                                           onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                                                                                           value="<?php echo __('In'); ?>" /></td></tr>
                            <?php endif; ?>

                        <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT, $sf_data->getRaw('allowedActions'))) : ?>
                            <tr><td></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="punchOutbutton" name="button" id="btnPunch"
                                                                                         onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                                                                         value="<?php echo __('Out'); ?>" /></td></tr>
                            <?php endif; ?>
                    </tbody>
                </table>
            </form>

            <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT, $sf_data->getRaw('allowedActions'))) : ?>
                <div>&nbsp; <?php echo __("Last punch in time: "); ?><?php echo $punchInTime; ?>&nbsp;<?php echo $punchInNote; ?></div>
            <?php endif; ?><br class="clear">          




        </div> 
        <br class="clear">
    </div>
</div>


<script type="text/javascript">
    //<![CDATA[
    //  dateTimeFormat = YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format;
    var dateFormat        = '<?php echo $sf_user->getDateFormat(); ?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat()); ?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    var employeeId='<?php echo $employeeId; ?>';
    var selectedDate='<?php echo $date; ?>';
    var currentTime='<?php echo $currentTime; ?>';
    var currentDate='<?php echo $currentDate; ?>';
    var linkForProxyPunchAction='<?php echo url_for('attendance/proxyPunchInPunchOut') ?>';
    var linkForOverLappingValidation='<?php echo url_for('attendance/validatePunchOutOverLapping') ?>';
    var linkForPunchInOverlappingValidation='<?php echo url_for('attendance/validatePunchInOverLapping') ?>';
    var errorForInvalidTime='<?php echo __('Punch out time should be higher than the punch in time'); ?>';
    var errorForInvalidFormat='<?php echo __('Time should be in yyyy-MM-dd HH:mm format'); ?>';
    var errorForInvalidTimeFormat='<?php echo __('Invalid Time') ?>';
    var getCurrentTimeLink='<?php echo url_for('attendance/getCurrentTime') ?>';
    var errorForInvalidDateFormat='<?php echo __('Invalid Date') ?>';
    var errorForOverLappingTime='<?php echo __('Overlapping records found'); ?>';
    var errorForInvalidNote='<?php echo __('Invalid note') ?>';
    var actionRecorder='<?php echo $actionRecorder; ?>';
   

    var punchOut =false;
    punchOut='<?php echo $action['PunchOut'] ?>'

  
    var punchInTime='<?php echo $punchInTime; ?>';
    var punchInNote='<?php echo json_encode($punchInNote); ?>';
    var punchInUtcTime='<?php echo $punchInUtcTime; ?>';       
  
   
    
    
    
</script>