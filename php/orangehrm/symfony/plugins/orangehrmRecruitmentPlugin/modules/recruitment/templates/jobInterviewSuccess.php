<?php
/**
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
 *
 */
?>

<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.datepicker.js') ?>"></script>
<?php echo stylesheet_tag('orangehrm.datepicker.css') ?>
<?php echo javascript_include_tag('orangehrm.datepicker.js') ?>
<?php use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css'); ?>
<?php use_javascript('../../../scripts/jquery/jquery.autocomplete.js'); ?>

<?php use_stylesheet('../orangehrmRecruitmentPlugin/css/jobInterviewSuccess'); ?>
<?php use_javascript('../orangehrmRecruitmentPlugin/js/jobInterviewSuccess'); ?>

<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="jobInterview">
    <div class="outerbox">
        <div class="mainHeading"><h2 id="jobInterviewHeading"><?php echo __("Schedule Interview"); ?></h2></div>
        <form name="frmJobInterview" id="frmJobInterview" method="post" action="<?php echo url_for('recruitment/jobInterview?candidateVacancyId=' . $form->candidateVacancyId . '&selectedAction=' . $form->selectedAction.'&interviewId='.$interviewId); ?>">
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['selectedInterviewerList']; ?>
            <br class="clear" />
            <label class="firstLabel">Candidate Name</label>
            <label class="secondLabel"><?php echo $form->candidateName; ?></label>
            <br class="clear" />
            <label class="firstLabel">Vacancy Name</label>
            <label class="secondLabel"><?php echo $form->vacancyName; ?></label>
            <br class="clear" />
            <?php echo $form['name']->renderLabel(__('Interview Heading')); ?>
            <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
            <br class="clear" />
            <label class="firstLabel">Interviewer Name</label>

            <?php for ($i = 1; $i <= $form->numberOfInterviewers; $i++) {
            ?>
                <div class="interviewer" id="<?php echo "interviewer_" . $i ?>">
                <?php echo $form['interviewer_' . $i]->render(array("class" => "formInputInterviewer", "maxlength" => 100)); ?>
                <span class="removeText" id=<?php echo "removeButton" . $i ?>><?php echo __('Remove'); ?></span>
                <br class="clear" />
            </div>
            <?php } ?>
            <span class="addText" id='addButton'><?php echo __('Add another'); ?></span>
            <div id="interviewerNameError"></div>
            <br class="clear" />
            <?php echo $form['date']->renderLabel(__('Date')); ?>
            <?php echo $form['date']->render(array("class" => "formInputText", "maxlength" => 25)); ?>
            <input id="frmDateBtn" type="button" name="" value="  " class="calendarBtn" />
            <br class="clear" />
            <?php echo $form['time']->renderLabel(__('Time')); ?>
            <?php echo $form['time']->render(array("class" => "formInputText", "maxlength" => 20)); ?>
            <label class="hhmm" style="padding-left: 6px">HH:MM</label>
            <br class="clear" />
            <?php echo $form['note']->renderLabel(__('Notes')); ?>
            <?php echo $form['note']->render(array("class" => "formInputText", "maxlength" => 255, "cols" => 30, "rows" => 7)); ?>
            <br class="clear" />
            <div class="formbuttons">
                <input type="button" class="savebutton" name="actionBtn" id="saveBtn"
                       value="<?php echo __('Save'); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    //<![CDATA[
    var cancelBtnUrl = '<?php echo url_for('recruitment/addCandidate?'); ?>';
    var dateFormat	= '<?php echo $sf_user->getDateFormat(); ?>';
    var jsDateFormat = '<?php echo get_js_date_format($sf_user->getDateFormat()); ?>';
    var dateDisplayFormat = dateFormat.toUpperCase();
    var lang_validDateMsg = '<?php echo __("Please enter a valid date in %format% format", array('%format%' => strtoupper($sf_user->getDateFormat()))) ?>';
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeeList = eval(employees);
    var numberOfInterviewers = <?php echo $form->numberOfInterviewers; ?>;
    var lang_identical_rows = "<?php echo __("Cannot assign same interviewer twice"); ?>";
    var interviewId = "<?php echo $interviewId; ?>";

    //]]>
</script>