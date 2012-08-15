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
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>

<?php echo stylesheet_tag('../orangehrmTimePlugin/css/createTimesheetForSubourdinateSuccess'); ?>
<?php echo javascript_include_tag('../orangehrmTimePlugin/js/createTimesheetForSubourdinateSuccess'); ?>

<div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>

 &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;<?php if (in_array(WorkflowStateMachine::TIMESHEET_ACTION_CREATE, $sf_data->getRaw('allowedToCreateTimesheets'))): ?>
    <input type="button" class="addTimesheetbutton" name="button" id="btnAddTimesheet"
           onmouseover="moverButton(this);" onmouseout="moutButton(this);"
           value="<?php echo __('Add Timesheet') ?>" />
       <?php endif; ?>

<div id="createTimesheet">
    <br class="clear"/>
    <form  id="createTimesheetForm" action=""  method="post">
        <?php echo $createTimesheetForm['_csrf_token']; ?>

        <?php echo $createTimesheetForm['date']->renderLabel(__('Select a Day to Create Timesheet')); ?>
        <?php echo $createTimesheetForm['date']->render(); ?><input id="DateBtn" type="button" name="" value="" class="calendarBtn" />
        <?php echo $createTimesheetForm['date']->renderError() ?>
        <br class="clear"/>
    </form>

</div>


<script type="text/javascript">
                                                    
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var employeeId = "<?php echo $employeeId; ?>";
    var linkForViewTimesheet="<?php echo url_for('time/viewTimesheet') ?>";
    var validateStartDate="<?php echo url_for('time/validateStartDate'); ?>";
    var createTimesheet="<?php echo url_for('time/createTimesheet'); ?>";
    var returnEndDate="<?php echo url_for('time/returnEndDate'); ?>";
    var currentDate= "<?php echo $currentDate; ?>";
    var lang_noFutureTimesheets= "<?php echo __("Failed to Create: Future Timesheets Not Allowed"); ?>";
    var lang_overlappingTimesheets= "<?php echo __("Timesheet Overlaps with Existing Timesheets"); ?>";
    var lang_timesheetExists= "<?php echo __("Timesheet Already Exists"); ?>";
    var lang_invalidDate= '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
</script>