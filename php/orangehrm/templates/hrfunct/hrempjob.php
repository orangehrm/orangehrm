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
if ($locRights['edit']) {
    $saveLocBtnAction= "assignLocation()";
} else {
    $saveLocBtnAction="showAccessDeniedMsg()";
}
$picDir = '../../themes/'.$styleSheet.'/pictures/';
$iconDir = '../../themes/'.$styleSheet.'/icons/';

?>
<script type="text/javascript"><!--//--><![CDATA[//><!--

   var locationNames = new Array();
<?php
    $assignedList = $this->popArr['assignedlocationList'];
    foreach($assignedList as $empLoc) {
        print "\tlocationNames['{$empLoc->getLocation()}'] = \"" . stripslashes($empLoc->getLocationName()) . "\";\n";
    }

    $availableList = $this->popArr['availablelocationList'];
    foreach($availableList as $loc) {
        print "\tlocationNames['{$loc[0]}'] = \"" . stripslashes($loc[1]) . "\";\n";
    }

?>

	function returnLocDet(){
		var popup=window.open('CentralController.php?uniqcode=CST&VIEW=MAIN&esp=1','Locations','height=450,resizable=1,scrollbars=1');
        if(!popup.opener) popup.opener=self;
	}

    function toggleEmployeeJobHistory() {
        oLayer = document.getElementById("employeeJobHistoryLayer");
        oLink = document.getElementById("toggleJobHistoryLayerLink");

        if (oLayer.style.display == 'none') {
            oLayer.style.display = 'block';
        } else {
            oLayer.style.display = 'none';
        }
        toggleEmployeeJobHistoryText();
    }

    function toggleEmployeeJobHistoryText() {
        oLayer = document.getElementById("employeeJobHistoryLayer");
        oLink = document.getElementById("toggleJobHistoryLayerLink");

        if (oLayer.style.display == 'none') {
            oLink.innerHTML = "<?php echo $lang_hremp_ShowEmployeeJobHistory; ?>";
        } else {
            oLink.innerHTML = "<?php echo $lang_hremp_HideEmployeeJobHistory; ?>";
        }
    }

	function toggleEmployeeContracts() {
		oLayer = document.getElementById("employeeContractLayer");
		oLink = document.getElementById("toggleContractLayerLink");

		if (oLayer.style.display == 'none') {
			oLayer.style.display = 'block';
		} else {
			oLayer.style.display = 'none';
		}
		toggleEmployeeContractsText();
	}

	function toggleEmployeeContractsText() {
		oLayer = document.getElementById("employeeContractLayer");
		oLink = document.getElementById("toggleContractLayerLink");

		if (oLayer.style.display == 'none') {
			oLink.innerHTML = "<?php echo $lang_hremp_ShowEmployeeContracts; ?>";
		} else {
			oLink.innerHTML = "<?php echo $lang_hremp_HideEmployeeContracts; ?>";
		}
	}

	function cmbTypeChanged()
	{
		empStatusCmb = document.getElementById("cmbType");

		if(empStatusCmb.value!='EST000')
		{
			$("tdTermDateDisc").style['display']='none';
			$("tdTermDateValue").style['display']='none';
			$("tdTermReasonDisc").style['display']='none';
			$("tdTermReasonValue").style['display']='none';
		}
		else
		{
			$("tdTermReasonDisc").style['display']='block';
			$("tdTermReasonValue").style['display']='block';
			$("tdTermDateDisc").style['display']='block';
			$("tdTermDateValue").style['display']='block';

			// Set terminated date to today (if empty)
			var termDate = document.getElementById("txtTermDate");
			if (termDate.value == YAHOO.OrangeHRM.calendar.formatHint.format) {
				today = new Date();
				termDate.value = formatDate(today, YAHOO.OrangeHRM.calendar.format);
			}

		}
	}

    /**
     * Show acccess denied message.
     */
    function showAccessDeniedMsg() {
        alert("<?php echo $lang_Error_AccessDenied; ?>")
    }

    /**
     * Run when the "add" button is clicked.
     * Shows the employee select fields
     */
    function toggleLocAddLayer() {
        var layer = document.getElementById("addLocationLayer");

        if (layer.style.display == 'block') {
            layer.style.display = 'none';
        } else {
            layer.style.display = 'block';
        }
    }

    /**
     * Run when the cancel button is pressed
     */
    function cancelLocEdit() {
        document.getElementById("addLocationLayer").style.display = 'none';
        //document.frmActivity.activityName.value = "";
        //document.frmActivity.activityId.value = "";
        addMode = true;
    }

    /**
     * Assign the location to the employee
     */
    function assignLocation() {
        var cmbLocation = $('cmbNewLocationId');

        if (cmbLocation.selectedIndex <= 0) {
            alert('<?php echo $lang_hremp_PleaseSelectALocationFirst;?>');
            return;
        } else {
            var location = cmbLocation.options[cmbLocation.selectedIndex].value;
            xajax_assignLocation(location);
            disableLocationLinks();
        }
    }

    /**
     * Assign the location to the employee
     */
    function deleteLocation(link, location) {
        xajax_removeLocation(location);
        disableLocationLinks();
    }

    /**
     * Run when a location has been assigned successfully
     * Removes location from select box and adds to assigned location list
     */
    function onLocationAssign(location) {
        var cmbLocation = $('cmbNewLocationId');

        // Remove location from select box
        var option = removeOption(cmbLocation, location);

        // add location to list
        var tbl = $('assignedLocationsTable');
        var lastRow = tbl.rows.length;
        var row = tbl.insertRow(lastRow);
        var leftCell = row.insertCell(0);
        var rightCell = row.insertCell(1);
        row.id = "locRow" + location;

        // Create location name
        var locationName = locationNames[location];
        var textNode = document.createTextNode(locationName);
        leftCell.appendChild(textNode);

        // creat location delete link
        var link = document.createElement('a');
        link.id = "locDelLink" + location;
        link.href = "javascript:deleteLocation(this, '" + location + "')";
        link.title = "<?php echo $lang_Admin_Users_delete;?>";
        var linkText = document.createTextNode('X');
        link.appendChild(linkText);

        rightCell.appendChild(link);
        rightCell.className = "locationDeleteChkBox";
    }

   /**
     * Run when a location has been removed successfully
     * Removes location from assigned list and adds to select box
     */
    function onLocationRemove(location) {

        // Remove location row
        var rowId = "locRow" + location;
        var row = $(rowId);

        var tbl = $('assignedLocationsTable');
        tbl.deleteRow(row.rowIndex);

        // Add location to select box
        var cmbLocation = $('cmbNewLocationId');

        var locationName = locationNames[location];
        var option = document.createElement('option');
        option.text = locationName;
        option.value = location;
        cmbLocation.options[cmbLocation.length] = option;
    }

    /**
     * Enable all location modify links
     */
    function enableLocationLinks() {
        enableLocationDeleteLinks();
    }

    /**
     * Disable all location modify links
     */
    function disableLocationLinks() {
        disableLocationDeleteLinks();
    }

    /**
     * Disable delete links
     */
    function disableLocationDeleteLinks() {
        var links = YAHOO.util.Dom.getElementsByClassName('locationDeleteLink');
        var numLinks = links.length;

        for(var i=0; i<numLinks;i++){
            links[i].href = "";
        }
    }

    /**
     * Enable delete links
     */
    function enableLocationDeleteLinks() {
        var links = YAHOO.util.Dom.getElementsByClassName('locationDeleteLink');
        var numLinks = links.length;

        for(var i=0; i<numLinks;i++) {
            var link = links[i];
            var location = link.id.replace("locDelLink", "");
            link.href = "javascript:deleteLocation(this, '" + location + "')";
        }
    }

    /**
     * Function run when job title selection is changed.
     */
     function onJobTitleChange(value) {
		document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait;?>....';
		xajax_fetchJobSpecInfo(value);
     }
     
    function reselectEmpStatus() {
    	empStatusSelectBox = $('cmbType');
    	jobTitleSelectBox = $('cmbJobTitle');
    	oldJobTitle = $('hidJobTitle').value;
    	
		if (oldEmpStatus != 0 || oldEmpStatus != '') {
			if (oldJobTitle == jobTitleSelectBox.options[jobTitleSelectBox.selectedIndex].value) {
			    for (i = 0; i < empStatusSelectBox.options.length; i++) {
			        if (empStatusSelectBox.options[i].value == oldEmpStatus) {
			            empStatusSelectBox.selectedIndex = i;
			            $('hidType').value = oldEmpStatus;
			            break;
			        }
			    }
			} else {
			    $('hidType').value = '0';
			}
		}
	}

//--><!]]></script>
<style type="text/css">
h3, table#assignedLocationsTable {
	margin-left:10px;
}

#jobSpecDuties {
	width:400px;
}
</style>
<?php
    $edit1 = $this->popArr['editJobInfoArr'];
    $jobSpec = $this->popArr['jobSpec'];
    if (empty($jobSpec)) {
        $jobSpecName = '';
        $jobSpecDuties = '';
    } else {
        $jobSpecName = CommonFunctions::escapeHtml($jobSpec->getName());
        $jobSpecDuties = nl2br(CommonFunctions::escapeHtml($jobSpec->getDuties()));
    }

if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') {
	$disabled = (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"';
?>
<div id="jobDetails" onclick="setUpdate(2)" onkeypress="setUpdate(2)">
	<label for="cmbJobTitle"><?php echo $lang_hremp_jobtitle; ?></label>
	<?php  ?>
	<select name="cmbJobTitle" id="cmbJobTitle" class="formSelect" <?php echo $disabled; ?> onchange="onJobTitleChange(this.value);">
		<option value="0">-- <?php echo $lang_hremp_SelectJobTitle; ?> --</option>
<?php 	$jobtit = $this->popArr['jobtit'];
		$selectedJobTitle = 0;
	for ($c=0; $jobtit && count($jobtit)>$c ; $c++)
		if(isset($this->postArr['cmbJobTitle'])) {
			if($this->postArr['cmbJobTitle'] == $jobtit[$c][0]) {
  				echo "<option selected=\"selected\" value='" . $jobtit[$c][0] . "'>" .CommonFunctions::escapeHtml($jobtit[$c][1]). "</option>";
  				$selectedJobTitle = $jobtit[$c][0];
			} else {
  				echo "<option value='" . $jobtit[$c][0] . "'>" .CommonFunctions::escapeHtml($jobtit[$c][1]). "</option>";
			}
		} elseif($edit1[0][2] == $jobtit[$c][0]) {
			echo "<option selected=\"selected\" value='" . $jobtit[$c][0] . "'>" .CommonFunctions::escapeHtml($jobtit[$c][1]). "</option>";
			$selectedJobTitle = $jobtit[$c][0];
		} else {
			echo "<option value='" . $jobtit[$c][0] . "'>" .CommonFunctions::escapeHtml($jobtit[$c][1]). "</option>";
		}
?>
	</select>
	<br class="clear" />

	<label for="cmbType"><?php echo $lang_hremp_EmpStatus; ?></label>
        <div id="empstatpp">
	<select class="formSelect" name="cmbType" id="cmbType" <?php echo $disabled; ?> onchange="javascript: cmbTypeChanged();">
		<option value="0">-- <?php echo $lang_hremp_selempstat?> --</option>
<?php
	$arrEmpType = $this->popArr['empstatlist'];
	$selectedEmpStatusValue = 0;
	for($c=0;count($arrEmpType)>$c;$c++)
		if(isset($this->postArr['cmbType'])) {
			if($this->postArr['cmbType']==$arrEmpType[$c][0]) {
				echo "<option selected=\"selected\" value='".$arrEmpType[$c][0]."'>" .CommonFunctions::escapeHtml($arrEmpType[$c][1]). "</option>";
				$selectedEmpStatusValue = $arrEmpType[$c][0];
			} else {
				echo "<option value='".$arrEmpType[$c][0]."'>" .CommonFunctions::escapeHtml($arrEmpType[$c][1]). "</option>";
			}
		} elseif($edit1[0][1]==$arrEmpType[$c][0]) {
			echo "<option selected=\"selected\" value='".$arrEmpType[$c][0]."'>" .CommonFunctions::escapeHtml($arrEmpType[$c][1]). "</option>";
			$selectedEmpStatusValue = $arrEmpType[$c][0];
		} else {
			echo "<option value='".$arrEmpType[$c][0]."'>" .CommonFunctions::escapeHtml($arrEmpType[$c][1]). "</option>";
		}

		if(count($arrEmpType) == 0) {
			$empStatDefault = new  EmploymentStatus();
			$arrDisplayEmpStat = $empStatDefault->filterEmpStat(EmploymentStatus::EMPLOYMENT_STATUS_ID_TERMINATED);
			echo "<option value='".$arrDisplayEmpStat[0][0]."'>".CommonFunctions::escapeHtml($arrDisplayEmpStat[0][1])."</option>";
		}
?>
	</select>
	<input type="hidden" name="hidJobTitle" id="hidJobTitle" value="<?php echo $selectedJobTitle; ?>" />
	<input type="hidden" name="hidType" id="hidType"value="<?php echo $selectedEmpStatusValue; ?>" />
        </div>
	<br class="clear" />

	<label><?php echo $lang_hremp_jobspec; ?></label>
	<label id="jobSpecName"><?php echo $jobSpecName;?></label>
	<br class="clear" />

	<label><?php echo $lang_hremp_jobspecduties; ?></label>
	<label id="jobSpecDuties"><?php echo $jobSpecDuties;?></label>
	<br class="clear" />

	<label for="cmbEEOCat"><?php echo $lang_hremp_eeocategory; ?></label>
	<select name="cmbEEOCat" id="cmbEEOCat" <?php echo $disabled; ?> class="formSelect">
		<option value="0">-- <?php echo $lang_hremp_seleeocat?> --</option>
<?php
	$eeojobcat = $this->popArr['eeojobcat'];
	for($c=0;$eeojobcat && count($eeojobcat)>$c;$c++) {
		if(isset($this->postArr['cmbEEOCat'])) {
		   if($this->postArr['cmbEEOCat']==$eeojobcat[$c][0]) {
				echo "<option selected=\"selected\" value='".$eeojobcat[$c][0]. "'>" . CommonFunctions::escapeHtml($eeojobcat[$c][1]) ."</option>";
		   } else {
				echo "<option value='".$eeojobcat[$c][0]. "'>" . CommonFunctions::escapeHtml($eeojobcat[$c][1]) ."</option>";
		   }
		} elseif($edit1[0][3]==$eeojobcat[$c][0]) {
			echo "<option selected=\"selected\" value='".$eeojobcat[$c][0]. "'>" . CommonFunctions::escapeHtml($eeojobcat[$c][1]) ."</option>";
		} else {
			echo "<option value='".$eeojobcat[$c][0]. "'>" . CommonFunctions::escapeHtml($eeojobcat[$c][1]) ."</option>";
		}
	}
?>
	</select>
	<br class="clear" />

	<label for=""><?php echo $lang_hremp_joindate; ?></label>
	<input type="text" class="formDateInput" name="txtJoinedDate" id="txtJoinedDate" <?php echo $disabled; ?>
		value="<?php echo (isset($this->postArr['txtJoinedDate'])) ? LocaleUtil::getInstance()->formatDate($this->postArr['txtJoinedDate']) : LocaleUtil::getInstance()->formatDate($edit1[0][5]); ?>" />
	<input type="button" <?php echo $disabled; ?> value="  " class="calendarBtn" />
	<br class="clear" />

	<label for="txtLocation"><?php echo $lang_hremp_Subdivision; ?></label>
	<input type="hidden" name="cmbLocation" value="<?php echo isset($this->postArr['cmbLocation']) ? CommonFunctions::escapeHtml($this->postArr['cmbLocation']) : CommonFunctions::escapeHtml($edit1[0][6])?>" readonly="readonly" />
	<input type="text" name="txtLocation" id="txtLocation" class="formInputText" readonly="readonly"
		value="<?php echo isset($this->postArr['txtLocation']) ? CommonFunctions::escapeHtml($this->postArr['txtLocation']) : CommonFunctions::escapeHtml($edit1[0][4])?>" />
	<label for="txtLocation">
		<input type="button" name="popLoc" value="..." onclick="returnLocDet()" <?php echo $disabled; ?> class="button" />
	</label>
	<br class="clear" />

<?php
if($_GET['reqcode'] !== "ESS") {
	$terminatinDateStyle = ($edit1[0][1] == 'EST000') ? '' : 'style="display:none"';
?>
	<label for="txtTermDate" id="tdTermDateDisc" <?php echo $terminatinDateStyle; ?>><?php echo $lang_hremp_termination_date; ?></label>
	<span id="tdTermDateValue" <?php echo $terminatinDateStyle; ?>>
		<input type="text" name="txtTermDate" id="txtTermDate" <?php echo $disabled; ?>
			class="formInputText" value="<?php echo (isset($this->postArr['txtTermDate'])) ? LocaleUtil::getInstance()->formatDate($this->postArr['txtTermDate']) : LocaleUtil::getInstance()->formatDate($edit1[0][7]); ?>" />
		<input type="button" <?php echo $disabled; ?> value="  " class="calendarBtn" name="calTermDate" id="calTermDate" />
	</span>
	<br class="clear" />
	<label for="txtTermReason" id="tdTermReasonDisc" <?php echo $terminatinDateStyle; ?>><?php echo $lang_hremp_termination_reason; ?></label>
	<span id="tdTermReasonValue" <?php echo $terminatinDateStyle; ?>>
		<textarea rows="3" cols="24" name="txtTermReason" id="txtTermReason"
			class="formTextArea" <?php echo $disabled; ?>><?php echo (isset($this->postArr['txtTermReason'])? CommonFunctions::escapeHtml($this->postArr['txtTermReason']):CommonFunctions::escapeHtml($edit1[0][8]));?></textarea>
	</span>
	<br class="clear" />
<?php } ?>

	<h3><?php echo $lang_hremp_Locations; ?></h3>
	<!-- start of list of assigned locations -->
	<table id="assignedLocationsTable">
			<tbody>
<?php
    $assignedList = $this->popArr['assignedlocationList'];
    $availableList = $this->popArr['availablelocationList'];

if (!empty($assignedList)) {
	foreach($assignedList as $empLoc) {
        $locId = $empLoc->getLocation();
?>
	    <tr id="locRow<?php echo $locId;?>" >
	        <td style="padding-right:10px;"><?php echo CommonFunctions::escapeHtml($empLoc->getLocationName()); ?></td>
<?php if ($locRights['delete']) { ?>
		        <td class="locationDeleteChkBox" style="display:none;">
		            <a class="locationDeleteLink" id="locDelLink<?php echo CommonFunctions::escapeHtml($locId);?>"
		                href="javascript:deleteLocation(this, '<?php echo stripslashes($locId);?>')"
		                title="<?php echo $lang_Admin_Users_delete;?>">X</a>
				</td>
<?php } ?>
		    </tr>
<?php
	}
}
?>
		</tbody>
	</table>
	<!-- end of list of assigned locations -->
	<br class="clear" />


</div>
<?php
}
?>
<div id ="addLocationLayer" style="display:none;height:50px;padding-left:5px;">
    <select name="cmbNewLocationId" id="cmbNewLocationId" class="formSelect" style="margin-top:10px;">
        <?php
         echo "<option value='0'> -- {$lang_hremp_SelectLocation} -- </option>";
         foreach ($availableList as $loc) {
              echo "<option value=\"{$loc[0]}\">" . CommonFunctions::escapeHtml($loc[1]) . "</option>";
         }
        ?>
    </select>

	<label for="cmbNewLocationId">
	    <input type="button" id="assignLocationButton" value="<?php echo $lang_Common_Assign; ?>" class="plainbtn"
	    	onclick="<?php echo $saveLocBtnAction; ?>;"
	        onmouseout="moutButton(this)" onmouseover="moverButton(this)" />
	</label>
</div>
<div class="formbuttons">
    <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditJob"
    	value="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
    	title="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editEmpMain(); return false;"/>
	<input type="reset" class="clearbutton" id="btnClearJob" tabindex="5"
		onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		disabled="disabled" value="<?php echo $lang_Common_Reset;?>" />
	<a href="javascript:toggleEmployeeContracts();" id="toggleContractLayerLink"><?php echo $lang_hremp_ShowEmployeeContracts; ?></a>
	<a href="javascript:toggleEmployeeJobHistory();" id="toggleJobHistoryLayerLink"><?php echo $lang_hremp_ShowEmployeeJobHistory; ?></a>

</div>

<script type="text/javascript">
	var oldEmpStatus = $('hidType').value;
</script>