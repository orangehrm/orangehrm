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
$token = $records['token'];
?>
<script type="text/javascript">
//<![CDATA[
var commonAction = '?benefitcode=Benefits&year=<?php echo CommonFunctions::escapeHtml($year); ?>&action=';

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
//]]>
</script>

<div class="outerbox">    
<form id="frmMain" name="frmMain" method="post" action="">
   <input type="hidden" name="token" value="<?php echo $token;?>" />
    <div class="mainHeading"><h2><?php echo "$lang_Benefits_PayrollSchedule : $year"; ?></h2></div>
    <?php 
    if (isset($_GET['message'])) {  
        $expString  = $_GET['message'];
        $messageType = CommonFunctions::getCssClassForMessage($expString, 'failure');
        $expString = 'lang_Benefits_Errors_' . $expString;        
    ?>      
    <div class="messagebar">
        <span class="<?php echo $messageType; ?>"><?php echo $$expString; ?></span>
    </div>
    <?php
    }
    ?>

    <div class="actionbar">
        <div class="actionbuttons">
        <?php   if($auth->isAdmin()) { ?>
            <input type="button" class="addbutton" onclick="returnAdd();"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Add;?>" />          
        <?php   }
                if ($auth->isAdmin() && (count($payPeriods) > 0)) { ?>
            <input type="button" class="delbutton" onclick="returnDelete();"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Delete;?>" />
                    
        <?php   } ?>
        </div>    
        <div class="noresultsbar"><?php echo (count($payPeriods) == 0) ? $lang_Error_NoRecordsFound : ''; ?></div>
        <div class="pagingbar"></div>
    <br class="clear" />
    </div>
    <br class="clear" />                               

	<table border="0" cellpadding="5" cellspacing="0" class="data-table">
		<thead>
			<tr>
				<?php if($auth->isAdmin()) { ?>
				<td></td>
				<?php } ?>
		    	<td><?php echo $lang_Benefits_CheckDate; ?></td>
		    	<td><?php echo $lang_Benefits_PayPeriod; ?></td>
		    	<td><?php echo $lang_Benefits_PayPeriodCloses; ?></td>
		    	<td><?php echo $lang_Benefits_TimesheetAprovalDue; ?></td>
			</tr>
		</thead>
		<tbody>
		<?php if (count($payPeriods) > 0) { ?>
			<?php
				$i=0;
				foreach ($payPeriods as $payPeriod) {
                    $rowStyle = ($i % 2) ? 'even' : 'odd';
					$i++;
			?>
				<tr>
					<?php if($auth->isAdmin()) { ?>
					<td class="<?php echo $rowStyle; ?>"><input type="checkbox" name="chkPayPeriodId[]" id="chkPayPeriodId_<?php echo $i;?>" value="<?php echo $payPeriod->getId(); ?>" /></td>
					<?php } ?>
					<td class="<?php echo $rowStyle; ?>">
						<?php
						if ($auth->isAdmin()) {
						?>
						<a href="?benefitcode=Benefits&amp;action=View_Edit_Pay_Period&amp;year=<?php echo CommonFunctions::escapeHtml($year); ?>&amp;id=<?php echo $payPeriod->getId(); ?>"><?php echo $payPeriod->getCheckDate(); ?></a>
						<?php
						} else {
							echo $payPeriod->getCheckDate();
						}
						?>
					</td>
					<td class="<?php echo $rowStyle; ?>"><?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getStartDate())." {$lang_Common_To} ".LocaleUtil::getInstance()->formatDate($payPeriod->getEndDate()); ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getCloseDate()); ?></td>
					<td class="<?php echo $rowStyle; ?>"><?php echo LocaleUtil::getInstance()->formatDate($payPeriod->getTimesheetAprovalDueDate()); ?></td>
				</tr>
			<?php } ?>
		<?php }?>
		</tbody>
	</table>
</form>
</div>
<script type="text/javascript">
    <!--
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');                
        }
    -->
</script>