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
 */
?>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.js')?>"></script>

<link href="<?php echo public_path('../../themes/orange/css/jquery/jquery.autocomplete.css')?>" rel="stylesheet" type="text/css"/>

<?php echo stylesheet_tag('../orangehrmCoreLeavePlugin/css/viewLeaveSummarySuccess');?>

<?php if (isset($form->recordsCount) && $form->recordsCount == 0 && isset($searchFlag) && $searchFlag == 1) { ?>
<div class="messageBalloon_notice" style="width:730px;"><?php echo __('No Results Found for This Criteria'); ?></div>
<?php } ?>

<form id="frmLeaveSummarySearch" name="frmLeaveSummarySearch" method="post" action="<?php echo url_for('coreLeave/viewLeaveSummary'); ?>">
<div class="outerbox" style="width: 800px;">
<div class="mainHeading"><h2>Leave Summary</h2></div>

<div class="searchbar">
<?php if ($form->userType == 'Admin' || $form->userType == 'Supervisor') { ?>

<table id="tblSearchCriteria" border="0">
<tr>
    <td><?php echo __('Leave Period'); ?></td>
    <td><?php echo $form['cmbLeavePeriod']->render(); ?></td>
    <td><?php echo __('Leave Type'); ?></td>
    <td><?php echo $form['cmbLeaveType']->render(); ?></td>
</tr>
<tr>
    <td><?php echo __('Employee'); ?></td>
    <td><?php echo $form['txtEmpName']->render(); ?>
        <div class="typeHint"><?php echo __('Type for hints...'); ?></div>
        <?php echo $form['cmbEmpId']->render(); ?>
    </td>
    <td><?php echo __('Job Title'); ?></td>
    <td><?php echo $form['cmbJobTitle']->render(); ?></td>
</tr>
<tr>
    <td><?php echo __('Location'); ?></td>
    <td><?php echo $form['cmbLocation']->render(); ?></td>
    <td><?php echo __('Sub Unit'); ?></td>
    <td><?php echo $form['cmbSubDivision']->render(); ?></td>
</tr>
<tr>
    <td><?php echo __('Records Per Page'); ?></td>
    <td colspan="3"><?php echo $form['cmbRecordsCount']->render(); ?></td>
</tr>
</table>
    <div class="formbuttons">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo; ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
    <input type="button" name="btnSearch" id="btnSearch" value="Search" class="savebutton" />
    <input type="reset" value="Reset" class="savebutton" />
        </div>
<?php } elseif ($form->userType == 'ESS') { ?>
    <table id="tblSearchCriteria" border="0">
<tr>
    <td width="75"><?php echo __('Leave Period'); ?></td>
    <td width="170"><?php echo $form['cmbLeavePeriod']->render(); ?></td>
    <td width="60"><?php echo __('Leave Type'); ?></td>
    <td width="75"><?php echo $form['cmbLeaveType']->render(); ?></td>
</tr>
<tr>
    <td><?php echo __('Records per page'); ?></td>
    <td colspan="3"><?php echo $form['cmbRecordsCount']->render(); ?></td>
</tr>
</table>
<div class="formbuttons">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo; ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
    <input type="button" name="btnSearch" id="btnSearch" value="Search" class="savebutton" />
        </div>
<?php } // Search criteria table ?>

<?php echo $form['_csrf_token']; ?>

<!--</form>--> <!-- End of frmLeaveSummarySearch -->

</div> <!-- End of searchbar -->
</div> <!-- End of outerbox -->




<?php if ($form->saveSuccess) { ?>
<div class="messageBalloon_success"><?php echo __('Leave Entitlements Successfully Saved'); ?></div>
<?php } ?>
<div id="validationMsg"></div>
<?php if ($form->recordsCount > 0) { ?>

<div class="outerbox">

<?php if ($form->pager->haveToPaginate()) { ?>
<div class="navigationHearder">
<div class="pagingbar">
<?php include_partial('global/paging_links_js', array('pager' => $form->pager));?>
</div>
<br class="clear" />
</div>
<?php } ?>

<!--<form id="frmLeaveSummaryResult" name="frmLeaveSummaryResult" method="post" action="">-->
<table border="0" cellpadding="0" cellspacing="0" class="data-table">

<thead>
<tr>
    <td valign="top"><?php echo __('Employee Name'); ?></td>
    <td valign="top"><?php echo __('Leave Type'); ?></td>
<td><?php echo __('Leave Entitled'); ?><div class="daysMove">(Days)</div></td>
<td><?php echo __('Leave Scheduled'); ?><div class="daysMove">(Days)</div></td>
<td><?php echo __('Leave Taken'); ?><div class="daysMove">(Days)</div></td>
<td><?php echo __('Leave Balance'); ?><div class="daysMove">(Days)</div></td>
</tr>
</thead>

<?php echo $form->getLeaveSummaryTbodyHtml($employeeId);?>

</table>



<?php if ($form->leaveSummaryEditMode) { ?>
<div class="formbuttons">
   <input type="button" class="savebutton" id="btnSave" value="Edit"/>
<input type="reset" class="resetbutton" value="Reset" id="summaryReset" />
</div>
<?php } // if ($form->leaveSummaryEditMode) ?>

</div> <!-- End of outerbox -->

</form> <!-- End of frmLeaveSummarySearch -->

<?php } // End of if ($recordsCount > 0) ?>







<script type="text/javascript">

/* Define language strings here */
var lang_not_numeric = '<?php echo __('Enter a Positive Number Less Than 365 with Two Decimal Places'); ?>';

/* Employee list */

var empdata = <?php echo str_replace('&#039;',"'",$form->getEmployeeListAsJson());?>;


function submitPage(pageNo) {

    document.frmLeaveSummarySearch.pageNo.value = pageNo;
    document.frmLeaveSummarySearch.hdnAction.value = 'paging';
    document.getElementById('frmLeaveSummarySearch').submit();

}


</script>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/jquery.autocomplete.js')?>"></script>

<?php echo javascript_include_tag('../orangehrmCoreLeavePlugin/js/viewLeaveSummarySuccess'); ?>

