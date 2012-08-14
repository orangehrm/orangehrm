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
<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>
<?php use_stylesheet('../orangehrmRecruitmentPlugin/css/addCandidateSuccess'); ?>
<?php use_javascript('../orangehrmRecruitmentPlugin/js/addCandidateSuccess'); ?>
<?php $browser = $_SERVER['HTTP_USER_AGENT']; ?>
<?php if (strstr($browser, "MSIE 8.0")): ?>
<?php $keyWrdWidth = 'width: 276px' ?>
<?php $resumeWidth = 37 ?>
<?php else: ?>
<?php $keyWrdWidth = 'width: 271px' ?>
<?php $resumeWidth = 38; ?>
<?php endif; ?>
<?php $title = ($candidateId > 0) ? __('Candidate') : __('Add Candidate'); ?>
<?php
        $allVacancylist[] = array("id" => "", "name" => __('-- Select --'));
        $allowedVacancylist[] = array("id" => "", "name" => __('-- Select --'));
        $allowedVacancylistWithClosedVacancies[] = array("id" => "", "name" => __('-- Select --'));
        $allowedVacancyIdArray[] = array();
        $closedVacancyIdArray[] = array();
        foreach ($jobVacancyList as $vacancy) {
            $newVacancyId = $vacancy['id'];
            $newVacancyName = ($vacancy['status'] == JobVacancy::CLOSED) ? $vacancy['name'] . " (".__('Closed').")" : $vacancy['name'];
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
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>

        <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
            <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
        </div>

        <div id="addCandidate">
            <div class="outerbox">

                <div class="mainHeading"><h2 id="addCandidateHeading"><?php echo $title; ?></h2></div>
                <form name="frmAddCandidate" id="frmAddCandidate" method="post" action="<?php echo url_for('recruitment/addCandidate?id=' . $candidateId); ?>" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
            <br class="clear"/>

            <div class="nameColumn" id="firstNameDiv">
                <label><?php echo __('Full Name'); ?></label>
            </div>
            <div class="column">
                <?php echo $form['firstName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                <div class="errorHolder"></div>
                <br class="clear"/>
                <label id="frmDate" class="helpText"><?php echo __('First Name'); ?><span class="required">*</span></label>
            </div>
            <div class="column" id="middleNameDiv">
                <?php echo $form['middleName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                <div class="errorHolder"></div>
                <br class="clear"/>
                <label id="toDate" class="helpText"><?php echo __('Middle Name'); ?></label>
            </div>
            <div class="column" id="middleNameDiv">
                <?php echo $form['lastName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                <div class="errorHolder"></div>
                <br class="clear"/>
                <label id="toDate" class="helpText"><?php echo __('Last Name'); ?><span class="required">*</span></label>
            </div>
            <br class="clear"/>
            <br class="clear"/>
            <div class="newColumn">
                <?php echo $form['email']->renderLabel(__('Email') . ' <span class="required">*</span>'); ?>
                <?php echo $form['email']->render(array("class" => "formInputText")); ?>
                <div class="errorHolder below"></div>
            </div>
            <div class="newColumn">
                <?php echo $form['contactNo']->renderLabel(__('Contact No'), array("class " => "contactNoLable")); ?>
                <?php echo $form['contactNo']->render(array("class" => "contactNo")); ?>
                <div class="errorHolder cntact"></div>
            </div>
            <br class="clear" />

            <div class="hrLine" ></div>

            <div class="newColumn">
                <?php echo $form['vacancy']->renderLabel(__('Job Vacancy'),array("class" => "vacancyDrpLabel")); ?>
                <?php echo $form['vacancy']->render(array("class" => "vacancyDrp")); ?>
                <div class="errorHolder vacancy"></div>
            </div>

            <?php if ($candidateId > 0) : ?>
            <?php $existingVacancyList = $actionForm->candidate->getJobCandidateVacancy(); ?>
            <?php if ($existingVacancyList[0]->getVacancyId() > 0) : ?>
                        <div id="actionPane" style="float:left; width:400px; padding-top:0px">
                <?php $i = 0 ?>
                <?php foreach ($existingVacancyList as $candidateVacancy) {
                ?>
                            <div id="<?php echo $i ?>" style="height: 18px; padding-top: 11px">
                    <?php
                                $widgetName = $candidateVacancy->getId();
                                echo $actionForm[$widgetName]->render(array("class" => "actionDrpDown"));
                    ?>
                                <span class="status" style="font-weight: bold"><?php echo __("Status") . ": " . __(ucwords(strtolower($candidateVacancy->getStatus()))); ?></span>
                    <?php
                            }
                            $i++;
                    ?></div>
                        <br class="clear" />
                <?php //} ?>
                    </div>
            <?php endif; ?>
            <?php endif; ?>
                        <br class="clear" />            
                        <!-- Resume block : Begins -->
                        <div>

                <?php
                        if ($form->attachment == "") {
                            echo $form['resume']->renderLabel(__('Resume'), array("class " => "resume"));
                            echo $form['resume']->render(array("class " => "duplexBox", "size" => $resumeWidth));
                            echo "<br class=\"clear\"/>";
                            echo "<span id=\"cvHelp\" class=\"helpText\">" . __(CommonMessages::FILE_LABEL_DOC) . "</span>";
                        } else {
                            $attachment = $form->attachment;
                            $linkHtml = "<div id=\"fileLink\"><a target=\"_blank\" class=\"fileLink\" href=\"";
                            $linkHtml .= url_for('recruitment/viewCandidateAttachment?attachId=' . $attachment->getId());
                            $linkHtml .= "\">{$attachment->getFileName()}</a></div>";

                            echo $form['resumeUpdate']->renderLabel(__('Resume'));
                            echo $linkHtml;
                            echo "<br class=\"clear\"/>";
                            echo "<div id=\"radio\">";
                            echo $form['resumeUpdate']->render(array("class" => ""));
                            echo "<br class=\"clear\"/>";
                            echo "</div>";
                            echo "<div id=\"fileUploadSection\">";
                            echo $form['resume']->renderLabel(' ');
                            echo $form['resume']->render(array("class " => "duplexBox", "size" => $resumeWidth));
                            echo "<br class=\"clear\"/>";
                            echo "<span id=\"cvHelp\" class=\"helpText\">" . __(CommonMessages::FILE_LABEL_DOC) . "</span>";
                            echo "</div>";
                        }
                ?>
                    </div>

                    <!-- Resume block : Ends -->

                    <div>
                <?php echo $form['keyWords']->renderLabel(__('Keywords'), array("class " => "keywrd")); ?>
                <?php echo $form['keyWords']->render(array("class" => "keyWords", "style" => $keyWrdWidth)); ?>
                        <div class="errorHolder below"></div>
                    </div>
                    <br class="clear" />
                    <div>
                <?php echo $form['comment']->renderLabel(__('Comment'), array("class " => "comment")); ?>
                <?php echo $form['comment']->render(array("class" => "formInputText", "cols" => 35, "rows" => 4)); ?>
                        <div class="errorHolder below"></div>
                    </div>
                    <br class="clear" />
                    <div>
                <?php echo $form['appliedDate']->renderLabel(__('Date of Application'), array("class " => "appDate")); ?>
                <?php echo $form['appliedDate']->render(array("class" => "formDateInput")); ?>
                        <div class="errorHolder below"></div>
                    </div>
                    <br class="clear" />
                    <div class="formbuttons">
                <?php if ($edit): ?>
                            <input type="button" class="savebutton" name="btnSave" id="btnSave"
                                   value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                       <?php endif; ?>
                       <?php if ($candidateId > 0): ?>
                         <input type="button" class="backbutton" name="btnBack" id="btnBack"
                                value="<?php echo __("Back"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                       <?php endif; ?>
                     </div>
                 </form>
             </div>
         </div>
         <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
         <br class="clear" />
         <br class="clear" />

<?php if ($candidateId > 0) : ?>
<?php $existingVacancyList = $actionForm->candidate->getJobCandidateVacancy(); ?>
                                    <div id="candidateHistoryResults">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
                                </div>
<?php endif; ?>

                                    <!-- confirmation box for removing vacancies-->
                                    <div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">
                                        <?php echo __("Remove vacancy?"); ?>
                                    <div class="dialogButtons">
                                        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Ok'); ?>" />
                                        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
                                    </div>
                                </div>

                                <!-- confirmation box for remove vacancies & save -->
                                <div id="deleteConfirmationForSave" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">
                                    <?php echo __("This action will remove previous vacancy"); ?>
                                    <br /><br />
                                    <?php echo __("Remove?"); ?>
                                    <div class="dialogButtons">
                                        <input type="button" id="dialogSaveButton" class="savebutton" value="<?php echo __('Ok'); ?>" />
                                        <input type="button" id="dialogCancelButton" class="savebutton" value="<?php echo __('Cancel'); ?>" />
                                    </div>
                                </div>

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
                                    var lang_commaSeparated = "<?php echo __('Enter comma separated words').'...'; ?>";
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
