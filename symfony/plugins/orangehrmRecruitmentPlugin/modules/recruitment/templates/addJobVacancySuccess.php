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

<?php
use_stylesheet(plugin_web_path('orangehrmRecruitmentPlugin', 'css/addJobVacancySuccess'));
use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/addJobVacancySuccess'));
?>
<?php if(($vacancyPermissions->canRead() && isset($vacancyId)) || ($vacancyPermissions->canCreate() && empty($vacancyId))){?>
<div class="box" id="addJobVacancy">

    <div class="head">
        <h1><?php echo isset($vacancyId) ? __('Edit Job Vacancy') : __('Add Job Vacancy'); ?></h1>
    </div>

    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <form name="frmAddJobVacancy" id="frmAddJobVacancy" method="post">

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form["hiringManagerId"]->render(); ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['jobTitle']->renderLabel(__('Job Title') . ' <em>*</em>'); ?>
                        <?php echo $form['jobTitle']->render(array("maxlength" => 50)); ?>
                    </li>
                    <li>
                        <?php echo $form['name']->renderLabel(__('Vacancy Name') . ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("maxlength" => 50)); ?>
                    </li>
                    <li>
                        <?php echo $form['hiringManager']->renderLabel(__('Hiring Manager') . ' <em>*</em>'); ?>
                        <?php echo $form['hiringManager']->render(array("maxlength" => 100)); ?>
                    </li>
                    <li>
                        <?php echo $form['noOfPositions']->renderLabel(__('Number of Positions')); ?>
                        <?php echo $form['noOfPositions']->render(array("maxlength" => 2)); ?>
                    </li>
                    <li class="largeTextBox">
                        <?php echo $form['description']->renderLabel(__('Description')); ?>
                        <?php echo $form['description']->render(array("cols" => 30, "rows" => 9)); ?>
                    </li>
                    <li>
                        <?php echo $form['status']->renderLabel(__('Active')); ?>
                        <?php echo $form['status']->render(); ?>
                    </li>
                    <!-- TODO: See whether this div is used in any addon
                    <li><div class="publishJobVacancySeparator">&nbsp;</div></li>
                    -->
                    <li class="labelRight">
                        <?php echo $form['publishedInFeed']->render(); ?>
                        <?php echo $form['publishedInFeed']->renderLabel(__('Publish in RSS feed(1) and web page(2)')); ?>
                    </li>

                    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_before_navigation_bar_1')) ?>

                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                    <li class="helpText">
                        1 : <?php echo __('RSS Feed URL') ?> : <?php echo link_to(null, 'recruitmentApply/jobs.rss', array('absolute' => true, 'target' => '_new')); ?>
                    </li>
                    <li class="helpText">
                        2 : <?php echo __('Web Page URL') ?> : <?php echo link_to(null, 'recruitmentApply/jobs.html', array('absolute' => true, 'target' => '_new')); ?>
                    </li>
                </ol>
                <p>
                    <?php if (isset($vacancyId)) { ?>
                        <?php if($vacancyPermissions->canUpdate() && isset($vacancyId)){?>
                        <input type="button" class="savebutton" name="btnSave" id="btnSave" value="<?php echo __("Edit"); ?>"/>
                        <?php }?>
                        <input type="button" class="cancel" name="btnBack" id="btnBack" value="<?php echo __("Back"); ?>"/>
                    <?php } else { ?>
                        <?php if($vacancyPermissions->canCreate() && empty($vacancyId)){?>
                        <input type="button" class="savebutton" name="btnSave" id="btnSave"value="<?php echo __("Save"); ?>"/>
                    <?php }} ?>
                </p>
            </fieldset>
        </form>
    </div>
    <?php
    if (isset($vacancyId)) {
        echo include_component('recruitment', 'attachments', array('id' => $vacancyId, 'screen' => JobVacancy::TYPE, 'permissions' => $vacancyPermissions));
    }
    ?>
</div>
<?php }?>
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
