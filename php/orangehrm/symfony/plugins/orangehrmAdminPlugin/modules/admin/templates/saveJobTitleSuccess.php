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

<?php use_stylesheet('../orangehrmAdminPlugin/css/saveJobTitleSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/saveJobTitleSuccess'); ?>
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="saveHobTitle">
    <div class="outerbox">
        <?php $heading = (empty($form->jobTitleId)) ? __("Add Job Title") : __("Edit Job Title") ?>
        <div class="mainHeading"><h2 id="saveHobTitleHeading"><?php echo $heading; ?></h2></div>
        <form name="frmSavejobTitle" id="frmSavejobTitle" method="post" action="<?php echo url_for('admin/saveJobTitle?jobTitleId=' . $form->jobTitleId); ?>" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
            <br class="clear"/>

            <?php echo $form['jobTitle']->renderLabel(__('Job Title') . ' <span class="required">*</span>'); ?>
            <?php echo $form['jobTitle']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
            <br class="clear"/>

            <?php echo $form['jobDescription']->renderLabel(__('Job Description')); ?>
            <?php echo $form['jobDescription']->render(array("class" => "formInputTextArea", "maxlength" => 400)); ?>
            <div class="errorHolder"></div>
            <br class="clear"/>

            <?php
            if (empty($form->attachment->id)) {
                echo $form['jobSpec']->renderLabel(__('Job Specification'), array("class " => "formInputFileUpload"));
                echo $form['jobSpec']->render(array("class " => "duplexBox", "size" => 32));
                echo "<br class=\"clear\"/>";
                echo "<span id=\"cvHelp\" class=\"helpText\">" . __(CommonMessages::FILE_LABEL_SIZE) . "</span>";
                echo "<br class=\"clear\"/>";
            } else {
                $attachment = $form->attachment;
                $linkHtml = "<div id=\"fileLink\"><a target=\"_blank\" class=\"fileLink\" href=\"";
                $linkHtml .= url_for('admin/viewJobSpec?attachId=' . $attachment->getId());
                $linkHtml .= "\">{$attachment->getFileName()}</a></div>";

                echo $form['jobSpecUpdate']->renderLabel(__('Job Specification'));
                echo $linkHtml;
                echo "<br class=\"clear\"/>";
                echo "<div id=\"radio\">";
                echo $form['jobSpecUpdate']->render(array("class" => ""));
                echo "<br class=\"clear\"/>";
                echo "</div>";
                echo "<div id=\"fileUploadSection\">";
                echo $form['jobSpec']->renderLabel(' ');
                echo $form['jobSpec']->render(array("class " => "duplexBox", "size" => 32));
                echo "<br class=\"clear\"/>";
                echo "<span id=\"cvHelp\" class=\"helpText\">" . __(CommonMessages::FILE_LABEL_SIZE) . "</span>";
                echo "</div>";
            }
            ?>

            <?php echo $form['note']->renderLabel(__('Note')); ?>
            <?php echo $form['note']->render(array("class" => "formInputTextArea", "maxlength" => 400)); ?>
            <div class="errorHolder"></div>
            <br class="clear"/>

            <div class="formbuttons">
                <input type="button" class="savebutton" name="btnSave" id="btnSave"
                       value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                <input type="button" class="cancelbutton" name="btnCancel" id="btnCancel"
                       value="<?php echo __("Cancel"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
            </div>

        </form>
    </div>
</div>

<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>

<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_jobTitleRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var viewJobTitleListUrl = '<?php echo url_for('admin/viewJobTitleList?jobTitleId='.$form->jobTitleId); ?>';
    var jobTitleId = '<?php echo $form->jobTitleId; ?>';
    var lang_exceed400Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 400)); ?>';
    var jobTitles = <?php echo str_replace('&#039;', "'", $form->getJobTitleListAsJson()) ?> ;
    var jobTitleList = eval(jobTitles);
    var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    //]]>
</script>
