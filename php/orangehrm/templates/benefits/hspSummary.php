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

require_once ROOT_PATH . '/lib/models/benefits/Hsp.php';

if (isset($errorFlag)) {

	if (isset($records['hspPlanNotDefined'])) {
	    echo "<h5>".$lang_Benefits_Summary_Plan_Not_Defined."</h5>";
	}

	if (isset($records['noEmployeeRecords'])) {
	    echo "<h5>".$lang_Benefits_Summary_No_Employee_Records."</h5>";
	}

	if (isset($records['nonExistedEmployeeSearch'])) {
	    echo "<h5>".$lang_Benefits_Summary_Search_EmpId_Not_Set."</h5>";
	}

	if (isset($records['hspNotDefined'])) {
	    echo "<h5>".$lang_empview_norecorddisplay."</h5>";
	}

	if (isset($records['hspNotDefinedESS'])) {
	    echo "<h5>".$lang_Benefits_HSP_Plan_Not_Defined_ESS."</h5>";
	}

} else { // HSP defined and Employees exist
    $hspSummary = $records[1];
    $year = $records[2];
    if (isset($records[5])) {
    	$saveSuccess = $records[5];
    }

    if ($records[0] == "searchHspSummary") {
        $oneEmployee = true;
    }

    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") {
        $adminUser = true;
    } else {
        $adminUser = false;
    }
?>
<style>
@import url("../../themes/<?php echo $styleSheet; ?>/css/suggestions.css");
</style>
<?php if ($_SESSION['isAdmin'] == 'Yes') { ?>
<script src="../../scripts/autoSuggest.js"></script>
<script src="../../scripts/suggestions.js"></script>
<?php } ?>
<script>
	function nextPage() {
		i=document.hspFullSummary.pageNo.value;
		i++;
		document.hspFullSummary.pageNo.value=i;
		document.hspFullSummary.action = "?benefitcode=Benefits&action=Hsp_Summary&year=<?php echo $year; ?>";
		document.hspFullSummary.submit();
	}
	function prevPage() {
		var i=document.hspFullSummary.pageNo.value;
		i--;
		document.hspFullSummary.pageNo.value=i;
		document.hspFullSummary.action = "?benefitcode=Benefits&action=Hsp_Summary&year=<?php echo $year; ?>";
		document.hspFullSummary.submit();
	}
	function chgPage(pNo) {
		document.hspFullSummary.pageNo.value=pNo;
		document.hspFullSummary.action = "?benefitcode=Benefits&action=Hsp_Summary&year=<?php echo $year; ?>";
		document.hspFullSummary.submit();
	}

	function markEmpNumber(empName) {
		empNoField = document.getElementById("hidEmpNo");
		for(i in employees) {
			if (employees[i].toLowerCase() == empName.toLowerCase()) {
				empNoField.value = ids[i];
				return;
			} else {
				empNoField.value = '';
			}
		}
	}

        var employees = new Array();
        var ids = new Array();

	<?php
	$employees = $records[6];
	for ($i=0;$i<count($employees);$i++) {
		echo "employees[" . $i . "] = \"" . $employees[$i][1]." ". $employees[$i][2] . "\";";
		echo "ids[" . $i . "] = \"" . $employees[$i][0] . "\";";
	}
	?>

	function edit() {
		with (document.hspFullSummary) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].name == 'txtAnnualLimit[]') || (elements[i].name == 'txtEmployerAmount[]') || (elements[i].name == 'txtEmployeeAmount[]') || (elements[i].name == 'txtTotalAccrued[]') || (elements[i].name == 'txtTotalUsed[]')) {
					elements[i].disabled = '';
				}
			}
		}
		document.getElementById('btnAdd').style.display = 'none';
		document.getElementById('btnSave').style.display = 'inline';
	}

	function save() {

		document.hspFullSummary.action = "?benefitcode=Benefits&action=Save_Hsp_Summary&year=<?php echo $year; ?><?php echo (isset($oneEmployee) && $oneEmployee)?"&empId=".$hspSummary[0]->getEmployeeId():""; ?>";
		document.hspFullSummary.submit();
	}

	function numeric(txt) {
		var flag=true;
		var i,code;

		if(txt.value=="") {
   			return false;
		}

		for(i=0;txt.value.length>i;i++) {
			code=txt.value.charCodeAt(i);
   			if(code>=48 && code<=57 || code==46) {
	   			flag=true;
			} else {
	   			flag=false;
	   			break;
	   		}
		}
		return flag;

	}

    function haltResumeHsp(hspId, empId, newHspStatus) {
    	xmlHTTPObject = null;

		try {
  			xmlHTTPObject = new XMLHttpRequest();
		} catch (e) {
			try {
			    xmlHTTPObject = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				xmlHTTPObject = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}

		if (xmlHTTPObject == null)
			alert("Your browser does not support AJAX!");

        xmlHTTPObject.onreadystatechange = function() {

            if (xmlHTTPObject.readyState == 4){

				completed = (xmlHTTPObject.responseText.trim().substr(0, 4) == 'done');
				serverMsg = xmlHTTPObject.responseText.trim().substr(5);

                if(completed) {
		   			successMsg = parseInt(xmlHTTPObject.responseText.trim().substr(5));

		   			switch (successMsg) {
						case <?php echo Hsp::HSP_STATUS_HALTED; ?> :
							statusLabel = 'Halted';
							buttonLabel = 'Resume';
							buttonWidth = '76px';
							hspReverseStatus = <?php echo Hsp::HSP_STATUS_ACTIVE; ?>;
							break;

						case <?php echo Hsp::HSP_STATUS_ACTIVE; ?> :
							statusLabel = 'Active';
							<?php if ($_SESSION['isAdmin'] == 'Yes') { ?>
								buttonLabel = 'Halt';
								buttonWidth = '76px';
								hspReverseStatus = <?php echo Hsp::HSP_STATUS_HALTED; ?>;
							<?php } else { ?>
								buttonLabel = 'Request Halt';
								buttonWidth = '95px';
								hspReverseStatus = <?php echo Hsp::HSP_STATUS_PENDING_HALT; ?>;
							<?php } ?>
							break;

						case <?php echo Hsp::HSP_STATUS_ESS_HALTED; ?> :
							statusLabel = 'Halted';
							buttonLabel = 'Resume';
							buttonWidth = '76px';
							hspReverseStatus = <?php echo Hsp::HSP_STATUS_ACTIVE; ?>;
							break;

						case <?php echo Hsp::HSP_STATUS_PENDING_HALT; ?> :
							statusLabel = 'Pending Halt';
							buttonLabel = 'Cancel Halt Request';
							buttonWidth = '130px';
							hspReverseStatus = <?php echo Hsp::HSP_STATUS_ACTIVE; ?>;
							break;

		   			}
					
					if (navigator.appVersion.indexOf("MSIE") != -1) {

						with(document.getElementById('btnHspStatus' + hspId)) {
							disabled = false;
							setAttribute("value", buttonLabel);
							style.width = buttonWidth;
							f = function(){
								haltResumeHsp(hspId,empId, hspReverseStatus);
							}
							setAttribute("onclick", f)
						}
					} else {
						with(document.getElementById('btnHspStatus' + hspId)) {
							
							disabled = false;
							setAttribute("value", buttonLabel);
							setAttribute("style", "width: " + buttonWidth);
							setAttribute("onclick", "haltResumeHsp('" + hspId + "', '" + empId + "', '" + hspReverseStatus + "');");
							
						}
					}

                   document.getElementById('lblHspStatus' + hspId).innerHTML = statusLabel;
				} else {
					alert('Error: ' + serverMsg);
				}
            } else {

		document.getElementById('btnHspStatus' + hspId).disabled = true;
	
	    }
        }

        xmlHTTPObject.open('GET', '../../plugins/ajaxCalls/haltResumeHsp.php?hspSummaryId=' + hspId + '&empId='+ empId +'&newHspStatus=' + newHspStatus, true);
        xmlHTTPObject.send(null);
    }

</script>
<?php if (isset($oneEmployee)) {  ?>
<h2><?php echo $lang_Benefits_Summary_Employee_Heading." "; echo $hspSummary[0]->getEmployeeName(); ?> - <?php echo $year; ?></h2>
<?php } else { ?>
<h2><?php echo $lang_Benefits_Summary_Heading; ?> - <?php echo $year; ?></h2>
<?php } ?>
<br />

<!-- Save success message begins -->
<?php

if (isset($saveSuccess) && $saveSuccess) {
	echo "<font color=\"#009900\"><b>".$lang_Benefits_Summary_Saved_Successfully."</b></font><br>";
} elseif (isset($saveSuccess) && !$saveSuccess) {
	echo "<font color=\"#FF0066\"><b>".$lang_Benefits_Summary_Could_Not_Save."</b></font><br>";
}

?>
<!-- Save success message ends -->

<!-- Search form begins -->
<form name="frmEmployeeSearch" action="?benefitcode=Benefits&action=Search_Hsp_Summary" method="post" onsubmit="markEmpNumber(this.txtEmployeeSearch.value);">
<input type="hidden" name="hidEmpNo" id="hidEmpNo" value="" />
<table width="670" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td width="450">
    <?php if ($adminUser) { ?>
    Employee <input type="text" name="txtEmployeeSearchName" id="txtEmployeeSearch" size="20" onchange="" />
    <?php } ?>
    &nbsp;&nbsp;&nbsp;
    Year
	<select name="year" id="">
	<?php
	$years = $records[7];
	foreach ($years as $val) {
	?>
	<option value="<?php echo $val; ?>" <?php echo ($val==date('Y'))?"selected":""; ?>><?php echo $val; ?></option>
	<?php } ?>
	</select>&nbsp;&nbsp;&nbsp;<input type="submit" name="search" id="search" value="Search" />
    </td>
    <td width="0">
	</td>
    <td width="230">
    <?php if ($adminUser) { ?>
 	<img id="btnAdd" title="Add" onClick="edit();"
 		 onMouseOut="this.src='../../themes/beyondT/pictures/btn_edit.gif';"
 		 onMouseOver="this.src='../../themes/beyondT/pictures/btn_edit_02.gif';"
 		 src="../../themes/beyondT/pictures/btn_edit.gif"
 		 style="display:inline;" />
 	<img id="btnSave" title="Save" onClick="save();"
 		 onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';"
 		 onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';"
 		 src="../../themes/beyondT/pictures/btn_save.gif"
 		 style="display:none;"/>
 	<?php } ?>
	<?php 	if ($_SESSION['printBenefits'] == "enabled" && $_SESSION['isAdmin']=='Yes') { 

		if (isset($oneEmployee) && $oneEmployee) {
			$pdfName = 'Personal-HSP-Summary';
			$empNoQueryStr = '&empId=' . $_POST['hidEmpNo'];
		} else {
			$pdfName = 'All-Employees-HSP-Summary';
			$empNoQueryStr = '';
		}
	?>
		<a href="?benefitcode=Benefits&action=Hsp_Summary&year=<?php echo $year; ?>&printPdf=1&pdfName=<?php echo $pdfName . $empNoQueryStr; ?>"><img title="Save As PDF" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save_as_pdf_01.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_as_pdf_02.gif';" src="../../themes/beyondT/pictures/btn_save_as_pdf_01.gif" border="0"></a>
	<?php } ?>
    </td>
  </tr>
</table>
</form>
<?php if ($_SESSION['isAdmin'] == 'Yes') { ?>
<script language="javascript">
	var oTextbox = new AutoSuggestControl(document.getElementById("txtEmployeeSearch"), new StateSuggestions(employees));
</script>
<?php } ?>
<br />
<!-- Search form ends -->

<!-- Summary form begins -->
<form name="hspFullSummary" action="" method="post">
<input type="hidden" name="pageNo" value="<?php echo $records[3]; ?>">
<table width="740" border="0" cellspacing="0" cellpadding="0">
<thead>
		  	<tr>
			<th class="tableTopLeft"></th>
			<?php if (!isset($oneEmployee) || $adminUser) { ?>
			<th class="tableTopMiddle" width="130"></th>
			<?php } ?>
			<th class="tableTopMiddle" width="50"></th>
		    	<th class="tableTopMiddle" width="70"></th>
		    	<th class="tableTopMiddle" width="90"></th>
		    	<th class="tableTopMiddle" width="90"></th>
		    	<th class="tableTopMiddle" width="90"></th>
		    	<th class="tableTopMiddle" width="90"></th>
			<th class="tableTopMiddle" width="90"></th>
		    	<th class="tableTopMiddle" width="50"></th>
		    	<th class="tableTopRight"></th>
			</tr>
  <tr>
    <th class="tableMiddleLeft"></th>
    <th colspan="<?php echo (!isset($oneEmployee) || $adminUser)?"4":"3"; ?>" scope="col">&nbsp;</th>
    <th colspan="2" align="center" scope="col"><?php echo $lang_Benefits_Summary_Contribution; ?></th>
    <th colspan="3" scope="col">&nbsp;</th>
	<th class="tableMiddleRight"></th>
  </tr>
  <tr>
    <th class="tableMiddleLeft"></th>
    <?php if (!isset($oneEmployee) || $adminUser) {  ?>
    <th><?php echo $lang_Benefits_Summary_Employee; ?></th>
    <?php } ?>
    <th><?php echo $lang_Benefits_Summary_Plan; ?></th>
    <th><?php echo $lang_Benefits_Summary_Status; ?></th>
    <th><?php echo $lang_Benefits_Summary_Annual_Limit; ?> <br />($) </th>
    <th><?php echo $lang_Benefits_Summary_Employer; ?> <br />($) </th>
    <th><?php echo $lang_Benefits_Summary_Employee; ?> <br />($) </th>
    <th><?php echo $lang_Benefits_Summary_Total_Accrued; ?> <br />($) </th>
    <th><?php echo $lang_Benefits_Summary_Total_Used; ?> <br />($) </th>
    <th>&nbsp;</th>
	<th class="tableMiddleRight"></th>
  </tr>
</thead>
<tbody>
<?php for ($i=0; $i<count($hspSummary); $i++) { // Displaying summary begins

$rowStyle = 'odd'; // For adding row background color. Refers time.css at /themes/beyondT/css/
if (($i%2) == 0) {
	$rowStyle = 'even';
}

?>
<!-- This TR is repeated for each summary record -->
  <tr>
    <td class="tableMiddleLeft"></td>
    <?php if (!isset($oneEmployee) || $adminUser) { ?>
    <td class="<?php echo $rowStyle; ?>"><a href="?benefitcode=Benefits&action=Hsp_Expenditures&year=<?php echo $year; ?>&employeeId=<?php echo $hspSummary[$i]->getEmployeeId(); ?>"><?php echo $hspSummary[$i]->getEmployeeName(); ?></a>
    <?php } ?>
    <input type="hidden" name="hidSummaryId[]" id="" value="<?php echo $hspSummary[$i]->getSummaryId(); ?>" />
    <input type="hidden" name="hidEmployeeId[]" id="" value="<?php echo $hspSummary[$i]->getEmployeeId(); ?>" />
    </td>
    <td class="<?php echo $rowStyle; ?>"><?php echo $hspSummary[$i]->getHspPlanName(); ?></td>
    <td class="<?php echo $rowStyle; ?>"><span id="lblHspStatus<?php echo $hspSummary[$i]->getSummaryId(); ?>"><?php echo $hspSummary[$i]->getHspPlanStatusName(); ?></span></td>
    <td class="<?php echo $rowStyle; ?>">
    <?php if ($adminUser) { ?>
    <input type="text" name="txtAnnualLimit[]" id="" value="<?php echo $hspSummary[$i]->getAnnualLimit(); ?>" size="6" disabled />
    <?php } else {
    	echo $hspSummary[$i]->getAnnualLimit();
    }
    ?>
    </td>

    <td class="<?php echo $rowStyle; ?>">
    <?php
    if ($hspSummary[$i]->getHspPlanId() == 3) {
    	echo "NA";
    	echo "<input type=\"hidden\" name=\"txtEmployerAmount[]\" value=\"0\" />";
    } else {
    ?>
    <?php if ($adminUser) { ?>
    <input type="text" name="txtEmployerAmount[]" id="" value="<?php echo $hspSummary[$i]->getEmployerAmount(); ?>" size="6" disabled />
    <?php } else {
    	echo $hspSummary[$i]->getEmployerAmount();
    }
    ?>
    <?php } ?>
    </td>
    <td class="<?php echo $rowStyle; ?>">
	<?php
    if ($hspSummary[$i]->getHspPlanId() == 2) {
    	echo "NA";
    	echo "<input type=\"hidden\" name=\"txtEmployeeAmount[]\" value=\"0\" />";
    } else {
    ?>
    <?php if ($adminUser) { ?>
    <input type="text" name="txtEmployeeAmount[]" id="" value="<?php echo $hspSummary[$i]->getEmployeeAmount(); ?>" size="6" disabled />
    <?php } else {
    	echo $hspSummary[$i]->getEmployeeAmount();
    }
    ?>
    <?php } ?>
    </td>
    <td class="<?php echo $rowStyle; ?>">
    <?php if ($adminUser) { ?>
    <input type="text" name="txtTotalAccrued[]" id="" value="<?php echo $hspSummary[$i]->getTotalAccrued(); ?>" size="6" disabled />
    <?php } else {
    	echo $hspSummary[$i]->getTotalAccrued();
    }
    ?>
    </td>
    <td class="<?php echo $rowStyle; ?>">
    <?php if ($adminUser) { ?>
    <input type="text" name="txtTotalUsed[]" id="" value="<?php echo $hspSummary[$i]->getTotalUsed(); ?>" size="6" disabled />
    <?php } else {
    	echo $hspSummary[$i]->getTotalUsed();
    }
    ?>
    </td>
    <td id="buttonSlot">
    <?php
	$summaryId = $hspSummary[$i]->getSummaryId();
	$statusId  = $hspSummary[$i]->getHspPlanStatus();
	$empId	   = $hspSummary[$i]->getEmployeeId();

	$buttonDisabled = '';
	$buttonWidth = '76px';

	switch ($statusId) {

		case Hsp::HSP_STATUS_HALTED :
			if ($_SESSION['isAdmin'] == 'Yes') {
				$buttonLabel = 'Resume';
				$newStatusId = Hsp::HSP_STATUS_ACTIVE;
			} else {
				$buttonLabel = 'Resume';
				$buttonDisabled = 'disabled';
				$newStatusId = '';
			}

			break;

		case Hsp::HSP_STATUS_ACTIVE :
			if ($_SESSION['isAdmin'] == 'Yes') {
				$buttonLabel = 'Halt';
				$newStatusId = Hsp::HSP_STATUS_HALTED;
			} else {
				$buttonLabel = 'Request Halt';
				$buttonWidth = '95px';
				$newStatusId = Hsp::HSP_STATUS_PENDING_HALT;
			}

			break;

		case Hsp::HSP_STATUS_ESS_HALTED :
			$buttonLabel = 'Resume';
			$newStatusId = Hsp::HSP_STATUS_ACTIVE;

			break;

		case Hsp::HSP_STATUS_PENDING_HALT :
			if ($_SESSION['isAdmin'] == 'Yes') {
				$buttonLabel = 'Halt';
				$newStatusId = Hsp::HSP_STATUS_ESS_HALTED;
			} else {
				$buttonLabel = 'Cancel Halt Request';
				$buttonWidth = '130px';
				$newStatusId = Hsp::HSP_STATUS_ACTIVE;
			}

			break;

		default :
			break;

	}

	$onclickFunction = "haltResumeHsp('$summaryId', '$empId', '$newStatusId')";

    ?>
    <input type="button" name="btnHspStatus[]" id="btnHspStatus<?php echo $summaryId; ?>" value="<?php echo $buttonLabel; ?>" onclick="<?php echo $onclickFunction; ?>" style="width: <?php echo $buttonWidth; ?>;" <?php echo $buttonDisabled; ?> />
    </td>
   <td class="tableMiddleRight"></td>
  </tr>
<?php } // Displaying summary ends ?>
</tbody>
		<tfoot>
		  	<tr>
				<td class="tableBottomLeft"></td>
				<?php if (!isset($oneEmployee) || $adminUser) { ?>
				<td class="tableBottomMiddle"></td>
				<?php } ?>
				<td class="tableBottomMiddle"></td>
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
<!-- Summary form ends -->
<table width="750">
<!-- Paging begins -->
<tr>
<td class="paging" height="40">
<?php
if (!isset($oneEmployee)) {
	$commonFunc = new CommonFunctions();
	$pageStr = $commonFunc->printPageLinks($records[4], $records[3], 50);
	$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

	echo $pageStr;
}
?>
</td>
</tr>
<!-- Paging ends -->
</table>

<?php } // HSP defined and Employees exist ?>
