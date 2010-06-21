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
$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
   unset($records['token']);
}

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

    // If FSA is avilabe and date is belwo March 15th
    $showFsaBalance=false;
   	$currentHspPlan = Config::getHspCurrentPlan();
   	if ($currentHspPlan == 3 || $currentHspPlan == 4 || $currentHspPlan == 5) { // If FSA is avaialbe in current plan
		$fsaEndDate = date('Y')."-03-15";
		$currentDate = date('Y-m-d');
		if ($currentDate <= $fsaEndDate) {
			$showFsaBalance=true;
		}
   	}

?>
<style>
#employeeSearchAC {
    width:15em; /* set width here */
    padding-bottom:2em;
    position:relative;
    top:-10px
}
#employeeSearchAC {
    z-index:9000; /* z-index needed on top instance for ie & sf absolute inside relative issue */
    float:left;
    margin-right:5px;
}
#txtEmployeeSearch {
    _position:absolute; /* abs pos needed for ie quirks */
}

#select {
	position:relative;
	left:10px;
}

#resetSummary, #btnSaveAsPDF {
	position:relative;
	left:-26px;
}

.data-table .valueSlot {
	display:block;
	width:100%;
	text-align:center;
}
</style>
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
		echo "employees[" . $i . "] = '" . addslashes($employees[$i][1] . " " . $employees[$i][2]) . "';\n";
		echo "ids[" . $i . "] = \"" . $employees[$i][0] . "\";\n";
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
		document.getElementById('btnEdit').style.display = 'none';
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

					with(document.getElementById('btnHspStatus' + hspId)) {

						disabled = false;

						if (navigator.appVersion.indexOf("MSIE") != -1) {

							setAttribute("value", buttonLabel);
							style.width = buttonWidth;

							f = function(){
								haltResumeHsp(hspId,empId, hspReverseStatus);
							}

							setAttribute("onclick", f);

						} else {
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

	function showAutoSuggestTip(obj) {
		if (obj.value == '<?php echo $lang_Common_TypeHereForHints; ?>') {
			obj.value = '';
			obj.style.color = '#000000';
		}
	}

	function resetForm() {
		$('hspFullSummary').reset();
	}

</script>
<div class="outerbox" style="width:96%;">
<?php include ROOT_PATH."/lib/common/autocomplete.php"; ?>
<div class="mainHeading"><h2><?php
if (isset($oneEmployee)) {
	echo "{$lang_Benefits_Summary_Employee_Heading} {$hspSummary[0]->getEmployeeName()} - {$year}";
} else {
	echo "{$lang_Benefits_Summary_Heading}- {$year}";
}
?></h2>
</div>

<!-- Save success message begins -->
<?php

if (isset($saveSuccess) && $saveSuccess) {
	$successMessage = $lang_Benefits_Summary_Saved_Successfully;
    $messageType = 'success';
} elseif (isset($saveSuccess) && !$saveSuccess) {
    $messageType = 'failure';
	$successMessage = $lang_Benefits_Summary_Could_Not_Save;
}

if (isset($successMessage)) {  ?>
<div class="messagebar">
    <span class="<?php echo $messageType; ?>"><?php echo $successMessage; ?></span>
</div>
<?php } ?>

<!-- Save success message ends -->

<!-- Search form begins -->
<form name="frmEmployeeSearch" action="?benefitcode=Benefits&action=Search_Hsp_Summary" method="post" onsubmit="markEmpNumber(this.txtEmployeeSearch.value);">

<input type="hidden" name="hidEmpNo" id="hidEmpNo" value="" />
    <div class="searchbox">
    <?php if ($adminUser) { ?>
        <label for="txtEmployeeSearch"><?php echo $lang_Admin_Users_Employee?></label>
        <div class="yui-ac" id="employeeSearchAC">
          <input autocomplete="off" class="yui-ac-input" id="txtEmployeeSearch" type="text" name="txtEmployeeSearchName" value="<?php echo $lang_Common_TypeHereForHints; ?>" onfocus="showAutoSuggestTip(this)" style="color: #999999" />
          <div class="yui-ac-container" id="employeeSearchACContainer">
            <div style="display: none; width: 159px; height: 0px; left: 100em" class="yui-ac-content">
              <div style="display: none;" class="yui-ac-hd"></div>
              <div class="yui-ac-bd">
                <ul>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                  <li style="display: none;"></li>
                </ul>
              </div>
              <div style="display: none;" class="yui-ac-ft"></div>
            </div>
            <div style="width: 0pt; height: 0pt;" class="yui-ac-shadow"></div>
          </div>
        </div>
    <?php } ?>

    <select name="year" id="select">
      <?php
    $years = $records[7];
    foreach ($years as $val) {
    ?>
      <option value="<?php echo $val; ?>" <?php echo ($val==date('Y'))?"selected":""; ?>><?php echo $val; ?></option>
      <?php } ?>
    </select>
    <input type="submit" class="plainbtn" onclick="returnSearch();"
        onmouseover="moverButton(this)" onmouseout="moutButton(this)"
        value="<?php echo $lang_Common_Search;?>" />
    <?php if ($adminUser) { ?>
    <input type="button" class="editbutton" id="btnEdit"
        onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_Edit;?>"
        style="display:inline;"/>
    <input type="button" class="savebutton" id="btnSave"
        onclick="save();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_Save;?>"
        style="display:none;"/>
    <?php } ?>
	<input type="button" id="resetSummary" class="plainbtn" value="<?php echo $lang_Common_Reset; ?>"
		onmouseover="moverButton(this)" onmouseout="moutButton(this)"
		onclick="resetForm()" />
    <?php   if ($_SESSION['printBenefits'] == "enabled" && $_SESSION['isAdmin']=='Yes') {

        if (isset($oneEmployee) && $oneEmployee) {
            $pdfName = 'Personal-HSP-Summary';
            $empNoQueryStr = '&empId=' . $_POST['hidEmpNo'];
        } else {
            $pdfName = 'All-Employees-HSP-Summary';
            $empNoQueryStr = '';
        }

        $pdfUrl = "?benefitcode=Benefits&action=Hsp_Summary&year={$year}&printPdf=1&pdfName={$pdfName}{$empNoQueryStr}";
    ?>
    	<input type="button" class="longbtn" id="btnSaveAsPDF" value="<?php echo $lang_Benefits_Summary_SaveAsPDF; ?>"
			onmouseover="moverButton(this)" onmouseout="moutButton(this)"
    		onclick="location.href='<?php echo $pdfUrl; ?>';" />
        <?php } ?>
        <br class="clear"/>
    </div>

</table>
</form>
<!-- Search form ends -->
<div class="pagingbar">
<?php
if (!isset($oneEmployee)) {
	$commonFunc = new CommonFunctions();
	$pageStr = $commonFunc->printPageLinks($records[4], $records[3], 50);
	$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

	echo $pageStr;
}
?>
</div><br class="clear" />
<!-- Summary form begins -->
<form name="hspFullSummary" id="hspFullSummary" action="" method="post">
   <input type="hidden" name="token" value="<?php echo $token;?>" />
<input type="hidden" name="pageNo" value="<?php echo $records[3]; ?>">
<table border="0" cellspacing="0" cellpadding="0" class="data-table">
<thead>

  <tr>
    <th colspan="<?php echo (!isset($oneEmployee) || $adminUser)?"4":"3"; ?>" scope="col">&nbsp;</th>
    <th colspan="2" align="center" scope="col"><?php echo $lang_Benefits_Summary_Contribution; ?></th>
    <th colspan="<?php echo ($showFsaBalance)?"4":"3"; ?>" scope="col">&nbsp;</th>
  </tr>
  <tr>
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
    <?php if ($showFsaBalance) { ?>
	<th><?php echo $lang_Benefits_Summary_Last_Year_FSA_Balance; ?> <br />($) </th>
    <?php } ?>
    <th>&nbsp;</th>
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
    <td class="<?php echo $rowStyle; ?>">
    <?php if (!isset($oneEmployee) || $adminUser) { ?>
    	<a href="?benefitcode=Benefits&action=Hsp_Expenditures&year=<?php echo $year; ?>&employeeId=<?php echo $hspSummary[$i]->getEmployeeId(); ?>">
    		<?php echo $hspSummary[$i]->getEmployeeName(); ?>
    	</a>
    <?php } ?>
	    <input type="hidden" name="hidSummaryId[]" id="" value="<?php echo $hspSummary[$i]->getSummaryId(); ?>" />
	    <input type="hidden" name="hidEmployeeId[]" id="" value="<?php echo $hspSummary[$i]->getEmployeeId(); ?>" />
    </td>
    <td class="<?php echo $rowStyle; ?>"><?php echo $hspSummary[$i]->getHspPlanName(); ?></td>
    <td class="<?php echo $rowStyle; ?>">
    	<span id="lblHspStatus<?php echo $hspSummary[$i]->getSummaryId(); ?>"><?php echo $hspSummary[$i]->getHspPlanStatusName(); ?></span>
    </td>
    <td class="<?php echo $rowStyle; ?>">
    	<span class="valueSlot">
	    <?php if ($adminUser) { ?>
	    	<input type="text" name="txtAnnualLimit[]" id="" value="<?php echo $hspSummary[$i]->getAnnualLimit(); ?>" size="6" disabled="disabled" />
	    <?php } else {
	    	echo $hspSummary[$i]->getAnnualLimit();
	    }
	    ?>
	    </span>
    </td>
    <td class="<?php echo $rowStyle; ?>">
    	<span class="valueSlot">
	    <?php
	    if ($hspSummary[$i]->getHspPlanId() == 3) {
	    	echo "NA";
	    	echo '<input type="hidden" name="txtEmployerAmount[]" value="0" />';
	    } else {
			if ($adminUser) { ?>
	    	<input type="text" name="txtEmployerAmount[]" id="" value="<?php echo $hspSummary[$i]->getEmployerAmount(); ?>" size="6" disabled="disabled" />
	    <?php } else {
	    	echo $hspSummary[$i]->getEmployerAmount();
	    	}
	     } ?>
	     </span>
    </td>
    <td class="<?php echo $rowStyle; ?>">
    	<span class="valueSlot">
		<?php
	    if ($hspSummary[$i]->getHspPlanId() == 2) {
	    	echo 'NA';
	    	echo '<input type="hidden" name="txtEmployeeAmount[]" value="0" />';
	    } else {
	    	if ($adminUser) { ?>
	    <input type="text" name="txtEmployeeAmount[]" id="" value="<?php echo $hspSummary[$i]->getEmployeeAmount(); ?>" size="6" disabled="disabled" />
	    <?php } else {
	    	echo $hspSummary[$i]->getEmployeeAmount();
	    	}
	    } ?>
	    </span>
    </td>
    <td class="<?php echo $rowStyle; ?>">
    	<span class="valueSlot">
	    <?php if ($adminUser) { ?>
	    <input type="text" name="txtTotalAccrued[]" id="" value="<?php echo $hspSummary[$i]->getTotalAccrued(); ?>" size="6" disabled="disabled" />
	    <?php } else {
	    	echo $hspSummary[$i]->getTotalAccrued();
	    }
	    ?>
	    </span>
    </td>
    <td class="<?php echo $rowStyle; ?>">
    	<span class="valueSlot">
	    <?php if ($adminUser) { ?>
	    <input type="text" name="txtTotalUsed[]" id="" value="<?php echo $hspSummary[$i]->getTotalUsed(); ?>" size="6" disabled="disabled" />
	    <?php } else {
	    	echo $hspSummary[$i]->getTotalUsed();
	    }
	    ?>
	    </span>
    </td>
    <?php if ($showFsaBalance) { ?>
	<td class="<?php echo $rowStyle; ?>">
		<span class="valueSlot"><?php if ($showFsaBalance) { echo $hspSummary[$i]->getFsaBalance(); } ?></span>
	</td>
    <?php } ?>
    <td id="buttonSlot" class="<?php echo $rowStyle; ?>">
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
    <input type="button" class="plainbtn" name="btnHspStatus[]" id="btnHspStatus<?php echo $summaryId; ?>" value="<?php echo $buttonLabel; ?>" onclick="<?php echo $onclickFunction; ?>" style="width: <?php echo $buttonWidth; ?>;" <?php echo $buttonDisabled; ?> />
    </td>
  </tr>
<?php } // Displaying summary ends ?>
</tbody>
</table>
</form>
<!-- Summary form ends -->

<?php if ($_SESSION['isAdmin'] == 'Yes') { ?>
<script type="text/javascript">
YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {
   	// Instantiate first JS Array DataSource
   	this.oACDS = new YAHOO.widget.DS_JSArray(employees);

   	// Instantiate AutoComplete for txtEmployeeSearch
   	this.oAutoComp = new YAHOO.widget.AutoComplete('txtEmployeeSearch','employeeSearchACContainer', this.oACDS);
   	this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
   	this.oAutoComp.typeAhead = false;
   	this.oAutoComp.useShadow = true;
   	this.oAutoComp.minQueryLength = 1;
   	this.oAutoComp.textboxFocusEvent.subscribe(function(){
   	    var sInputValue = YAHOO.util.Dom.get('txtEmployeeSearch').value;
   	    if(sInputValue.length === 0) {
   	        var oSelf = this;
   	        setTimeout(function(){oSelf.sendQuery(sInputValue);},0);
   	    }
   	});
};
</script>
<?php } ?>
</div>
<script type="text/javascript">
    <!--
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    -->
</script>
<?php } // HSP defined and Employees exist ?>
