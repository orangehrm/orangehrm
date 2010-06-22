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

$jobTitleHistory = $this->popArr['jobTitleHistory'];
$subDivisionHistory = $this->popArr['subDivisionHistory'];
$locationHistory = $this->popArr['locationHistory'];

$allItems = count($jobTitleHistory) + count($subDivisionHistory) + count($locationHistory);

$picDir = '../../themes/'.$styleSheet.'/pictures/';
$iconDir = '../../themes/'.$styleSheet.'/icons/';

?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--

function deleteJobHistory() {
    var check = false;
    with (document.frmEmp) {
        for (var i=0; i < elements.length; i++) {
            if (((elements[i].name == 'chklocationHistory[]')
                    || (elements[i].name == 'chksubdivisionHistory[]')
                    || (elements[i].name == 'chkjobtitHistory[]')) && (elements[i].checked == true)) {
                check = true;
                break;
            }
        }
    }

    if(!check) {
          alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
          return;
    }

    document.frmEmp.empjobHistorySTAT.value="DEL";
    qCombo(2);
}

function addJobHistoryItem() {

    err = false;
    msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

    var type = $('cmbHistoryItemType').value;

    if (type == 'JOB') {
        if ($('cmbJobTitleHistory').value == 0) {
            err = true;
            msg += "\t- <?php echo $lang_hremp_EmployeeHistory_PleaseSelectJobTitle; ?>\n";
        }
    } else if (type == 'LOC') {
        if ($('cmbLocationHistory').value == 0) {
            err = true;
            msg += "\t- <?php echo $lang_hremp_EmployeeHistory_PleaseSelectLocation; ?>\n";
        }
    } else if (type == 'SUB') {
        if ($('cmbHistorySubDiv').value == '') {
            err = true;
            msg += "\t- <?php echo $lang_hremp_EmployeeHistory_PleaseSelectSubDivision; ?>\n";
        }
    }

    var startDate = strToDate($('txtEmpHistoryItemFrom').value, YAHOO.OrangeHRM.calendar.format);
    var endDate = strToDate($('txtEmpHistoryItemTo').value, YAHOO.OrangeHRM.calendar.format);

    if (!startDate) {
        err = true;
        msg += "\t- <?php echo $lang_hremp_EmployeeHistory_PleaseSpecifyStartDate; ?>\n";
    }

    if (!endDate) {
        err = true;
        msg += "\t- <?php echo $lang_hremp_EmployeeHistory_PleaseSpecifyEndDate; ?>\n";
    }

    if(startDate && endDate && (startDate >= endDate)) {
        err = true;
        msg += "\t- <?php echo $lang_hremp_EmployeeHistory_StartShouldBeforeEnd; ?>\n";
    }

    if (err) {
        alert(msg);
        return false;
    }

    document.frmEmp.empjobHistorySTAT.value="ADD";
    qCombo(2);
}

function selectHistoryType(type) {
    if (type == 'JOB') {
        $('cmbJobTitleHistory').style.display = 'block';
        $('cmbLocationHistory').style.display = 'none';
        $('selectHistorySubDiv').style.display = 'none';
    } else if (type == 'SUB') {
        $('cmbJobTitleHistory').style.display = 'none';
        $('cmbLocationHistory').style.display = 'none';
        $('selectHistorySubDiv').style.display = 'block';
    } else if (type == 'LOC') {
        $('cmbJobTitleHistory').style.display = 'none';
        $('cmbLocationHistory').style.display = 'block';
        $('selectHistorySubDiv').style.display = 'none';
    }
}

function selectHistSubDiv() {
    var popup=window.open('CentralController.php?uniqcode=CST&VIEW=MAIN&esp=1&locInput=HistorySubDiv','Locations','height=450,width=400,resizable=1');
    if(!popup.opener) popup.opener=self;
}

function validateDateArrays(fromArray, toArray) {
    var err = false;

    var fromLen = fromArray.length;
    for (xx=0; xx<fromLen; xx++) {

        var from = fromArray[xx];
        var to = toArray[xx];
        var startDate = strToDate(from.value, YAHOO.OrangeHRM.calendar.format);
        var endDate = strToDate(to.value, YAHOO.OrangeHRM.calendar.format);

        var startBad = false;
        var endBad = false;

        if (!startDate) {
            err = true;
            startBad = true;
        }

        if (!endDate) {
            err = true;
            endBad = true;
        }

        if (startDate && endDate && (startDate > endDate)) {
            err = true;
            startBad = true;
            endBad = true;
        }

        if (startBad) {
            from.style.backgroundColor = 'red';
        } else {
            from.style.backgroundColor = 'white';
        }

        if (endBad) {
            to.style.backgroundColor = 'red';
        } else {
            to.style.backgroundColor = 'white';
        }
    }

    return err;
}

function validateEditAndSubmit() {

    var fromArray = YAHOO.util.Dom.getElementsByClassName('jobTitleHisFromDate');
    var toArray = YAHOO.util.Dom.getElementsByClassName('jobTitleHisToDate');

    var err = validateDateArrays(fromArray, toArray);

    var fromArray = YAHOO.util.Dom.getElementsByClassName('subDivHisFromDate');
    var toArray = YAHOO.util.Dom.getElementsByClassName('subDivHisToDate');

    result = validateDateArrays(fromArray, toArray);

    err = err || result;

    var fromArray = YAHOO.util.Dom.getElementsByClassName('locHisFromDate');
    var toArray = YAHOO.util.Dom.getElementsByClassName('locHisToDate');

    result = validateDateArrays(fromArray, toArray);
    err = err || result;

    if (!err) {
        document.frmEmp.empjobHistorySTAT.value="EDIT";
        qCombo(2);
    } else {
        var msg = "<?php echo $lang_hremp_EmployeeHistory_DatesWrong;?>\n";
        msg += "<?php echo $lang_hremp_EmployeeHistory_ExpectedDateFormat;?> : " + YAHOO.OrangeHRM.calendar.format + "\n";
        msg += "<?php echo $lang_hremp_EmployeeHistory_DatesWithErrorsHighlighted; ?>";
        alert(msg);
    }
}

function editJobHistory() {

    var btn = $('editSaveHistoryBtn');

    if(btn.title == '<?php echo $lang_Common_Save;?>') {
        validateEditAndSubmit();
        return;
    }

    // Enable all date entry input boxes
    var elms = YAHOO.util.Dom.getElementsByClassName('jobHistEditBox');
    for(var i=0,j=elms.length;i<j;i++){
        elms[i].style.disabled = false;
    }

    var frm=document.frmEmp;
    for (var i=0; i < frm.elements.length; i++)
        frm.elements[i].disabled = false;
    
    btn.className = 'savebutton';
	btn.value= '<?php echo $lang_Common_Save;?>';
    btn.title = '<?php echo $lang_Common_Save;?>';
}

//--><!]]></script>
<div id="employeeJobHistoryLayer" style="display:none;">

<div id="addPaneJobHistory" style="display:none">
<?php   if($locRights['add']) { ?>
    <div class="subHeading"><h3><?php echo $lang_hremp_EmployeeAddHistoryItem; ?></h3></div>

    <table style="height:80px" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="60"><?php echo $lang_Common_Add; ?></td>
          <td width="300">
            <select class="formSelect" id="cmbHistoryItemType" name="cmbHistoryItemType" onchange="selectHistoryType(this.value);">
                <option value="JOB" selected="selected" ><?php echo $lang_hremp_EmployeeJobTitleOption;?></option>
                <option value="SUB"><?php echo $lang_hremp_EmployeeSubDivisionOption;?></option>
                <option value="LOC"><?php echo $lang_hremp_EmployeeLocationOption;?></option>
            </select>
            <select class="formSelect" id="cmbJobTitleHistory" name="cmbJobTitleHistory" style="display:block;">
                <option value="0">-- <?php echo $lang_hremp_SelectJobTitle; ?> --</option>
            <?php
                $jobtit = $this->popArr['jobtit'];
                if (!empty($jobtit)) {
                    foreach ($jobtit as $jobtitle) {
                        echo "<option value='" . $jobtitle[0] . "'>" .CommonFunctions::escapeHtml($jobtitle[1]). "</option>";
                    }
                }
            ?>
            </select>
            <select class="formSelect" id="cmbLocationHistory" name="cmbLocationHistory" style="display:none;">
                <option value='0'> -- <?php echo $lang_hremp_SelectLocation;?> -- </option>
            <?php
                $locationList = $this->popArr['loc'];
                if (is_array($locationList)) {
	                foreach($locationList as $loc) {
	                    echo "<option value='" . $loc[0] . "'>" .CommonFunctions::escapeHtml($loc[1]). "</option>";
	                }
                }
            ?>
            </select>
            <span id="selectHistorySubDiv" style="display:none;">
                <input class="formInputText" type="text"  name="txtHistorySubDiv"  id="txtHistorySubDiv" value="" readonly="readonly" />
                <input type="hidden"  name="cmbHistorySubDiv" id="cmbHistorySubDiv" value="" readonly="readonly" />
                <input type="button" value="..." onclick="selectHistSubDiv()" class="button" />
            </span>
            </td>
        </tr>

        <tr>
          <td width="80"><?php echo $lang_hremp_EmployeeHistoryFrom; ?></td>
          <td>
            <input class="formDateInput" type="text" value="" name="txtEmpHistoryItemFrom" id="txtEmpHistoryItemFrom" size="12" />
            <input type="button" value="   " class="calendarBtn" /></td>
        </tr>
      <tr>
        <td valign="top"><?php echo $lang_hremp_EmployeeHistoryTo; ?></td>
        <td align="left" valign="top">
            <input class="formDateInput" type="text" value="" name="txtEmpHistoryItemTo" id="txtEmpHistoryItemTo" size="12" />
            <input type="button" value="   " class="calendarBtn" /></td>
      </tr>
    </table>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnSaveJobHist" id="btnSaveJobHist" 
    	value="<?php echo $lang_Common_Save;?>" 
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);" 
    	onclick="addJobHistoryItem(); return false;"/>
</div>	    
<?php  } ?>
</div>
	<div class="subHeading"><h3><?php echo $lang_hremp_EmployeeJobHistory; ?></h3></div>
    <input type="hidden" name="empjobHistorySTAT" value=""/>
	<div class="actionbar">
		<div class="actionbuttons">	
<?php if($locRights['add']) { ?>
		<input type="button" class="addbutton"
			onclick="showAddPane('JobHistory');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>			
<?php } ?>
<?php if ($allItems > 0) { ?>
<?php   if($locRights['delete']) { ?>
		<input type="button" class="delbutton"
			onclick="deleteJobHistory();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>	
<?php   } ?>

<?php   if($locRights['edit']) { ?>
		<input type="button" class="editbutton" id="editSaveHistoryBtn"
			onclick="editJobHistory();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Edit;?>" title="<?php echo $lang_Common_Edit;?>"/>			
<?php   } ?>
<?php } ?>
		</div>
	</div>
<!--  Start previous job titles -->
<table id="jobTitleHistoryTable" width="100%" class="historyTable">
<thead>
    <tr><th width="10"></th>
    <th><?php echo $lang_hremp_EmployeePreviousPositions;?></th>
    <th><?php echo $lang_hremp_EmployeeHistoryFrom;?></th>
    <th><?php echo $lang_hremp_EmployeeHistoryTo;?></th>
    </tr>
</thead>
<tbody>
<?php if (empty($jobTitleHistory)) { ?>
        <tr><td colspan="4"><?php echo $lang_hremp_EmployeeHistoryNoItemsFound; ?></td></tr>
<?php } else {?>

<?php
    foreach ($jobTitleHistory as $jobTitleItem) {
        $id = CommonFunctions::escapeHtml($jobTitleItem->getId());
        $code = CommonFunctions::escapeHtml($jobTitleItem->getCode());
        $name = CommonFunctions::escapeHtml($jobTitleItem->getName());
        $from = LocaleUtil::getInstance()->formatDate($jobTitleItem->getStartDate());
        $to = LocaleUtil::getInstance()->formatDate($jobTitleItem->getEndDate());
?>
    <tr id="jobTitleHistoryRow<?php echo $id;?>">
    <td width="10"><input type='checkbox' class='checkbox' name='chkjobtitHistory[]' value="<?php echo $id;?>">
    </td>
    <td><?php echo $name;?>
        <input type='hidden' name='jobTitleHisId[]' value="<?php echo $id;?>"/>
        <input type='hidden' name='jobTitleHisCode[]' value="<?php echo $code;?>"/></td>
    <td nowrap="nowrap">
        <input disabled="disabled" type="text" value="<?php echo $from;?>" name="jobTitleHisFromDate[]"
            class="jobHistEditBox noDefaultEdit jobTitleHisFromDate" size="12"/>
        <?php //<input type="button" value="   " class="calendarBtn jobHistDateBtn" style="display:none;" />?></td>
    <td nowrap="nowrap">
        <input disabled="disabled" type="text" value="<?php echo $to;?>" name="jobTitleHisToDate[]"
            class="jobHistEditBox noDefaultEdit jobTitleHisToDate" size="12"/>
        <?php //<input type="button" value="   " class="calendarBtn jobHistDateBtn" style="display:none;" />?></td>
    </tr>
<?php
    }
}
?>
    </tbody>   
    </table>
<!-- Start previous sub units -->
<table id="subDivisionHistoryTable" width="100%" class="historyTable">
<thead>
    <tr><th width="10"></th>
    <th><?php echo $lang_hremp_EmployeePreviousSubUnits;?></th>
    <th><?php echo $lang_hremp_EmployeeHistoryFrom;?></th>
    <th><?php echo $lang_hremp_EmployeeHistoryTo;?></th>
    </tr>
</thead>
<tbody>
<?php if (empty($subDivisionHistory)) { ?>
        <tr><td colspan="4"><?php echo $lang_hremp_EmployeeHistoryNoItemsFound; ?></td></tr>       
<?php } else { ?>
	
<?php
    foreach ($subDivisionHistory as $subItem) {
        $id = $subItem->getId();
        $name = CommonFunctions::escapeHtml($subItem->getName());
        $code = CommonFunctions::escapeHtml($subItem->getCode());
        $from = LocaleUtil::getInstance()->formatDate($subItem->getStartDate());
        $to = LocaleUtil::getInstance()->formatDate($subItem->getEndDate());
?>
    <tr id="subDivisionHistoryRow<?php echo $id;?>">
    <td width="10"><input type='checkbox' class='checkbox' name='chksubdivisionHistory[]' value="<?php echo $id;?>"/>
    </td>
    <td><?php echo $name;?>
        <input type='hidden' name='subDivHisId[]' value="<?php echo $id;?>"/>
        <input type='hidden' name='subDivHisCode[]' value="<?php echo $code;?>"/></td>
    <td nowrap="nowrap">
        <input disabled="disabled" type="text" value="<?php echo $from;?>" name="subDivHisFromDate[]"
            class="jobHistEditBox noDefaultEdit subDivHisFromDate" size="12"/>
    </td>
    <td nowrap="nowrap">
        <input disabled="disabled" type="text" value="<?php echo $to;?>" name="subDivHisToDate[]"
            class="jobHistEditBox noDefaultEdit subDivHisToDate" size="12"/>        
    </td>
    </tr>
<?php
    }
}
?>
</tbody>
</table>

<!-- Start previous locations -->
<table id="locationHistoryTable" width="100%" class="historyTable">
<thead>
    <tr><th width="10"></th>
    <th><?php echo $lang_hremp_EmployeePreviousLocations;?></th>
    <th><?php echo $lang_hremp_EmployeeHistoryFrom;?></th>
    <th><?php echo $lang_hremp_EmployeeHistoryTo;?></th>
    </tr>
</thead>
<tbody>
<?php if (empty($locationHistory)) { ?>
    <tr><td colspan="4"><?php echo $lang_hremp_EmployeeHistoryNoItemsFound; ?></td></tr>
<?php } else { ?>

<?php
    foreach ($locationHistory as $locItem) {
        $id = $locItem->getId();
        $code = CommonFunctions::escapeHtml($locItem->getCode());
        $name = CommonFunctions::escapeHtml($locItem->getName());
        $from = LocaleUtil::getInstance()->formatDate($locItem->getStartDate());
        $to = LocaleUtil::getInstance()->formatDate($locItem->getEndDate());
?>
    <tr id="locationHistoryRow<?php echo $id;?>">
	    <td width="10"><input type='checkbox' class='checkbox' name='chklocationHistory[]' value="<?php echo $id;?>"/>
	    </td>
	    <td><?php echo $name;?>
	        <input type='hidden' name='locHisId[]' value="<?php echo $id;?>"/>
	        <input type='hidden' name='locHisCode[]' value="<?php echo $code;?>"/></td>
	    <td nowrap="nowrap">
	        <input disabled="disabled" type="text" value="<?php echo $from;?>" name="locHisFromDate[]"
	            class="jobHistEditBox noDefaultEdit locHisFromDate" size="12"/>
	    </td>
	    <td nowrap="nowrap">
	        <input disabled="disabled" type="text" value="<?php echo $to;?>" name="locHisToDate[]"
	            class="jobHistEditBox noDefaultEdit locHisToDate" size="12"/>
	    </td>
    </tr>
<?php
    }
}
?>
</tbody>
</table>

</div>



