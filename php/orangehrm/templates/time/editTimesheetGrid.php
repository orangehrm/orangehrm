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

require_once ROOT_PATH . '/lib/controllers/TimeController.php';

$GLOBALS['lang_Common_Select'] = $lang_Common_Select;

function populateActivities($projectId, $row, $activityId=null, $activityName=null) {

	ob_clean();

	require ROOT_PATH . '/language/default/lang_default_full.php';

	$timeController = new TimeController();
	$projectActivities = $timeController->fetchProjectActivities($projectId);

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
	$element="cmbActivity[$row]";

	if (count($projectActivities) == 0) {
		$projectActivities[0][0] = -1;
		$projectActivities[0][1] = "- $lang_Time_Timesheet_SelectProject -";

		$objResponse = $xajaxFiller->cmbFillerById($objResponse,$projectActivities, 0,'frmTimesheet',$element, 0);
	} else {

		if ($activityId != null) {
			$projectActivityObject = new ProjectActivity();
		    if ($projectId == $projectActivityObject->retrieveActivityProjectId($activityId)) {
				$activityExists = false;
				$i = 0;
				foreach ($projectActivities as $activity) {
					if ($activity[$i][0] == $activityId) {
					    $activityExists = true;
					}
					$i++;
				}

				if (!$activityExists) {
					$count = count($projectActivities);
					$projectActivities[$count][0] = $activityId;
					$projectActivities[$count][1] = $activityName;
				}
		    }
		}

		$objResponse->addScript("document.getElementById('".$element."').options.length = 0;");
	 	$objResponse->addScript("document.getElementById('".$element."').options[0] = new Option('- $lang_Common_Select -','-1');");
		$objResponse = $xajaxFiller->cmbFillerById($objResponse,$projectActivities, 0,'frmTimesheet',$element, 1);
	}

	$objResponse->addScript('document.getElementById("'.$element.'").focus();');

	$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateActivities');
$objAjax->processRequests();

$timesheet=$records[0];
$timesheetSubmissionPeriod=$records[1];
$timeExpenses=$records[2];
$customers=$records[3];
$projects=$records[4];
$employee=$records[5];
$self=$records[6];
$return=$records[8];

$status=$timesheet->getStatus();

switch ($status) {
	case Timesheet::TIMESHEET_STATUS_NOT_SUBMITTED : $statusStr = $lang_Time_Timesheet_Status_NotSubmitted;
												break;
	case Timesheet::TIMESHEET_STATUS_SUBMITTED : $statusStr = $lang_Time_Timesheet_Status_Submitted;
												break;
	case Timesheet::TIMESHEET_STATUS_APPROVED : $statusStr = $lang_Time_Timesheet_Status_Approved;
												break;
	case Timesheet::TIMESHEET_STATUS_REJECTED : $statusStr = $lang_Time_Timesheet_Status_Rejected;
												break;
}

$startDate = strtotime($timesheet->getStartDate() . " 00:00:00");
$endDate = strtotime($timesheet->getEndDate() . " 23:59:59");
$startDatePrint = LocaleUtil::getInstance()->formatDateTime(date("Y-m-d H:i", $startDate));
$endDatePrint = LocaleUtil::getInstance()->formatDateTime(date("Y-m-d H:i", $endDate));
$row=0;

$sysConf = new sysConf();
$dateFormat = LocaleUtil::convertToXpDateFormat($sysConf->getDateFormat());
$timeFormat = LocaleUtil::convertToXpDateFormat($sysConf->getTimeFormat());

?>
<style type="text/css">
.tableTopLeft {
    background: none;    
}
.tableTopMiddle {
    background: none;    
}
.tableTopRight {
    background: none;    
}
.tableMiddleLeft {
    background: none;    
}
.tableMiddleRight {
    background: none;    
}
.tableBottomLeft {
    background: none;    
}
.tableBottomMiddle {
    background: none;    
}
.tableBottomRight {
    background: none;    
}
</style>


<?php $objAjax->printJavascript(); ?>



<div class="outerbox" style="width:980px">
<div class="mainHeading"><h2>Edit timesheet for week starting 2009-03-09</h2></div>    
    
<form id="frmTimesheet" name="frmTimesheet" method="post" action="/orangehrm/lib/controllers/CentralController.php?timecode=Time&id=4&action=">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>

		<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle" width="120px"></th>
	    	<th class="tableTopMiddle" width="120px"></th>

<?php for ($i=$startDate; $i<=$endDate; $i=strtotime("+1 day", $i)) { ?>
			<th width="80px" class="tableTopMiddle"></th>
<?php } ?>

			<th class="tableTopRight"></th>
		</tr>
		<tr>
			<th class="tableMiddleLeft"></th>
			<th class="tableMiddleMiddle">Project</th>
			<th class="tableMiddleMiddle">Activity</th>

<?php for ($i=$startDate; $i<=$endDate; $i=strtotime("+1 day", $i)) { ?>
			<th width="80px" class="tableMiddleMiddle"><?php echo date('l ' . LocaleUtil::getInstance()->getDateFormat(), $i); ?></th>
<?php } ?>

			<th class="tableMiddleRight"></th>

		</tr>
	</thead>
	<tbody>
					<tr id="row[0]">
				<td class="tableMiddleLeft"></td>
				<td ><select id="cmbProject[0]" name="cmbProject[]" onfocus="looseCurrFocus();"  onchange="$('status').innerHTML='Loading...'; xajax_populateActivities(this.value, 0);" >
										<option value="-1">--Select--</option>

										<option value="0">Internal - Internal</option>
									</select>
				</td>
				<td ><select id="cmbActivity[0]" name="cmbActivity[]" onfocus="looseCurrFocus();">
						<option value="-1">- Select a Project -</option>
					</select>
				</td>
				
<?php for ($i=$startDate; $i<=$endDate; $i=strtotime("+1 day", $i)) { ?>
				<td width="80px"></td>
<?php } ?>
				
				<td class="tableMiddleRight"></td>
			</tr>
	</tbody>
	<tfoot>

	  	<tr>
			<td class="tableBottomLeft"></td>
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomMiddle"></td>

<?php for ($i=$startDate; $i<=$endDate; $i=strtotime("+1 day", $i)) { ?>
			<td width="80px" class="tableBottomMiddle">
			<input type="text" name="txtDuration" size="10" maxlength="5" />
			</td>
<?php } ?>

			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>

<p id="controls">
<input type="hidden" name="txtTimesheetId" value="4" />
<input type="hidden" name="txtEmployeeId" value="1" />
<input type="hidden" name="nextAction" value="View_Timesheet" />
<div class="formbuttons">
<input type="button" class="updatebutton"  
        onclick="actionUpdate(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnUpdate" id="btnUpdate"                              
        value="Add Row" />         
<input type="button" class="resetbutton"  
        onclick="actionReset(); return false;"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        name="btnReset" id="btnReset"                              
        value="Save" />         
</p>

</form>
</div>
</div>

<script type="text/javascript">
        //<![CDATA[
        totRows = 0;
        currFocus = $("cmbProject[0]");
        currFocus.focus();
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');                
        }
        //]]>
</script>