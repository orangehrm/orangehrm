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
<?php use_stylesheet(plugin_web_path('orangehrmRecruitmentPlugin', 'css/changeCandidateVacancyStatusSuccess')); ?>
<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/changeCandidateVacancyStatusSuccess')); ?>
<?php if($candidatePermissions->canRead()){?>
<div class="box" id="addJobVacancy">

    <div class="head">
        <h1><?php echo __($form->actionName); ?></h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <form name="frmCandidateVacancyStatus" id="frmCandidateVacancyStatus" method="post">
            <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li>
                        <label class="firstLabel"><?php echo __('Candidate Name'); ?></label>
                        <label class="secondLabel"><?php echo htmlspecialchars($form->candidateName); ?></label>
                    </li>
                    <li>
                        <label class="firstLabel"><?php echo __('Vacancy'); ?></label>
                        <label class="secondLabel"><?php echo htmlspecialchars($form->vacancyName); ?></label>
                    </li>
                    <li>
                        <label class="firstLabel"><?php echo __('Hiring Manager'); ?></label>
                        <label class="secondLabel"><?php echo htmlspecialchars($form->hiringManagerName); ?></label>
                    </li>
                    <li>
                        <label class="firstLabel"><?php echo __('Current Status'); ?></label>
                        <label class="secondLabel"><?php echo __($form->currentStatus); ?></label>
                    </li>
                    <?php if ($form->id > 0): ?>
                        <li>
                            <label class="firstLabel"><?php echo __('Performed Action'); ?></label>
                            <label class="secondLabel"><?php echo __($form->performedActionName); ?></label>
                        </li>
                        <li>
                            <label class="firstLabel"><?php echo __('Performed By'); ?></label>
                            <label class="secondLabel"><?php echo htmlspecialchars($form->performedBy); ?></label>
                        </li>
                        <li>
                            <label class="firstLabel"><?php echo __('Performed Date'); ?></label>
                            <label class="secondLabel"><?php echo $form->performedDate; ?></label>
                        </li>
                    <?php endif; ?>

                    <li>
                        <?php echo $form['notes']->renderLabel(__('Notes')); ?>
                        <?php echo $form['notes']->render(array("class" => "formInputText", 'max_length' => 2147483647, "cols" => 40, "rows" => 9)); ?>
                    </li>
                </ol>
                <p>
                    <?php if($candidatePermissions->canUpdate()){?>
                        <?php if (!($form->id > 0)): ?>
                            <input type="button" class="savebutton" name="actionBtn" id="actionBtn" value="<?php echo __($form->actionName); ?>"/>
                        <?php elseif ($enableEdit): ?>
                            <input type="button" class="savebutton" name="btnSave" id="btnSave" value="<?php echo __('Edit'); ?>"/>
                        <?php endif; ?>
                    <?php }?>
                    <input type="button" class="cancelbutton" name="cancelBtn" id="cancelBtn" value="<?php echo __("Back"); ?>"/>
                </p>
            </fieldset>
        </form>
    </div>
    <?php if (!empty($interviewId)) { ?>
        <?php echo include_component('recruitment', 'attachments', array('id' => $interviewId, 'screen' => JobInterview::TYPE, 'permissions'=>$candidatePermissions)); ?>
    <?php } ?>
</div>
<?php }?>

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