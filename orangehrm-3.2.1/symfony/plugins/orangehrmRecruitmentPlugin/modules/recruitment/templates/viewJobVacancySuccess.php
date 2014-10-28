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
<?php if($vacancyPermissions->canRead()){?>
<div class="box searchForm toggableForm" id="srchVacancy">
    
    <div class="head">
        <h1><?php echo __('Vacancies'); ?></h1>
    </div>
    
    <div class="inner">

        <form name="frmSrchJobVacancy" id="frmSrchJobVacancy" method="post" action="<?php echo url_for('recruitment/viewJobVacancy'); ?>">
            <fieldset>
                <?php echo $form['_csrf_token']; ?>
                <ol>
                    <li>
                        <?php echo $form['jobTitle']->renderLabel(__('Job Title'), array("class" => "jobTitleLabel")); ?>
                        <?php echo $form['jobTitle']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                    </li>
                    <li>
                        <?php echo $form['jobVacancy']->renderLabel(__('Vacancy'), array("class" => "vacancyLabel")); ?>
                        <?php echo $form['jobVacancy']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                    </li>
                    <li>
                        <?php echo $form['hiringManager']->renderLabel(__('Hiring Manager'), array("class" => "hiringManagerLabel")); ?>
                        <?php echo $form['hiringManager']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                    </li>
                    <li>
                        <?php echo $form['status']->renderLabel(__('Status'), array("class" => "statusLabel")); ?>
                        <?php echo $form['status']->render(array("class" => "drpDown", "maxlength" => 50)); ?>
                    </li>
                </ol>

                <p>
                    <input type="button" id="btnSrch" value="<?php echo __("Search") ?>" name="btnSrch" />
                    <input type="button" class="reset" id="btnRst" value="<?php echo __("Reset") ?>" name="btnSrch" />                    
                </p>
            </fieldset>            
        </form>
    </div>
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>

<?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>

<!-- Confirmation box HTML: Begins -->
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

<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('recruitment/viewJobVacancy'); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php //echo $form->pageNo;         ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>
<?php }?>
<script type="text/javascript">

    function submitPage(pageNo) {

        document.frmHiddenParam.pageNo.value = pageNo;
        document.frmHiddenParam.hdnAction.value = 'paging';
        document.getElementById('frmHiddenParam').submit();

    }
    //<![CDATA[
    var addJobVacancyUrl = '<?php echo url_for('recruitment/addJobVacancy'); ?>';
    var vacancyListUrl = '<?php echo url_for('recruitment/getVacancyListForJobTitleJson?mode=' . getVacancyListForJobTitleJsonAction::MODE_VACANCIES . '&jobTitle='); ?>';
    var hiringManagerListUrlForJobTitle = '<?php echo url_for('recruitment/getHiringManagerListJson?mode=' . getVacancyListForJobTitleJsonAction::MODE_VACANCIES . '&jobTitle='); ?>';
    var hiringManagerListUrlForVacancyId = '<?php echo url_for('recruitment/getHiringManagerListJson?mode=' . getVacancyListForJobTitleJsonAction::MODE_VACANCIES . '&vacancyId='); ?>';
    var lang_all = '<?php echo __("All") ?>';
    //]]>
</script>