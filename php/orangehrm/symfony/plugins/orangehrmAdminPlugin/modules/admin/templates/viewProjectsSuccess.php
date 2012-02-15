<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */
?>
<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.8.13.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>
<?php use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css'); ?>
<?php use_javascript('../../../scripts/jquery/jquery.autocomplete.js'); ?>
<?php use_stylesheet('../orangehrmAdminPlugin/css/viewProjectsSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/viewProjectsSuccess'); ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div id="searchProject">
    <div class="outerbox">
        <div class="mainHeading"><h2 id="searchProjectHeading"><?php echo __("Projects"); ?></h2></div>
        <form name="frmSearchProject" id="frmSearchProject" method="post" action="<?php echo url_for('admin/viewProjects'); ?>" >
            <?php echo $form['_csrf_token']; ?>

            <br class="clear"/>
            <div id="customer" class="contentDiv">
                <?php echo $form['customer']->renderLabel(__('Customer Name')); ?>
                <?php echo $form['customer']->render(array("class" => "txtBox")); ?>
                <br class="clear"/>
            </div>

            <div id="project" class="contentDiv">
                <?php echo $form['project']->renderLabel(__('Project')); ?>
                <?php echo $form['project']->render(array("class" => "txtBox")); ?>
                <br class="clear"/>
            </div>

            <div id="projectAdmin" class="contentDiv">
                <?php echo $form['projectAdmin']->renderLabel(__('Project Admin')); ?>
                <?php echo $form['projectAdmin']->render(array("class" => "txtBox")); ?>
                <br class="clear"/>
            </div>
            <br class="clear"/>
            <br class="clear"/>
            <div class="actionbar" style="border-top: 1px solid #FAD163; margin-top: 3px">
                <div class="actionbuttons">
                    <input type="button" class="searchbutton" name="btnSave" id="btnSearch"
                           value="<?php echo __("Search"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                    <input type="button" class="resetbutton" name="btnReset" id="btnReset"
                           value="<?php echo __("Reset"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                </div>
                <br class="clear"/>
            </div>
            <br class="clear"/>
        </form>
    </div>
</div>
<div id="customerList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
            </div>

            <form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('admin/viewProjects'); ?>">
                <input type="hidden" name="pageNo" id="pageNo" value="<?php //echo $form->pageNo;                 ?>" />
                <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
            </form>

<!-- confirmation box -->
<div id="deleteConfirmation" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>" style="display: none;">

   <?php echo __(CommonMessages::DELETE_CONFIRMATION); ?>

    <div class="dialogButtons">
        <input type="button" id="dialogDeleteBtn" class="savebutton" value="<?php echo __('Ok'); ?>" />
        <input type="button" id="dialogCancelBtn" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>

            <script type="text/javascript">
                function submitPage(pageNo) {

                    document.frmHiddenParam.pageNo.value = pageNo;
                    document.frmHiddenParam.hdnAction.value = 'paging';
                    document.getElementById('frmHiddenParam').submit();

                }
                var addProjectUrl = '<?php echo url_for('admin/saveProject'); ?>';
                var viewProjectUrl = '<?php echo url_for('admin/viewProjects'); ?>';
                var customers = <?php echo str_replace('&#039;', "'", $form->getCustomerListAsJson()) ?> ;
                var customersArray = eval(customers);
                var projects = <?php echo str_replace('&#039;', "'", $form->getProjectListAsJson()) ?> ;
                var projectsArray = eval(projects);
                var projectAdmins = <?php echo str_replace('&#039;', "'", $form->getProjectAdminListAsJson()) ?> ;
                var projectAdminsArray = eval(projectAdmins);
                var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
</script>