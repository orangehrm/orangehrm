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
<script type="text/javascript">
//<![CDATA[
	function onSearch() {

		var selectObj = $('empsearch_search_by');

		if (selectObj.value == -1) {

			//alert("<?php //echo $lang_Common_SelectField; ?>");
			alert('<?php echo __("Please select a field to search")?>');
			selectObj.focus();
			return false;
		} else {
			$('search_form').submit();
			return true;
		}
	}

	function onReset() {
		location.href = "<?php echo url_for('pim/index')?>?_reset=1";
	}

<?php if (true) { //if($this->getArr['reqcode']=='EMP') { ?>
	function onAdd() {

		location.href = "<?php echo url_for('pim/addEmployee') ?>";

	}

	function onDelete() {

		var checkboxes = document.getElementsByName('ids[]');
		var recordSelected = false;
		var checkboxCount = checkboxes.length;

		for (var i=0; i < checkboxCount; i++) {
			if (checkboxes[i].checked) {
				recordSelected = true;
				break;
			}
		}

		if (recordSelected) {
			return true;
		} else {
			alert('<?php echo __("Select at least one record to delete") ?>');
			//alert("<?php //echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
			return false;
		}
	}

<?php } else { ?>
	//function onAdd() {
    //    var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=<?php //echo $this->getArr['reqcode']?>','Employees','height=450,width=400,scrollbars=1');
    //    if(!popup.opener) popup.opener=self;
	//}
<?php } ?>

//]]>
</script>
<div class="outerbox">

<div class="mainHeading">
	<h2><?php echo __("Employee Information") ?></h2>
</div>

<?php if ($form->hasErrors() || $sf_user->hasFlash('success') || $sf_user->hasFlash('error')): ?>
<div class="messagebar">
    <?php include_partial('global/form_errors', array('form' => $form));?>
    <?php include_partial('global/flash_messages', array('sf_user' => $sf_user));?>
</div>
<?php endif; ?>

<div class="searchbox">
<form id="search_form" method="post" action="<?php echo url_for('pim/index'); ?>">
    <?php echo $form['_csrf_token'];
	      echo $form['search_by']->renderLabel(__("Search By").":");
          echo $form['search_by']->render();
          echo $form['search_for']->renderLabel(__("Search For").":");
          echo $form['search_for']->render(); ?>

    <input
    	type="button" class="plainbtn" onclick="onSearch()"
    	onmouseover="this.className='plainbtn plainbtnhov'"
    	onmouseout="this.className='plainbtn'" value="<?php echo __("Search")?>" name="_search" />
    <input
    	type="button" class="plainbtn"
    	onmouseover="this.className='plainbtn plainbtnhov'" onclick="onReset()"
    	onmouseout="this.className='plainbtn'" value="<?php echo __("Reset")?>" name="_reset" />
    <br class="clear" />
</form>
</div>

<form method="post" action="<?php echo url_for('pim/delete');?>" onsubmit="return onDelete()">

<div class="actionbar">
<div class="actionbuttons"><input type="button" class="plainbtn"
	<?php echo (true/*$locrights['add']*/) ? 'onclick="onAdd();"' : 'disabled'; ?>
	onmouseover="this.className='plainbtn plainbtnhov'"
	onmouseout="this.className='plainbtn'" value="<?php echo __("Add")?>" /> <?php if(true) {//$this->getArr['reqcode']=='EMP') { ?>
<input type="submit" class="plainbtn"
	<?php //echo ($locrights['delete']) ? 'onclick="returndelete();"' : 'disabled' ?>
	onmouseover="this.className='plainbtn plainbtnhov'"
	onmouseout="this.className='plainbtn'" value="<?php echo __("Delete")?>" /> <?php } ?></div>
<div class="noresultsbar"><?php //echo (empty($emplist)) ? $norecorddisplay : '';?></div>

<?php if ($pager->haveToPaginate()): ?>
<div class="pagingbar">
    <?php include_partial('global/paging_links', array('pager' => $pager, 'url'=>'@employee_list'));?>
</div>

<?php endif; ?>

<br class="clear" />
</div>
<br class="clear" />
<table cellspacing="0" cellpadding="0" class="data-table">
	<thead>
		<tr>
			<td width="50">&nbsp;&nbsp;&nbsp;</td>
			<td scope="col"><?php echo $sorter->sortLink('employeeId', __('Employee Id'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('fullName', __('Employee Name'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('jobTitle', __('Job Title'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('employeeStatus', __('Employment Status'), '@employee_list', ESC_RAW); ?></td>
			<td scope="col"><?php echo $sorter->sortLink('subDivision', __('Sub-Division'), '@employee_list', ESC_RAW); ?></td>
			<?php
    			/* Show supervisor only for admin users, not for supervisors */
    			if ($sf_user->hasCredential('Admin')) {
            ?>
			<td scope="col"><?php echo $sorter->sortLink('supervisor', __('Supervisor'), '@employee_list', ESC_RAW); ?></td>
			<?php } ?>
		</tr>
	</thead>

	<tbody>
		<?php
					    $row = 0;
						foreach ($employee_list as $employee):
							$cssClass = ($row %2) ? 'even' : 'odd';
							$row = $row + 1;
						?>

		<tr class="<?php echo $cssClass;?>">
			<?php if(true){//$_GET['reqcode']=='EMP') { ?>
			<td><input type="checkbox" class="checkbox" name="ids[]"
				value="<?php echo $employee->getEmpNumber() ?>" /></td>
			<?php } else { ?>
			<td></td>
			<?php } ?>

			<td>
			<?php
			$empId = $employee->getEmployeeId();

			if (empty($empId)) {
			    $empId = str_pad($employee->getEmpNumber(), IDGeneratorService::MIN_LENGTH, "0", STR_PAD_LEFT);
			}

			echo link_to($empId, "pim/viewEmployee?empNumber=" . $employee->getEmpNumber());
			?>
			</td>
			<td>
			<?php
			    // Link to orangehrm page
			    $params = array('menu_no_top' => 'hr',
			                    'id' => format_emp_number($employee->getEmpNumber()),
			                    'capturemode' => 'updatemode',
			                    'reqcode' => 'EMP');
			    echo link_to($employee->getFullName(), public_path('../../index.php'),
			                 array('query_string'=> http_build_query($params)) );
			?>
			</td>
			<td><?php echo $employee->getJobTitle() ?></td>
			<td><?php echo $employee->getEmployeeStatus() ?></td>
			<td><?php echo $employee->getSubDivision() ?></td>

			<?php /* Show supervisor only for admin users, not for supervisors */
			if ($sf_user->hasCredential('Admin')): ?>
				<td><?php echo $employee->getSupervisorNames() ?></td>
			<?php endif; ?>
		</tr>
		<?php endforeach; ?>

	</tbody>
</table>
</form>
</div>

