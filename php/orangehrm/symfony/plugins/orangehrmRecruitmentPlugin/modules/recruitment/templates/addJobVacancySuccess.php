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

<?php use_stylesheet('../orangehrmRecruitmentPlugin/css/addJobVacancySuccess'); ?>
<?php use_javascript('../orangehrmRecruitmentPlugin/js/addJobVacancySuccess'); ?>
<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>

<?php $browser = $_SERVER['HTTP_USER_AGENT']; ?>
<?php if (strstr($browser, "MSIE 8.0")): ?>
<?php $drpDownWidth = 'width: 268px' ?>
<?php $textBoxWidth = 'width: 263px' ?>
<?php else: ?>
<?php $drpDownWidth = 'width: 270px' ?>
<?php $textBoxWidth = 'width: 260px' ?>
<?php endif; ?>
        <?php echo isset($templateMessage)?templateMessage($templateMessage):''; ?>
        <div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
            <span><?php echo isset($message) ? $message : ''; ?></span>
        </div>

        <div id="addJobVacancy">
            <div class="outerbox">
        <?php if (isset($vacancyId)) {
        ?>
            <div class="mainHeading"><h2 id="addJobVacancHeading"><?php echo __('Edit Job Vacancy'); ?></h2></div>
        <?php } else {
        ?>
            <div class="mainHeading"><h2 id="addJobVacancHeading"><?php echo __('Add Job Vacancy'); ?></h2></div>
        <?php } ?>
        <form name="frmAddJobVacancy" id="frmAddJobVacancy" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form["hiringManagerId"]->render(); ?>
            <br class="clear"/>

            <?php echo $form['jobTitle']->renderLabel(__('Job Title') . ' <span class="required">*</span>'); ?>
            <?php echo $form['jobTitle']->render(array("class" => "drpDown", "maxlength" => 50, "style" => $drpDownWidth)); ?>
            <br class="clear"/>

            <?php echo $form['name']->renderLabel(__('Vacancy Name') . ' <span class="required">*</span>'); ?>
            <?php echo $form['name']->render(array("class" => "formInput", "maxlength" => 50, "style" => $textBoxWidth)); ?>
            <br class="clear"/>

            <?php echo $form['hiringManager']->renderLabel(__('Hiring Manager') . ' <span class="required">*</span>'); ?>

            <?php echo $form['hiringManager']->render(array("class" => "formInput", "maxlength" => 100, "style" => $textBoxWidth)); ?>

            <br class="clear"/>

            <?php echo $form['noOfPositions']->renderLabel(__('Number of Positions')); ?>
            <?php echo $form['noOfPositions']->render(array("class" => "formInput", "maxlength" => 2)); ?>
            <br class="clear"/>

            <?php echo $form['description']->renderLabel(__('Description')); ?>
            <?php echo $form['description']->render(array("class" => "formInputText", "cols" => 30, "rows" => 9)); ?>
            <br class="clear"/>

            <?php echo $form['status']->renderLabel(__('Active')); ?>
            <?php echo $form['status']->render(array("class" => "formSelect")); ?>
            <br class="clear"/>
            
            <hr class="publishJobVacancySeparator" />
            
            <div class="publishJobVacancy" id="publishJobVacancy">                
                <?php echo $form['publishedInFeed']->render(array("class" => "formSelect")); ?>
                <?php echo $form['publishedInFeed']->renderLabel(__('Publish in RSS feed(1) and web page(2)')); ?>
                <br class="clear"/>
            </div>
            
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_before_navigation_bar_1'))?>
            
            <div class="formbuttons">
                <?php if (isset($vacancyId)) { ?>
                    <input type="button" class="savebutton" name="btnSave" id="btnSave"
                           value="<?php echo __("Edit"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                    <input type="button" class="backbutton" name="btnBack" id="btnBack"
   value="<?php echo __("Back"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>

<?php } else { ?>
                <input type="button" class="savebutton" name="btnSave" id="btnSave"
                       value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
<?php } ?>
            </div>
        </form>
    </div>
</div>
 <div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
 <div class="paddingLeftRequired"><span>1 </span>: <?php echo __('RSS Feed URL') ?> <span>:</span> <?php echo link_to(null, 'recruitmentApply/jobs.rss', array('absolute' => true, 'target' => '_new'));?></div>
 <div class="paddingLeftRequired"><span>2 </span>: <?php echo __('Web Page URL') ?> <span>:</span> <?php echo link_to(null, 'recruitmentApply/jobs.html', array('absolute' => true, 'target' => '_new'));?></div>

<?php if (isset($vacancyId)) { ?>
                    <br class="clear"/>
                    <br class="clear"/>
                    <div>
<?php echo include_component('recruitment', 'attachments', array('id' => $vacancyId, 'screen' => JobVacancy::TYPE)); ?>
                </div>
<?php } ?>

                <script type="text/javascript">
                    //<![CDATA[
                    var hiringManagers = <?php echo str_replace('&#039;', "'", $form->getHiringManagerListAsJson()) ?> ;
                    var hiringManagersArray = eval(hiringManagers);
		    var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
                    var lang_negativeAmount = "<?php echo __("Should be a positive number"); ?>";
                    var lang_tooLargeAmount = "<?php echo __("Should be less than %amount%", array("%amount%" => '99')); ?>";
                    var lang_jobTitleRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                    var lang_vacancyNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                    var lang_enterAValidEmployeeName = "<?php echo __(ValidationMessages::INVALID); ?>";
                    var lang_nameExistmsg = "<?php echo __("Already exists"); ?>";
                    var vacancyNames = <?php echo $form->getVacancyList(); ?>;
                    var vacancyNameList = eval(vacancyNames);
                    var lang_edit = "<?php echo __("Edit"); ?>";
                    var lang_save = "<?php echo __("Save"); ?>";
                    var lang_cancel = "<?php echo __("Cancel"); ?>";
                    var lang_back = "<?php echo __("Back"); ?>";
                    var linkForAddJobVacancy = "<?php echo url_for('recruitment/addJobVacancy'); ?>";
                    var lang_descriptionLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 40000)) ?>";
		    var backBtnUrl = '<?php echo url_for('recruitment/viewJobVacancy?'); ?>';
		    var backCancelUrl = '<?php echo url_for('recruitment/addJobVacancy?'); ?>';
<?php if (isset($vacancyId)) { ?>
                    var vacancyId = '<?php echo $vacancyId; ?>';
<?php } else { ?>
                    var vacancyId = "";
<?php } ?>
//]]>
</script>
