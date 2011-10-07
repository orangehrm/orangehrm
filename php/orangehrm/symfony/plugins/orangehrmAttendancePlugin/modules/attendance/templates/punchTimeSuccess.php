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
<?php if (!((in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, $sf_data->getRaw('allowedActions')) ))) : ?>

    <body id="b" onload="JavaScript:timedRefresh(1);">
    <?php endif; ?>
    <?php echo stylesheet_tag('../orangehrmAttendancePlugin/css/punchTimeSuccess'); ?>
    <?php echo javascript_include_tag('punchTimeSuccess'); ?>

    <link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
    <script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
    <?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
    <?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

    <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px; width: 470px">
        <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
    </div>


    <div id="validationMsg" style="margin-left: 16px; width: 470px"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>
    <div class="outerbox"  style="width: 500px">
        <div class="maincontent">

            <?php if (isset($actionPunchIn)): ?>
                <div class="mainHeading">
                    <h2><?php echo __('Punch In'); ?></h2>
                </div>
            <?php endif; ?>

            <?php if (isset($actionPunchOut)): ?>
                <div class="mainHeading">
                    <h2><?php echo __('Punch Out'); ?></h2>
                </div>
            <?php endif; ?>

            <br class="clear">
            <form  id="punchTimeForm" method="post">
                <table class="punchTable" border="0" cellpadding="5" cellspacing="0">
                    <tbody>
                        <?php echo $form['_csrf_token']; ?>
                        <?php if ((in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, $sf_data->getRaw('allowedActions')) )) : ?>
                            <tr>
                                <td><?php echo $form['date']->renderLabel() ?></td>
                                <td> <?php echo $form['date']->renderError() ?><?php echo $form['date']->render(); ?>&nbsp;<input id="DateBtn" type="button" name="" value="" class="calendarBtn"style="display: inline;margin:0;float:none; "/></td></tr>
                            <tr><td> <?php echo $form['time']->renderLabel() ?></td><td><?php echo $form['time']->renderError() ?><?php echo $form['time']->render(); ?><span class="timeFormatHint">HH:MM</span></td></tr>
                            <tr><td style="vertical-align: top" > <?php echo $form['note']->renderLabel() ?></td><td><?php echo $form['note']->renderError() ?><?php echo $form['note']->render(array("onkeyup" => "validateNote()")); ?></td></tr>
                        <?php else: ?>
                            <?php echo $attendanceFormToImplementCsrfToken['_csrf_token']; ?>

                            <tr><td> <?php echo __('Date'); ?></td><td>&nbsp;<span id="currentDate"></span><input type="hidden" class="date"name="date" value=""/></td></tr>
                            <tr><td>  <?php echo __('Time'); ?></td><td>&nbsp;<span id="currentTime"></span><input  type="hidden" class="time"name="time" value="">&nbsp;&nbsp;&nbsp;&nbsp;<span class="timeFormatHint">HH:MM</span></td></tr>

                            <tr><td id="noteLable"><?php echo __('Note'); ?></td><td>&nbsp;<textarea id="note" class="note" name="note" rows="5" cols="50" onkeyup="validateNote()"></textarea> </td></tr>
                        <?php endif; ?>


                        <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN, $sf_data->getRaw('allowedActions'))) : ?>
                            <tr> <td></td> <td> <input type="button" class="punchInbutton" name="button" id="btnPunch"
                                                       onmouseover="moverButton(this);" onmouseout="moutButton(this); "
                                                       value="<?php echo __('In'); ?>" /></td></tr>
                            <?php endif; ?>

                        <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT, $sf_data->getRaw('allowedActions'))) : ?>
                            <tr><td></td> <td><input type="button" class="punchOutbutton" name="button" id="btnPunch"
                                                     onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                                                     value="<?php echo __('Out'); ?>" /></td></tr>
                            <?php endif; ?>
                    </tbody>
                </table>
            </form>

            <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT, $sf_data->getRaw('allowedActions'))) : ?>
                <div>&nbsp; <?php echo __("Last punch in time"). ": "; ?><?php echo $punchInTime; ?></div>
                <?php if (!empty($punchInNote)):?>
                <br class="clear">
                <div style="width:40px; padding-left: 5px; margin-bottom: 5px; float:left"><?php echo __("Note").": "; ?></div><div style="float:left"><?php echo $punchInNote;?></div>
            <?php endif; ?>
            <?php endif; ?><br class="clear">
        </div>


    </div>

    <?php if (in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, $sf_data->getRaw('allowedActions')))  : ?>
        <?php $editmode = true; ?>
    <?php endif; ?>

</body>
<script type="text/javascript">
    //<![CDATA[
    //  dateTimeFormat = YAHOO.OrangeHRM.calendar.format+" "+YAHOO.OrangeHRM.time.format;
    var dateFormat        = '<?php echo $sf_user->getDateFormat(); ?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat()); ?>';
    var dateDisplayFormat = dateFormat.toUpperCase();

    var linkForPunchIn ='<?php echo url_for('attendance/punchIn') ?>';
    var linkForPunchOut ='<?php echo url_for('attendance/punchOut') ?>';
    var linkForOverLappingValidation='<?php echo url_for('attendance/validatePunchOutOverLapping') ?>';
    var linkForPunchInOverlappingValidation='<?php echo url_for('attendance/validatePunchInOverLapping') ?>';
    var errorForInvalidTime='<?php echo __('Punch out time should be higher than the punch in time'); ?>';
    var errorForInvalidFormat='<?php echo __('Please enter a valid time in HH:mm format'); ?>';
    var errorForInvalidTimeFormat='<?php echo __('Please enter a valid time in HH:mm format') ?>';
    var getCurrentTimeLink='<?php echo url_for('attendance/getCurrentTime') ?>';
    var errorForInvalidDateFormat='<?php echo __('Please enter a valid date in yyyy-MM-dd format') ?>';
    var errorForOverLappingTime='<?php echo __('Overlapping records found'); ?>';
    var errorForInvalidNote='<?php echo __('Comment should be less than 250 characters') ?>';

    var actionPunchIn='<?php echo $actionPunchIn; ?>';

    var actionPunchOut='<?php echo $actionPunchOut; ?>';
    

    var employeeId='<?php echo $employeeId; ?>';
    var recordId='<?php echo $recordId; ?>';
    var currentTime='<?php echo $currentTime; ?>';
    var currentDate='<?php echo $currentDate; ?>';
    var punchInTime='<?php echo $punchInTime; ?>';
    var timeZone='<?php echo $timezone; ?>';
    
    var punchInNote='<?php echo json_encode($punchInNote); ?>';
    var punchInUtcTime='<?php echo $punchInUtcTime; ?>';
                                              
    //  

    var editMode =false;

    if( '<?php echo $editmode ?>'){
                
        editMode=true;
    }

    if(!editMode){

        function timedRefresh(timeoutPeriod) {

            var t=setTimeout("getCurrentTime();",timeoutPeriod);


        }
        function getCurrentTime(){

            var d = new Date()
            var timeZone = -d.getTimezoneOffset()*60;

            var r = $.ajax({
                type: 'POST',
                url: getCurrentTimeLink,
                data: "timeZone="+timeZone,
                async: false,

                success: function(msg){
                    array = msg.split("_");

                }
            });
            document.getElementById("currentDate").innerHTML = array[0];
            document.getElementById("currentTime").innerHTML = array[1];
            $(".time").val(array[1]);
            $(".date").val(array[0]);



            var t=setTimeout("getCurrentTime();",60000);

        }
    }

    //]]>
</script>