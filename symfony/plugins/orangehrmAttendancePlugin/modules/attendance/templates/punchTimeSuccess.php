<?php /**

 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */ ?>
<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>
<?php if (!((in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME, $sf_data->getRaw('allowedActions')) && (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN, $sf_data->getRaw('allowedActions')))) || ((in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, $sf_data->getRaw('allowedActions')) && (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT, $sf_data->getRaw('allowedActions'))))))) : ?>
    <body id="b" onload="JavaScript:timedRefresh(1);">
<?php endif; ?>
        
<?php echo javascript_include_tag(plugin_web_path('orangehrmAttendancePlugin', 'js/punchTimeSuccess')); ?>

<!-- 
TODO: Use field level validation 
For top level messages, use new styles
-->
<div id="validationMsg" style="margin-left: 16px; width: 470px"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>

<?php
$heading = __('Punch In');
$heading = isset($actionPunchOut) ? __('Punch Out') : $heading;

$isEditable = (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, $sf_data->getRaw('allowedActions')) && (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN, $sf_data->getRaw('allowedActions')))) || (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, $sf_data->getRaw('allowedActions'))) && (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT, $sf_data->getRaw('allowedActions')));
$isPunchInAllowed = in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN, $sf_data->getRaw('allowedActions'));
$isPunchOutAllowed = in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT, $sf_data->getRaw('allowedActions'));

$punchInDisplayNote = '';

if ($isPunchOutAllowed) {

    $dateTimeArray = explode(" ", $punchInTime);
    $punchInDisplayTime = set_datepicker_date_format($dateTimeArray[0]) . " " . $dateTimeArray[1];
    
}
?>

<div class="box">

    <div class="head"><h1><?php echo $heading; ?></h1></div>

    <div class="inner">

        <?php include_partial('global/flash_messages'); ?>

        <form  id="punchTimeForm" method="post">

            <?php echo $form['_csrf_token']; ?>

            <fieldset>
                <ol>
                    <?php if ($isEditable) : ?>
                        <?php if ($isPunchOutAllowed) : ?>
                        <li>
                            <label><?php echo __('Punched in Time'); ?></label>
                            <label class="line"><?php echo $punchInDisplayTime; ?></label>
                        </li>
                        <?php if (!empty($punchInNote)) : ?>
                        <li>
                            <label><?php echo __('Punched in Note'); ?></label>
                            <label class="line"><?php echo $punchInNote; ?></label>
                        </li>
                        <?php endif; ?> 
                        <?php endif; ?> 
                        <li>
                            <label><?php echo $form['date']->renderLabel() ?></label>
                            <?php echo $form['date']->render(); ?>
                            <span id="dateErrorHolder" class="validation-error"></span>
                        </li>
                        <li>
                            <label><?php echo $form['time']->renderLabel() ?></label>
                            <?php echo $form['time']->render(); ?> <span class="fieldHelpRight">HH:MM</span>
                            <span id="timeErrorHolder" class="validation-error"></span>
                        </li>
                        <li class="largeTextBox">
                            <label><?php echo $form['note']->renderLabel() ?></label>
                            <?php echo $form['note']->render(); ?>
                            <span id="noteErrorHolder" class="validation-error"></span>
                        </li>
                    <?php else : ?>
                        <?php echo $attendanceFormToImplementCsrfToken['_csrf_token']; ?>
                        <?php if ($isPunchOutAllowed) : ?>
                        <li>
                            <label><?php echo __('Punched in Time'); ?></label>
                            <label><?php echo $punchInDisplayTime; ?></label>
                        </li>
                        <?php if (!empty($punchInNote)) : ?>
                        <li>
                            <label><?php echo __('Punched in Note'); ?></label>
                            <label><?php echo $punchInNote; ?></label>
                        </li>
                        <?php endif; ?> 
                        <?php endif; ?> 
                        <li>
                            <label><?php echo __('Date'); ?></label>
                            <span id="currentDate"></span><input type="hidden" class="date"name="date" value=""/>
                        </li>
                        <li>
                            <label><?php echo __('Time'); ?></label>
                            <span id="currentTime"></span><input  type="hidden" class="time"name="time" value=""> <span>HH:MM</span>
                        </li>
                        <li class="largeTextBox">
                            <label><?php echo __('Note'); ?></label>
                            <textarea id="note" class="note" name="note" rows="5" cols="50"></textarea>
                            <span id="noteErrorHolder" class="validation-error"></span>
                        </li>
                    <?php endif; ?>
                </ol> 

                <?php if ($isPunchInAllowed) : ?>
                    <p>
                        <input type="button" name="button" class="punchInbutton" id="btnPunch" value="<?php echo __('In'); ?>" />
                    </p>
                <?php endif; ?>                
                <?php if ($isPunchOutAllowed) : ?>
                    <p>
                        <input type="button" name="button" class="punchOutbutton" id="btnPunch" value="<?php echo __('Out'); ?>" />
                    </p>
                <?php endif; ?> 
            </fieldset>
        </form>
    </div>
</div>    

<?php
//TODO: Kept the 'if' condition as it was. Better to move to a meaningful variable
if (((in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME, $sf_data->getRaw('allowedActions')) && (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN, $sf_data->getRaw('allowedActions')))) || ((in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, $sf_data->getRaw('allowedActions')) && (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT, $sf_data->getRaw('allowedActions'))))))) :
    ?>
    <?php $editmode = true; ?>
</body>
<?php endif; ?>

<script type="text/javascript">
    //<![CDATA[
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';

    var linkForPunchIn ='<?php echo url_for('attendance/punchIn') ?>';
    var linkForPunchOut ='<?php echo url_for('attendance/punchOut') ?>';
    var linkForOverLappingValidation='<?php echo url_for('attendance/validatePunchOutOverLapping') ?>';
    var linkForPunchInOverlappingValidation='<?php echo url_for('attendance/validatePunchInOverLapping') ?>';
    var errorForInvalidTime='<?php echo __('Punch out Time Should Be Higher Than Punch in Time'); ?>';
    var errorForInvalidFormat="<?php echo __('Should Be a Valid Time in %format% Format', array('%format%' => 'HH:MM')) ?>";
    var errorForInvalidTimeFormat="<?php echo __('Should Be a Valid Time in %format% Format', array('%format%' => 'HH:MM')) ?>";
    var getCurrentTimeLink='<?php echo url_for('attendance/getCurrentTime') ?>';
    var errorForInvalidDateFormat='<?php echo __('Should Be a Valid Date in %format% Format', array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>';
    var errorForOverLappingTime="<?php echo __('Overlapping Records Found'); ?>";
    var errorForInvalidNote='<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)) ?>';

    var actionPunchIn='<?php echo $actionPunchIn; ?>';

    var actionPunchOut='<?php echo $actionPunchOut; ?>';

    var recordId = '<?php echo $recordId; ?>';
    var employeeId='<?php echo $employeeId; ?>';
    var currentTime='<?php echo $currentTime; ?>';
    var currentDate='<?php echo set_datepicker_date_format($currentDate); ?>';
    var punchInTime='<?php echo $punchInTime; ?>';
    var timeZone='<?php echo $timezone; ?>';
    var punchInNote='<?php echo json_encode($punchInNote); ?>';
    var punchInUtcTime='<?php echo $punchInUtcTime; ?>';

    var editMode =false;

    if('<?php echo $editmode ?>') {                
        editMode=true;
    }

    if(!editMode) {

        function timedRefresh(timeoutPeriod) {

            var t=setTimeout("getCurrentTime();",timeoutPeriod);


        }
        function getCurrentTime(){

            var d = new Date()
            var timeZone = -d.getTimezoneOffset()*60;
            var array = new Array();
            var r = $.ajax({
                type: 'POST',
                url: getCurrentTimeLink,
                data: "timeZone="+timeZone,
                async: false,

                success: function(msg){
                    array = msg.split("_");

                }
            });
            var parsedDate = $.datepicker.parseDate('yy-mm-dd', array[0]);
            array[0] = $.datepicker.formatDate(datepickerDateFormat, parsedDate);
            if(document.getElementById("currentDate") != null){
                document.getElementById("currentDate").innerHTML = array[0];}
            if(document.getElementById("currentTime") != null){
                document.getElementById("currentTime").innerHTML = array[1];}
            $(".time").val(array[1]);
            $(".date").val(array[0]);
            
            var t=setTimeout("getCurrentTime();",60000);

        }
    }

    //]]>
</script>