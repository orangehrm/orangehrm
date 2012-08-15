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
            <label class="firstLabel"><?php echo __('Candidate Name');?></label>
            <label class="secondLabel"><?php echo $form->candidateName; ?></label>
            <br class="clear" />
            <label class="firstLabel"><?php echo __('Vacancy Name'); ?></label>
            <label class="secondLabel"><?php echo $form->vacancyName; ?></label>
            <br class="clear" />
            <label class="firstLabel"><?php echo __('Current Status'); ?></label>
            <label class="secondLabel"><?php echo __($form->currentStatus); ?></label>
            <br class="clear" />
            <?php echo $form['name']->renderLabel(__('Interview Title') . ' <span class="required">*</span>') ; ?>
            <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
            <div class="errorHolder"></div>
            <br class="clear" />
            <label class="firstLabel"><?php echo __('Interviewer Name') . ' <span class="required">*</span>'; ?></label>

            <?php for ($i = 1; $i <= $form->numberOfInterviewers; $i++) {
            ?>
                <div class="interviewer" id="<?php echo "interviewer_" . $i ?>">
                <?php echo $form['interviewer_' . $i]->render(array("class" => "formInputInterviewer", "maxlength" => 100)); ?>                
                <span class="removeText" id=<?php echo "removeButton" . $i ?>><?php echo __('Remove'); ?></span>
                <div class="errorHolder interviwerErrors interviwerErrorContainers"></div>
                <br class="clear" />
            </div>
            <?php } ?>
            <a class="addText" id='addButton'><?php echo __('Add another'); ?></a>
            <div id="interviewerNameError"></div>
            <br class="clear" />
            <?php echo $form['date']->renderLabel(__('Date') . ' <span class="required">*</span>'); ?>
            <?php echo $form['date']->render(array("class" => "formInputText", "maxlength" => 25)); ?>            
            <div class="errorHolder"></div>
            <br class="clear" />
            <?php echo $form['time']->renderLabel(__('Time')); ?>
            <?php echo $form['time']->render(array("class" => "formInputText", "maxlength" => 20)); ?>       
            <?php echo $form['time']->renderError(); ?>
            <label class="hhmm" style="padding-left: 6px">HH:MM</label>
            <div class="errorHolder"></div>
            <br class="clear" />
            <?php echo $form['note']->renderLabel(__('Notes')); ?>
            <?php echo $form['note']->render(array("class" => "formInputText", "cols" => 30, "rows" => 7)); ?>
            <br class="clear" />
            <div class="formbuttons">
		<?php if(empty ($interviewId)){?>
                <input type="button" class="savebutton" name="actionBtn" id="saveBtn"
                       value="<?php echo __('Save'); ?>" onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
		<?php } else {?>
		<input type="button" class="savebutton" name="actionBtn" id="saveBtn"
                       value="<?php echo __('Edit'); ?>" onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
		<?php } ?>
                <input type="button" class="cancelbutton" name="cancelButton" id="cancelButton"
                           value="<?php echo __("Back"); ?>" onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
            </div>
        </form>
    </div>
</div>

<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>

<?php if (isset($interviewId)) { ?>
    <br class="clear"/>
    <br class="clear"/>
    <div>
        <?php echo include_component('recruitment', 'attachments', array('id' => $interviewId, 'screen' => JobInterview::TYPE)); ?>
    </div>
<?php } ?>


<script type="text/javascript">
    //<![CDATA[
    var cancelBtnUrl = '<?php echo url_for('recruitment/addCandidate?'); ?>';
    var cancelUrl = '<?php echo url_for('recruitment/changeCandidateVacancyStatus?'); ?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeeList = eval(employees);
    var numberOfInterviewers = <?php echo $form->numberOfInterviewers; ?>;
    var lang_identical_rows = "<?php echo __("Already exists"); ?>";
    var interviewId = "<?php echo $interviewId; ?>";
    var historyId = "<?php echo $historyId; ?>";
    var getInterviewSheduledTimeListActionUrl = "<?php echo url_for('recruitment/getInterviewSheduledTimeListJson?candidateId=' . $form->candidateId); ?>";
    var lang_interviewHeadingRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_noMoreThan98 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_noMoreThan18 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>";
    var lang_enterAValidEmployeeName = "<?php echo __(ValidationMessages::INVALID); ?>";
    var lang_dateRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validTimeRequired = "<?php echo __(ValidationMessages::INVALID); ?>";
    var lang_viewInterviewDetails = "<?php echo __('View Interview Details'); ?>";
    var lang_editInterviewDetails = "<?php echo __('Edit Interview Details'); ?>";
    var addCandidateUrl = "<?php echo public_path('index.php/recruitment/addCandidate', true) . "?id=" . $form->candidateId; ?>";
    var lang_typeHint = "<?php echo __("Type for hints");?>" + "...";
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_back = "<?php echo __("Back"); ?>";
    var lang_cancel = "<?php echo __("Cancel"); ?>";
    var editHiringManager = "<?php echo $editHiringManager; ?>";
    //]]>
</script>