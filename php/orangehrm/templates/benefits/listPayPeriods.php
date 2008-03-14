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
$payPeriods = $records[0];
$year = $records[1];
$auth = $records[2];
?>
<h2>
	<?php echo "$lang_Benefits_PayrollSchedule : $year"; ?>
	<hr/>
</h2>
<?php if (isset($_GET['message'])) {

		$expString  = $_GET['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
		$expString = 'lang_Benefits_Errors_' . $expString;
?>
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
<?php echo $$expString; ?>
		</font>
<?php }	?>
<script type="text/javascript">
var commonAction = '?benefitcode=Benefits&year=<?php echo $year; ?>&action=';

function returnAdd() {
	window.location = commonAction+'View_Add_Pay_Period';
}

function returnDelete() {
	$check = 0;
	with (document.frmMain) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'chkPayPeriodId[]')){
				$check = 1;
			}
		}
	}

	if ($check == 1){
		$('frmMain').action=commonAction+'Delete_Pay_Period';
		$('frmMain').submit();
	} else {
		alert("<?php echo $lang_Common_SelectDelete; ?>");
	}
}
</script>
<div id="controls">
<?php	if($auth->isAdmin()) { ?>
 	<img title="Add" onClick="returnAdd();"
 		 onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.gif';"
 		 onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.gif';"
 		 src="../../themes/beyondT/pictures/btn_add.gif" />
<?php 	}
		if ($auth->isAdmin() && (count($payPeriods) > 0)) { ?>
 	<img title="Delete" onClick="returnDelete();"
 		 onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.gif';"
 		 onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';"
 		 src="../../themes/beyondT/pictures/btn_delete.gif" />
<?php 	} ?>
</div>
<?php if (count($payPeriods) == 0) { ?>
	<h5><?php echo $lang_Error_NoRecordsFound; ?></h5>
<?php } else { ?>
<form id="frmMain" name="frmMain" method="post">
	<table border="0" cellpadding="5" cellspacing="0">
		<thead>
		  	<tr>
				<th class="tableTopLeft"></th>
				<?php if($auth->isAdmin()) { ?>
				<th class="tableTopMiddle"></th>
				<?php } ?>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
		    	<th class="tableTopMiddle"></th>
				<th class="tableTopRight"></th>
			</tr>
			<tr>
				<th class="tableMiddleLeft"></th>
				<?php if($auth->isAdmin()) { ?>
				<th></th>
				<?php } ?>
		    	<th><?php echo $lang_Benefits_CheckDate; ?></th>
		    	<th></th>
		    	<th><?php echo $lang_Benefits_PayPeriod; ?></th>
		    	<th></th>
		    	<th><?php echo $lang_Benefits_PayPeriodCloses; ?></th>
		    	<th></th>
		    	<th><?php echo $lang_Benefits_TimesheetAprovalDue; ?></th>
				<th class="tableMiddleRight"></th>
			</tr>
		</thead>
		<tbody>
		<?php if (count($payPeriods) > 0) { ?>
			<?php
				$i=0;
				foreach ($payPeriods as $payPeriod) {
					$rowStyle = 'odd';
					if (($i%2) == 0) {
						$rowStyle = 'even';
					}
					$i++;
			?>
				<tr>
					<td class="tableMiddleLeft"></td>
					<?php if($auth->isAdmin()) { ?>
					<td class="<?php echo $rowStyle; ?>"><input type="checkbox" name="chkPayPeriodId[]" id="chkPayPeriodId[]" value="<?php echo $payPeriod->getId(); ?>" /></td>
					<?php } ?>
					<td class="<?php echo $rowStyle; ?>">
						<?php
						if ($auth->isAdmin()) {
						?>
						<a href="?benefitcode=Benefits&action=View_Edit_Pay_Period&year=<?php echo $year; ?>&id=<?php echo $payPeriod->getId(); ?>"><?php echo $payPeriod->getCheckDate(); ?></a>
						<?php
						} else {
							echo $payPeriod->getCheckDate();
						}
						?>
					</td>
					<td class="<?php echo $rowStyle; ?>"></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getStartDate())." {$lang_Common_To} ".LocaleUtil::getInstance()->formatDate($payPeriod->getEndDate()); ?></td>
					<td class="<?php echo $rowStyle; ?>"></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getCloseDate()); ?></td>
					<td class="<?php echo $rowStyle; ?>"></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getTimesheetAprovalDueDate()); ?></td>
				    <td class="tableMiddleRight"></td>
				</tr>
			<?php } ?>
		<?php }?>
		</tbody>
		<tfoot>
		  	<tr>
				<td class="tableBottomLeft"></td>
				<?php if($auth->isAdmin()) { ?>
				<td class="tableBottomMiddle"></td>
				<?php } ?>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomMiddle"></td>
				<td class="tableBottomRight"></td>
			</tr>
	  	</tfoot>
	</table>
</form>
<?php }?>