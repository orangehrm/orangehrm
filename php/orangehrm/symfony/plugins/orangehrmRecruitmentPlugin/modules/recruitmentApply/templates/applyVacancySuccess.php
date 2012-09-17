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
<?php use_stylesheet('../orangehrmRecruitmentPlugin/css/applyVacancySuccess'); ?>
<?php use_javascript('../orangehrmRecruitmentPlugin/js/applyVacancySuccess'); ?>
<?php $browser = $_SERVER['HTTP_USER_AGENT']; ?>
<?php if (strstr($browser, "MSIE 8.0")): ?>
    <?php $keyWrdWidth = 'width: 276px' ?>
    <?php $resumeWidth = 37 ?>
<?php else: ?>
    <?php $keyWrdWidth = 'width: 271px' ?>
    <?php $resumeWidth = 38; ?>
<?php endif; ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
	<span><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div id="addCandidate">
    <div class="outerbox" style="width:800px">

        <div class="mainHeading"><h2 id="addCandidateHeading"><?php echo __("Apply for") . " " . $name; ?></h2></div>
        
        <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_1')) ?>
        
        <form name="frmAddCandidate" id="frmAddCandidate" method="post" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form["vacancyList"]->render(); ?>

            <br class="clear"/>

            <div class="description">
                <div style="float:left"><label><?php echo __('Description'); ?><span  id="extend">[+]</span></label></div>
                <br class="clear"/>
                <div id="description">
                    <textarea id="txtArea" cols="88" rows="1" onkeyup="expandtextarea(this)"><?php echo $description ?></textarea>
                </div>
            </div>
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
            
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_2')) ?>
            
            <br class="clear"/>
            <br class="clear"/>
            <div class="newColumn">
                <?php echo $form['email']->renderLabel(__('Email'). ' <span class="required">*</span>'); ?>
                <?php echo $form['email']->render(array("class" => "formInputText")); ?>
                <div class="errorHolder below"></div>
            </div>
            <div class="newColumn">
                <?php echo $form['contactNo']->renderLabel(__('Contact No'), array("class " => "contactNoLable")); ?>
                <?php echo $form['contactNo']->render(array("class" => "contactNo")); ?>
                <div class="errorHolder cntact"></div>
            </div>
            <br class="clear" />

            <div class="hrLine" >&nbsp;</div>
            <br class="clear" />
            <div class="resumeDiv">

                <?php
                if ($candidateId == "") {
                    echo $form['resume']->renderLabel(__('Resume'). '<span class="required">*</span>', array("class " => "resume"));
                    echo $form['resume']->render(array("class " => "duplexBox", "size" => $resumeWidth));
                    echo "<div class=\"errorHolder below\"></div><br class=\"clear\"/>";
                    echo "<span id=\"cvHelp\" class=\"helpText\">" . __(CommonMessages::FILE_LABEL_DOC) . "</span>";
                } else {
		    echo "<span class=\"resumeUp\">".__('Resume')."</span>";
                    echo "<span class=\"fileLink\">".__('Uploaded')."</span>";

                }
                ?>
	    </div>
            <br class="clear"/>
            <div>
                <?php echo $form['keyWords']->renderLabel(__('Keywords'), array("class " => "keywrd")); ?>
                <?php echo $form['keyWords']->render(array("class" => "keyWords")); ?>
                <div class="errorHolder below"></div>
            </div>
            <br class="clear" />
            <div>
                <?php echo $form['comment']->renderLabel(__('Notes'), array("class " => "comment")); ?>
                <?php echo $form['comment']->render(array("class" => "formInputText","id" => "notes", "cols" => 43, "rows" => 4)); ?>
                <div class="errorHolder below"></div>
            </div>
            <br class="clear" />
            
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_3')) ?>
            
            <div class="formbuttons">
                <input type="button" class="savebutton" name="btnSave" id="btnSave"
                       value="<?php echo __("Submit"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/><span id="backLink"><?php echo __("Back to Job List"); ?></span>
            </div>

        </form>
    </div>
    <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
</div>

<script type="text/javascript">
    //<![CDATA[
    var description	= '<?php $description; ?>';
    var vacancyId	= '<?php echo $vacancyId; ?>';
    var candidateId	= '<?php echo ($candidateId !="") ? $candidateId : 0;?>';
    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_emailRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var lang_tooLargeInput = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 30)); ?>";
    var lang_commaSeparated = "<?php echo __("Enter comma separated words") . '...'; ?>";
    var lang_validPhoneNo = "<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>";
    var lang_noMoreThan250 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_resumeRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var linkForApplyVacancy = "<?php echo url_for('recruitmentApply/applyVacancy'); ?>";
    var linkForViewJobs = "<?php echo url_for('recruitmentApply/viewJobs'); ?>";
    var lang_back = "<?php echo __("Go to Job Page")?>";
	
</script>
