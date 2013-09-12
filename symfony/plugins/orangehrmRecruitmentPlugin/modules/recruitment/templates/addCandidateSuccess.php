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

<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/addCandidateSuccess')); ?>
<?php $title = ($candidateId > 0) ? __('Candidate') : __('Add Candidate'); ?>
<?php
$allVacancylist[] = array("id" => "", "name" => __('-- Select --'));
$allowedVacancylist[] = array("id" => "", "name" => __('-- Select --'));
$allowedVacancylistWithClosedVacancies[] = array("id" => "", "name" => __('-- Select --'));
$allowedVacancyIdArray[] = array();
$closedVacancyIdArray[] = array();
foreach ($jobVacancyList as $vacancy) {
    $newVacancyId = $vacancy['id'];
    $newVacancyName = ($vacancy['status'] == JobVacancy::CLOSED) ? $vacancy['name'] . " (" . __('Closed') . ")" : $vacancy['name'];
    $allVacancylist[] = array("id" => $newVacancyId, "name" => $newVacancyName);
    if (in_array($vacancy['id'], $form->allowedVacancyList)) {
        $allowedVacancylistWithClosedVacancies[] = array("id" => $newVacancyId, "name" => $newVacancyName);
        $allowedVacancyIdArray[] = $newVacancyId;
        if ($vacancy['status'] == JobVacancy::ACTIVE) {
            $allowedVacancylist[] = array("id" => $newVacancyId, "name" => $newVacancyName);
        } else {
            $closedVacancyIdArray[] = $newVacancyId;
        }
    }
}
?>
<style type="text/css">
.actionDrpDown {
    width: 170px;
    margin:1px 10px 0 10px;
}    
</style>
<?php if($candidatePermissions->canRead()){?>
<div class="box" id="addCandidate">

    <div class="head"><h1 id="addCandidateHeading"><?php echo $title; ?></h1></div>
    <div class="inner">
        <?php include_partial('global/flash_messages', array('prefix' => 'addcandidate')); ?>
        <form name="frmAddCandidate" id="frmAddCandidate" method="post" action="<?php echo url_for('recruitment/addCandidate?id=' . $candidateId); ?>" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li class="line nameContainer">

                        <label class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
                        <ol class="fieldsInLine">
                            <li>
                                <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                                <?php echo $form['firstName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                                 <?php echo $form['middleName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"><em>*</em> <?php echo __('Last Name'); ?></div>
                                <?php echo $form['lastName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                            </li>
                        </ol>                        

                    </li>

                    <li>

                        <?php echo $form['email']->renderLabel(__('Email') . ' <em>*</em>'); ?>
                        <?php echo $form['email']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li>
                        <?php echo $form['contactNo']->renderLabel(__('Contact No'), array("class " => "contactNoLable")); ?>
                        <?php echo $form['contactNo']->render(array("class" => "contactNo")); ?>
                    </li>
                </ol>
                <ol>
                    <li  class="line">
                        <?php echo $form['vacancy']->renderLabel(__('Job Vacancy'), array("class" => "vacancyDrpLabel")); ?>
                        <?php echo $form['vacancy']->render(array("class" => "vacancyDrp")); ?>


                        <?php if ($candidateId > 0) : ?>
                            <?php $existingVacancyList = $actionForm->candidate->getJobCandidateVacancy(); ?>
                            <?php if ($existingVacancyList[0]->getVacancyId() > 0) : ?>
                        <div id="actionPane" style="float:left; width:600px; padding-top:0px">
                                <?php $i = 0 ?>
                                <?php foreach ($existingVacancyList as $candidateVacancy) {
                                    ?>
                                    <div id="<?php echo $i ?>">
                                    <?php
                                    $widgetName = $candidateVacancy->getId();
                                    echo $actionForm[$widgetName]->render(array("class" => "actionDrpDown"));
                                    ?> 
                                <span class="status" style="font-weight: bold"><?php echo __("Status") . ": " . __(ucwords(strtolower($candidateVacancy->getStatus()))); ?></span>
                                    <?php
                                }
                                $i++;
                                ?>

                                <?php //} ?>

                            <?php endif; ?>
                        <?php endif; ?>
                    </li>

                    <!-- Resume block : Begins -->

                    <li>    

                        <?php
                        if ($form->attachment == "") {
                            echo $form['resume']->renderLabel(__('Resume'), array("class " => "resume"));
                            echo $form['resume']->render();
                            echo "<label class=\"fieldHelpBottom\">" . __(CommonMessages::FILE_LABEL_DOC) . "</label>";
                        } else {
                            $attachment = $form->attachment;
                            $linkHtml = "<div id=\"fileLink\"><a target=\"_blank\" class=\"fileLink\" href=\"";
                            $linkHtml .= url_for('recruitment/viewCandidateAttachment?attachId=' . $attachment->getId());
                            $linkHtml .= "\">{$attachment->getFileName()}</a></div>";

                            echo $form['resumeUpdate']->renderLabel(__('Resume'));
                            echo $linkHtml;
                            echo "<li class=\"radio noLabel\" id=\"radio\">";
                            echo $form['resumeUpdate']->render(array("class" => "fileEditOptions"));
                            echo "</li>";
                            echo "<li id=\"fileUploadSection\" class=\"noLabel\">";
                            echo $form['resume']->renderLabel(' ');
                            echo $form['resume']->render(array("class " => "duplexBox"));
                            echo "<label class=\"fieldHelpBottom\">" . __(CommonMessages::FILE_LABEL_DOC) . "</label>";
                            echo "</li>";
                        }
                        ?>
                    </li>

                    <!-- Resume block : Ends -->

                    <li>
                        <?php echo $form['keyWords']->renderLabel(__('Keywords'), array("class " => "keywrd")); ?>
                        <?php echo $form['keyWords']->render(array("class" => "keyWords")); ?>
                    </li>
                    <li>
                        <?php echo $form['comment']->renderLabel(__('Comment'), array("class " => "comment")); ?>
                        <?php echo $form['comment']->render(array("class" => "formInputText", "cols" => 35, "rows" => 4)); ?>
                    </li>
                    <li>
                        <?php echo $form['appliedDate']->renderLabel(__('Date of Application'), array("class " => "appDate")); ?>
                        <?php echo $form['appliedDate']->render(array("class" => "formDateInput")); ?>
                    </li>
                    <li class="required new">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>

                    <?php if ($edit): ?>
                        <?php if(($candidatePermissions->canCreate() && empty($candidateId)) || ($candidatePermissions->canUpdate() && $candidateId > 0)) {?>
                        <input type="button"id="btnSave" value="<?php echo __("Save"); ?>"/>
                        <?php }?>
                    <?php endif; ?>
                    <?php if ($candidateId > 0): ?>
                        <input type="button" class="cancel" id="btnBack" value="<?php echo __("Back"); ?>"/>
                    <?php endif; ?>

                </p>
            </fieldset>
        </form>
    </div>

</div>

<?php if ($candidateId > 0) : ?>
    <?php $existingVacancyList = $actionForm->candidate->getJobCandidateVacancy(); ?>
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
<?php endif; ?>

<!-- Confirmation box - delete HTML: Begins -->
<div class="modal hide" id="deleteConfirmation">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->

<!-- Confirmation box - remove vacancies & save HTML: Begins -->
<div class="modal hide" id="deleteConfirmationForSave">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("This action will remove previous vacancy"); ?></p>
        <br>
        <p><?php echo __('Remove?'); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogSaveButton" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" id="dialogCancelButton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box remove vacancies & save HTML: Ends -->
<?php }?>
<script type="text/javascript">
    //<![CDATA[
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_emailRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_validEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var list = <?php echo json_encode($allVacancylist); ?>;
    var allowedVacancylistWithClosedVacancies = <?php echo json_encode($allowedVacancylistWithClosedVacancies); ?>;
    var allowedVacancylist = <?php echo json_encode($allowedVacancylist); ?>;
    var allowedVacancyIdArray = <?php echo json_encode($allowedVacancyIdArray); ?>;
    var closedVacancyIdArray = <?php echo json_encode($closedVacancyIdArray); ?>;
    var lang_identical_rows = "<?php echo __('Cannot assign same vacancy twice'); ?>";
    var lang_tooLargeInput = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 30)); ?>";
    var lang_commaSeparated = "<?php echo __('Enter comma separated words') . '...'; ?>";
    var currentDate = '<?php echo set_datepicker_date_format(date("Y-m-d")); ?>';
    var lang_dateValidation = "<?php echo __("Should be less than current date"); ?>";
    var lang_validPhoneNo = "<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>";
    var lang_noMoreThan250 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_cancel = "<?php echo __("Cancel"); ?>";
    var candidateId = "<?php echo $candidateId; ?>";
    var attachment = "<?php echo $form->attachment; ?>"
    var changeStatusUrl = '<?php echo url_for('recruitment/changeCandidateVacancyStatus?'); ?>';
    var backBtnUrl = '<?php echo url_for('recruitment/viewCandidates?'); ?>';
    var cancelBtnUrl = '<?php echo url_for('recruitment/addCandidate?'); ?>';
    var interviewUrl = '<?php echo url_for('recruitment/jobInterview?'); ?>';
    var interviewAction = '<?php echo WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW; ?>';
    var interviewAction2 = '<?php echo WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW; ?>';
    var removeAction = '<?php echo JobCandidateVacancy::REMOVE; ?>';
    var lang_remove =  '<?php echo __("Remove"); ?>';
    var lang_editCandidateTitle = "<?php echo __('Edit Candidate'); ?>";
    var editRights = "<?php echo $edit; ?>";
    var activeStatus = "<?php echo JobCandidate::ACTIVE; ?>";
    var candidateStatus = "<?php echo $candidateStatus; ?>";
    var invalidFile = "<?php echo $invalidFile; ?>";
</script>
