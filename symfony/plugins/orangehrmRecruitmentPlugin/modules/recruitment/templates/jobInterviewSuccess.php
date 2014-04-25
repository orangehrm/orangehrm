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
<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>

<?php
use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/jobInterviewSuccess'));
?>
<?php if($candidatePermissions->canRead()){?>
<div class="box" id="jobInterview">
    <div class="head">
        <h1><?php echo __("Schedule Interview"); ?></h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <form name="frmJobInterview" id="frmJobInterview" method="post" action="<?php echo url_for('recruitment/jobInterview?candidateVacancyId=' . $form->candidateVacancyId . '&selectedAction=' . $form->selectedAction . '&interviewId=' . $interviewId); ?>">
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form['selectedInterviewerList']; ?>
            <fieldset>
                <ol>
                    <li>
                        <label class="firstLabel"><?php echo __('Candidate Name'); ?></label>
                        <label class="secondLabel line"><?php echo htmlspecialchars($form->candidateName); ?></label>
                    </li>
                    <li>
                        <label class="firstLabel"><?php echo __('Vacancy Name'); ?></label>
                        <label class="secondLabel line"><?php echo htmlspecialchars($form->vacancyName); ?></label>
                    </li>
                    <li>
                        <label class="firstLabel"><?php echo __('Current Status'); ?></label>
                        <label class="secondLabel line"><?php echo __($form->currentStatus); ?></label>
                    </li>
                    <li>
                        <?php echo $form['name']->renderLabel(__('Interview Title') . ' <em>*</em>', array('class' => 'firstLabel')); ?>
                        <?php echo $form['name']->render(array("maxlength" => 100)); ?>
                    </li>
                        <?php for ($i = 1; $i <= $form->numberOfInterviewers; $i++) {
                            ?>
                            <li class="<?php echo ($i == 1) ?'':'interviewer noLabel'; ?>" id="<?php echo "interviewer_" . $i ?>">
                                <?php if ($i == 1) : ?>
                                <label class="firstLabel"><?php echo __('Interviewer Name') . ' <em>*</em>'; ?></label>
                                <?php endif; ?>                                
                                <?php echo $form['interviewer_' . $i]->render(array("class" => "formInputInterviewer", "maxlength" => 100)); ?>                
                                <?php if($candidatePermissions->canUpdate()){?>
                                    <?php if($i != 1) { ?>
                                        <a class="removeText fieldHelpRight" id=<?php echo "removeButton" . $i ?>><?php echo __('Remove'); ?></a>
                                    <?php } else { ?>
                                        <a class="addText fieldHelpRight" id='addButton'><?php echo __('Add Another'); ?></a>
                                    <?php } ?>       
                                <?php }?>
                            </li>
                        <?php } ?>
                    <li>
                        <?php echo $form['date']->renderLabel(__('Date') . ' <em>*</em>', array('class' => 'firstLabel')); ?>
                        <?php echo $form['date']->render(array("class" => "calendar")); ?>            
                    </li>
                    <li>
                        <?php echo $form['time']->renderLabel(__('Time'), array('class' => 'firstLabel')); ?>
                        <?php echo $form['time']->render(array("maxlength" => 20)); ?>            
                        <label class="hhmm" style="padding-left: 6px">HH:MM</label>
                    </li>
                    <li>
                        <?php echo $form['note']->renderLabel(__('Notes'), array('class' => 'firstLabel')); ?>
                        <?php echo $form['note']->render(array("cols" => 30, "rows" => 7)); ?>
                    </li>
                    <li>
                    <li class="required" style="clear: both">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    </li>
                </ol>
                <p>
                    <?php if($candidatePermissions->canUpdate()){?>
                        <?php if (empty($interviewId)) { ?>
                            <input type="button" name="actionBtn" id="saveBtn" value="<?php echo __('Save'); ?>"/>
                        <?php } else { ?>
                            <input type="button" name="actionBtn" id="saveBtn" value="<?php echo __('Edit'); ?>"/>
                        <?php } ?>
                    <?php }?>
                    <input type="button" name="cancelButton" id="cancelButton" value="<?php echo __("Back"); ?>"/>
                </p>
        </form>
    </div>
    <?php
    if (isset($interviewId)) {
        echo include_component('recruitment', 'attachments', array('id' => $interviewId, 'screen' => JobInterview::TYPE, 'permissions'=>$candidatePermissions));
    }
    ?>
</div>
<?php }?>

<script type="text/javascript">
    //<![CDATA[
    var cancelBtnUrl = '<?php echo url_for('recruitment/addCandidate?'); ?>';
    var cancelUrl = '<?php echo url_for('recruitment/changeCandidateVacancyStatus?'); ?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => get_datepicker_date_format($sf_user->getDateFormat()))) ?>';
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeeList = eval(employees);
    var numberOfInterviewers = <?php echo $form->numberOfInterviewers; ?>;
    var lang_identical_rows = "<?php echo __("Already exists"); ?>";
    var interviewId = "<?php echo $interviewId; ?>";
    var historyId = "<?php echo $historyId; ?>";
    var getInterviewSheduledTimeListActionUrl = "<?php echo url_for('recruitment/getInterviewSheduledTimeListJson?candidateId=' . $form->candidateId); ?>";
    var lang_interviewHeadingRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_interviewerRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_noMoreThan98 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_noMoreThan18 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>";
    var lang_enterAValidEmployeeName = "<?php echo __(ValidationMessages::INVALID); ?>";
    var lang_dateRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validTimeRequired = "<?php echo __(ValidationMessages::INVALID); ?>";
    var lang_viewInterviewDetails = "<?php echo __('View Interview Details'); ?>";
    var lang_editInterviewDetails = "<?php echo __('Edit Interview Details'); ?>";
    var addCandidateUrl = "<?php echo public_path('index.php/recruitment/addCandidate', true) . "?id=" . $form->candidateId; ?>";
    var lang_typeHint = "<?php echo __("Type for hints"); ?>" + "...";
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_back = "<?php echo __("Back"); ?>";
    var lang_cancel = "<?php echo __("Cancel"); ?>";
    var editHiringManager = "<?php echo $editHiringManager; ?>";
    //]]>
</script>
