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
<?php use_stylesheet('../orangehrmRecruitmentPlugin/css/changeCandidateVacancyStatusSuccess'); ?>
<?php use_javascript('../orangehrmRecruitmentPlugin/js/changeCandidateVacancyStatusSuccess'); ?>

 <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
            <span><?php echo isset($message) ? $message : ''; ?></span>
 </div>

<div id="candidateVacancyStatus">
    <div class="outerbox">
        <div class="mainHeading"><h2 id="candidateActionHeading"><?php echo __($form->actionName); ?></h2></div>
        <form name="frmCandidateVacancyStatus" id="frmCandidateVacancyStatus" method="post">
            <?php echo $form['_csrf_token']; ?>
            <br class="clear" />
            <label class="firstLabel"><?php echo __('Candidate Name'); ?></label>
            <label class="secondLabel"><?php echo $form->candidateName; ?></label>
            <br class="clear" />
            <label class="firstLabel"><?php echo __('Vacancy'); ?></label>
            <label class="secondLabel"><?php echo $form->vacancyName; ?></label>
            <br class="clear" />
            <label class="firstLabel"><?php echo __('Hiring Manager'); ?></label>
            <label class="secondLabel"><?php echo $form->hiringManagerName; ?></label>
            <br class="clear" />
            <label class="firstLabel"><?php echo __('Current Status'); ?></label>
            <label class="secondLabel"><?php echo __($form->currentStatus); ?></label>
            <?php if ($form->id > 0): ?>
                <br class="clear" />
                <label class="firstLabel"><?php echo __('Performed Action'); ?></label>
                <label class="secondLabel"><?php echo __($form->performedActionName); ?></label>
                <br class="clear" />
                <label class="firstLabel"><?php echo __('Performed By'); ?></label>
                <label class="secondLabel"><?php echo __($form->performedBy); ?></label>
                <br class="clear" />
                <label class="firstLabel"><?php echo __('Performed Date'); ?></label>
                <label class="secondLabel"><?php echo $form->performedDate; ?></label>
            <?php endif; ?>
                <br class="clear" />
                <br class="clear" />
            <?php echo $form['notes']->renderLabel(__('Notes')); ?>
            <?php echo $form['notes']->render(array("class" => "formInputText", 'max_length' => 2147483647, "cols" => 40, "rows" => 9)); ?>
                <br class="clear" />
                <div class="formbuttons">
                <?php if (!($form->id > 0)): ?>
                    <input type="button" class="savebutton" name="actionBtn" id="actionBtn"
                           value="<?php echo __($form->actionName); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                       <?php elseif($enableEdit): ?>
                            <input type="button" class="savebutton" name="btnSave" id="btnSave"
                           value="<?php echo __('Edit'); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                       <?php endif; ?>
                    <input type="button" class="cancelbutton" name="cancelBtn" id="cancelBtn"
                           value="<?php echo __("Back"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                </div>
            </form>
        </div>
    </div>

<?php if (!empty ($interviewId)) { ?>
    <br class="clear"/>
    <br class="clear"/>
    <div>
        <?php echo include_component('recruitment', 'attachments', array('id' => $interviewId, 'screen' => JobInterview::TYPE)); ?>
    </div>
<?php } ?>



<script type="text/javascript">
        //<![CDATA[
        var candidateId = "<?php echo $form->candidateId; ?>";
        var cancelBtnUrl = '<?php echo url_for('recruitment/addCandidate?'); ?>';
        var cancelUrl = '<?php echo url_for('recruitment/changeCandidateVacancyStatus?'); ?>';
        var lang_edit = "<?php echo __('Edit'); ?>";
        var lang_save = "<?php echo __('Save'); ?>";
        var lang_back = "<?php echo __('Back'); ?>";
        var lang_cancel = "<?php echo __('Cancel'); ?>";
        var candidateVacancyId = "<?php echo $form->candidateVacancyId; ?>";
        var selectedAction = "<?php echo $form->selectedAction; ?>";
        var historyId = "<?php echo $form->id; ?>";
        var selectedAction = "<?php echo $selectedAction; ?>";
        var passAction = "<?php echo WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED; ?>";
        var failAction = "<?php echo WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED; ?>";
        var linkForchangeCandidateVacancyStatus = "<?php echo url_for('recruitment/changeCandidateVacancyStatus?'); ?>";
    //]]>
</script>