
<?php echo stylesheet_tag('../orangehrmAttendancePlugin/css/proxyPunchInOutSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmAttendancePlugin/js/proxyPunchInPunchOutSuccess'); ?>

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
                            <td> <?php echo $form['date']->renderError() ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['date']->render();?></td></tr>
                        <tr><td> <?php echo $form['time']->renderLabel() ?></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['time']->renderError() ?><?php echo $form['time']->render(); ?><span class="timeFormatHint">HH:MM</span></td></tr>
                        <tr><td> <?php echo $form['timezone']->renderLabel() ?></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['timezone']->renderError() ?><?php echo $form['timezone']->render(); ?></td></tr>
                        <tr><td style="vertical-align: top" > <?php echo $form['note']->renderLabel() ?></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form['note']->renderError() ?><?php echo $form['note']->render(array("onkeyup" => "validateNote()")); ?></td></tr>


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
            <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT, $sf_data->getRaw('allowedActions'))) : ?>
            <?php $dateArray = explode(" ", $punchInTime)?>
            <div>&nbsp; <?php echo __("Last punch in time")." : "; ?><?php echo set_datepicker_date_format($dateArray[0])." ".$dateArray[1]; ?></div>
                <?php if (!empty($punchInNote)): ?>
                    <br class="clear">
                    <div style="width:60px; padding-left: 5px; float:left"><?php echo __("Note")." : "; ?></div><div style="float:left"><?php echo $punchInNote; ?></div>
                <?php endif; ?>
            <?php endif; ?><br class="clear">


        </div> 
        <br class="clear">
    </div>
</div>


<script type="text/javascript">
    //<![CDATA[
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var employeeId='<?php echo $employeeId; ?>';
    var selectedDate='<?php echo $date; ?>';
    var currentTime='<?php echo $currentTime; ?>';
    var currentDate='<?php echo $currentDate; ?>';
    var linkForProxyPunchAction='<?php echo url_for('attendance/proxyPunchInPunchOut') ?>';
    var linkForOverLappingValidation='<?php echo url_for('attendance/validatePunchOutOverLapping') ?>';
    var linkForPunchInOverlappingValidation='<?php echo url_for('attendance/validatePunchInOverLapping') ?>';
    var errorForInvalidTime='<?php echo __('Punch out Time Should Be Higher Than Punch in Time'); ?>';
    var errorForInvalidFormat="<?php echo __('Should Be a Valid Time in %format% Format', array('%format%' => 'HH:MM')) ?>";
    var errorForInvalidTimeFormat="<?php echo __('Should Be a Valid Time in %format% Format', array('%format%' => 'HH:MM')) ?>";
    var getCurrentTimeLink='<?php echo url_for('attendance/getCurrentTime') ?>';
    var errorForInvalidDateFormat='<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>';
    var errorForOverLappingTime="<?php echo __('Overlapping Records Found'); ?>";
    var errorForInvalidNote='<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)) ?>';
    var actionRecorder='<?php echo $actionRecorder; ?>';
   

    var punchOut =false;
    punchOut='<?php echo $action['PunchOut'] ?>'

  
    var punchInTime='<?php echo $punchInTime; ?>';
    var punchInNote='<?php echo json_encode($punchInNote); ?>';
    var punchInUtcTime='<?php echo $punchInUtcTime; ?>';       
  

    
    
</script>