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

//xajax headers
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/controllers/EmpViewController.php';
require_once ROOT_PATH . '/lib/models/eimadmin/JobTitle.php';

	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];

	$arrMStat = $this->popArr['arrMStat'];

$escapedReqCode = CommonFunctions::escapeHtml($this->getArr['reqcode']);
if (isset($_GET['id'])) {
    $escapedId = CommonFunctions::escapeHtml($_GET['id']);
}
else
{
    $escapedId = '';
}

function populateStates($value) {

	$view_controller = new ViewController();
	$provlist = $view_controller->xajaxObjCall($value,'LOC','province');

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	if ($provlist) {
		$objResponse->addAssign('lrState','innerHTML','<select name="txtState" id="txtState"><option value="0">--- Select ---</option></select>');
		$objResponse = $xajaxFiller->cmbFillerById($objResponse,$provlist,1,'frmGenInfo.lrState','txtState');

	} else {
		$objResponse->addAssign('lrState','innerHTML','<input type="text" name="txtState" id="txtState" value="">');
	}
	$objResponse->addAssign('status','innerHTML','');

return $objResponse->getXML();
}

function populateDistrict($value) {

	$emp_view_controller = new EmpViewController();
	$dislist = $emp_view_controller->xajaxObjCall($value,'EMP','district');

	$objResponse = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$response = $xajaxFiller->cmbFiller($objResponse,$dislist,1,'frmEmp','cmbCity');
	$response->addAssign('status','innerHTML','');

return $response->getXML();
}

//function assEmpStat($value) {
//
//	$view_controller = new ViewController();
//	$empstatlist = $view_controller->xajaxObjCall($value,'JOB','assigned');
//
//	$objResponse = new xajaxResponse();
//	$xajaxFiller = new xajaxElementFiller();
//	$response = $xajaxFiller->cmbFiller($objResponse,$empstatlist,0,'frmEmp','cmbType',3);
//	$response->addAssign('status','innerHTML','');
//
//return $response->getXML();
//}

function fetchJobSpecInfo($value) {
	$lan = new Language();
	require ($lan->getLangPath("full.php"));

   $jobTitle=new JobTitle();
   $status=$jobTitle->getJobStatusFromTitle($value);

   $stat[]=array(0 => '', 1 => '0', 2 => "-- {$lang_hremp_selempstat} --");

   for( $i=0;$i<count($status);$i++){
        $stat[]=$status[$i];
   }

   $status=$stat;

   $view_controller = new ViewController();
   $response = new xajaxResponse();

   $xajaxFiller = new xajaxElementFiller();

   $objResponse = $xajaxFiller->cmbFillerById($response,$status,1,'frmEmp.empstatpp','cmbType');

   $jobSpec = $view_controller->getJobSpecForJob($value);

    if (empty($jobSpec)) {
        $jobSpecName = '';
        $jobSpecDuties = '';
    } else {
        $jobSpecName = CommonFunctions::escapeHtml($jobSpec->getName());
        $jobSpecDuties = nl2br(CommonFunctions::escapeHtml($jobSpec->getDuties()));
    }

    $response->addAssign('jobSpecName','innerHTML', $jobSpecName);
    $response->addAssign('jobSpecDuties','innerHTML', $jobSpecDuties);
    $response->addAssign('status','innerHTML','');

	$response->addScript('reselectEmpStatus();');

return $response->getXML();
}

function getUnAssMemberships($mtype) {

	$emp_view_controller = new EmpViewController();

	$value[0] = $_GET['id'];
	$value[1] = $mtype;

	$unAssMembership = $emp_view_controller->xajaxObjCall($value,'MEM','unAssMembership');

	$response = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$response = $xajaxFiller->cmbFiller($response,$unAssMembership,0,'frmEmp','cmbMemCode',3);
	$response->addAssign('status','innerHTML','');

return $response->getXML();
}

function getMinMaxCurrency($value, $salGrd) {

	$emp_view_controller = new EmpViewController();
	$common_func = new CommonFunctions();

	$temp[0] = $salGrd;
	$temp[1] = $_GET['id'];

	$currlist = $emp_view_controller->xajaxObjCall($temp,'BAS_FOR_PIM','currency');

	for($c=0; $c < count($currlist);$c++)
		if(isset($currlist[$c][2]) && $currlist[$c][2] == $value)
			break;

	$response = new xajaxResponse();

	if ($value === '0') {
		$response->addAssign('txtMinCurrency','value', '');
		$response->addAssign('divMinCurrency','innerHTML', '-N/A-');
		$response->addAssign('txtMaxCurrency','value', '');
		$response->addAssign('divMaxCurrency','innerHTML', '-N/A-');

	} else {
		if(isset($currlist[$c])){
			$response->addAssign('txtMinCurrency','value',$currlist[$c][3]);
			$response->addAssign('divMinCurrency','innerHTML', $common_func->formatSciNO($currlist[$c][3]));
			$response->addAssign('txtMaxCurrency','value', $currlist[$c][5]);
			$response->addAssign('divMaxCurrency','innerHTML', $common_func->formatSciNO($currlist[$c][5]));
		}
	}
return $response->getXML();
}
$GLOBALS['lang_hremp_SelectCurrency'] = $lang_hremp_SelectCurrency;
function getUnAssignedCurrencyList($payGrade,$callbackScript = null) {
	$emp_view_controller = new EmpViewController();
	$empId = $_GET['id'];

	$temp[] = $payGrade;
	$temp[] = $empId;
	if($payGrade){
		$currlist = $emp_view_controller->xajaxObjCall($temp,'BAS','currency');
	}else{
		$currlist[0][0]  = $GLOBALS['lang_hremp_SelectCurrency'] ;
		$currlist[0][2]  = "0";
	}

	$response = new xajaxResponse();
	$xajaxFiller = new xajaxElementFiller();
	$xajaxFiller->setDefaultOptionName('select_currency');
	$response = $xajaxFiller->cmbFiller2($response, $currlist, 0, 2, 'frmEmp', 'cmbCurrCode', 0);
	$response->addAssign('status','innerHTML','');
	$response->addScript("getUnAssignedCurrencyListCallback('$payGrade')");

	return $response->getXML();
}


$GLOBALS['lang_Common_Select'] = $lang_Common_Select;
$GLOBALS['lang_hremp_ErrorAssigningLocation'] = $lang_hremp_ErrorAssigningLocation;

/**
 * Assign location to employee
 * @param string $locCode Location code
 */
function assignLocation($locCode) {

    $empViewController = new EmpViewController();
    $result = $empViewController->assignLocation($_GET['id'], $locCode);

    $response = new xajaxResponse();
    if ($result) {
        $response->addScript('onLocationAssign("' . $locCode. '");');
    } else {
        $response->addScript('alert("' . $GLOBALS['lang_hremp_ErrorAssigningLocation'] .'");');
    }

    $xajaxFiller = new xajaxElementFiller();
    $response->addAssign('status','style','display:none;');
    $response->addScript('enableLocationLinks();');

    return $response->getXML();
}

/**
 * Remove location from employee
 * @param string $locCode Location code
 */
function removeLocation($locCode) {

    $empViewController = new EmpViewController();
    $result = $empViewController->removeLocation($_GET['id'], $locCode);

    $response = new xajaxResponse();
    if ($result) {
       $response->addScript('onLocationRemove("' . $locCode. '");');
    } else {
        $response->addScript('alert("' . $GLOBALS['lang_hremp_ErrorAssigningLocation'] .'");');
    }

    $xajaxFiller = new xajaxElementFiller();
    $response->addAssign('status','style','display:none;');
    $response->addScript('enableLocationLinks();');

    return $response->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->registerFunction('populateDistrict');
//$objAjax->registerFunction('assEmpStat');
$objAjax->registerFunction('fetchJobSpecInfo');
$objAjax->registerFunction('getUnAssMemberships');
$objAjax->registerFunction('getMinMaxCurrency');
$objAjax->registerFunction('getUnAssignedCurrencyList');
$objAjax->registerFunction('assignLocation');
$objAjax->registerFunction('removeLocation');

$objAjax->processRequests();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>OrangeHRM - Employee Details</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php
$objAjax->printJavascript();



include ROOT_PATH."/lib/common/calendar.php"; ?>

<script type="text/javascript"><!--//--><![CDATA[//><!--

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var z,i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; z=(v=='show')?3:(v=='hide')?2:2; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; obj.zIndex=z; }
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}


<?php	if (($locRights['add']) || ($escapedReqCode === "ESS")) { ?>
function addEmpMain() {

	var cnt = document.frmEmp.txtEmpLastName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('<?php echo $lang_Error_LastNameNumbers?>')) {
		cnt.focus();
		return;
	}  if (cnt.value == '') {
		alert('<?php echo $lang_Error_LastNameEmpty?>');
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpFirstName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('<?php echo $lang_Error_FirstNameNumbers?>')) {
		cnt.focus();
		return;
	} else if (cnt.value == '') {
		alert('<?php echo $lang_Error_FirstNameEmpty?>');
		cnt.focus();
		return;
	}



	document.frmEmp.sqlState.value = "NewRecord";
	document.frmEmp.submit();
}
<?php } else { ?>

function addEmpMain() {
	alert('<?php echo $lang_Common_AccessDenied;?>');
}
<?php } ?>


function goBack() {
	location.href = "./CentralController.php?reqcode=<?php echo $escapedReqCode; ?>&VIEW=MAIN<?php echo (isset($this->getArr['currentPage'])) ? "&pageNO=" . CommonFunctions::escapeHtml($this->getArr['currentPage']) : ""; ?>";
}

function editEmpMain() {
<?php if (($locRights['edit']) || ($escapedReqCode === "ESS")) {?>

	var lockedEl = Array(100);
	var lockEmpCont = false;


	if(document.frmEmp.EditMode.value=='1') {
		updateEmpMain();
		return;
	}

	var frm=document.frmEmp;

	for (var i=0; i < frm.elements.length; i++) {
		if (frm.elements[i].type == "hidden")
			frm.elements[i].disabled=false;

		<?php

			  $supervisorEMPMode = false;
			  if ((isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor']) && (isset($escapedReqCode) && ($escapedReqCode === "EMP")) ) {
			      $supervisorEMPMode = true;
			  }

		      /* If admin or supervisor in EMP page */
			  if ((isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 'Yes')) || $supervisorEMPMode ) { ?>

        if (frm.elements[i].className.indexOf('noDefaultEdit') == -1) {
		  frm.elements[i].disabled=false;
        }

		<?php } ?>
	}

        <?php

          $allowLocationDelete = false;
          $allowLocationEdit = false;
          if ($supervisorEMPMode) {
              $allowLocationDelete = true;
              $allowLocationEdit = true;
          } else if (isset($_SESSION['isAdmin']) && ($_SESSION['isAdmin'] == 'Yes')) {
              $allowLocationDelete = $locRights['delete'];
              $allowLocationEdit = $locRights['edit'];
          }

          // display location modifying link
          if ($allowLocationEdit) {
         ?>
            var addLocationLayer = document.getElementById("addLocationLayer");
            if (addLocationLayer) {
                addLocationLayer.style.display = 'block';
            }
        <?php } ?>
        <?php
          // Show deletion check boxes
          if ($allowLocationDelete) {
         ?>
            var elms = YAHOO.util.Dom.getElementsByClassName('locationDeleteChkBox');
            // loop over all the elements
            for(var i=0,j=elms.length;i<j;i++){
                elms[i].style.display = 'block';
            }

        <?php } ?>

		<?php
		/* form elements disabled only for supervisor mode */
		if ($supervisorEMPMode) { ?>

			disableArr = new Array(	'cmbType',
									'cmbRepEmpID',
									'cmbRepMethod',
									'cmbRepType',
									'txtBasSal',
									'cmbCurrCode');

			for (j=0; j<disableArr.length; j++) {
				if (frm[disableArr[j]]) {
					frm[disableArr[j]].disabled = true;
				}
			}

		<?php } ?>


		<?php if (isset($escapedReqCode) && ($escapedReqCode === "ESS")) { ?>
		enableArr = new Array(	'txtEmpFirstName',
								'txtEmpMiddleName',
								'txtEmpLastName',
								'txtEmpNickName',
								"txtOtherID",
								'cmbCountry',
								'txtEConName',
								"btnBrowser",
								"chkSmokeFlag",
								"txtMilitarySer",
								"cmbNation",
								"cmbMarital",
								"cmbEthnicRace",
								"optGender",
								"btnLicExpDate",
								"txtLicExpDate",
								"txtState",
								"cmbCity",
								"txtHmTelep",
								"txtWorkTelep",
								"txtOtherEmail",
								"txtStreet1",
								"txtStreet2",
								"txtzipCode",
								"txtMobile",
								"txtWorkEmail",
								"txtEConRel",
								"txtEConHmTel",
								"txtEConMobile",
								"txtEConWorkTel",
								"txtEConName");

		for (j=0; j<enableArr.length; j++) {
			if (frm[enableArr[j]]) {
				if (frm[enableArr[j]].length) {
					for (i=0; i<frm[enableArr[j]].length; i++) {
						frm[enableArr[j]][i].disabled = false;
					}
				}
				frm[enableArr[j]].disabled = false;
			}
		}

		<?php } ?>

    /* Enable clear buttons */
    var clearButtons = YAHOO.util.Dom.getElementsByClassName('clearbutton');
    for(var i=0; i < clearButtons.length; i++) {
        clearButtons[i].disabled = false;
    }

    /* Convert edit buttons to save buttons */
    var editButtons = YAHOO.util.Dom.getElementsByClassName('editbutton');
    for(var i=0; i < editButtons.length; i++) {
        editButtons[i].value="<?php echo $lang_Common_Save; ?>";
        editButtons[i].title="<?php echo $lang_Common_Save; ?>";
        editButtons[i].className = "savebutton";
    }

	document.frmEmp.EditMode.value='1';

<?php } else { ?>
	alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
}

function updateEmpMain() {
	var cnt = document.frmEmp.txtEmpLastName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('<?php echo $lang_Error_LastNameNumbers?>')) {
		cnt.focus();
		return;
	}  if (cnt.value == '') {
		alert('<?php echo $lang_Error_LastNameEmpty?>');
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpFirstName;
	if(!(cnt.value == '') && !alpha(cnt) && !confirm('<?php echo $lang_Error_FirstNameNumbers?>')) {
		cnt.focus();
		return;
	} else if (cnt.value == '') {
		alert('<?php echo $lang_Error_FirstNameEmpty?>');
		cnt.focus();
		return;
	}

	var cnt = document.frmEmp.txtEmpMiddleName;


    // contact details validation
    if( document.frmEmp.contactFlag.value == '1' ){

        // check work email
        var workEmail = document.frmEmp.txtWorkEmail.value;
        if (workEmail != '') {
            if( !checkEmail(workEmail) ){
                alert ('<?php echo $lang_Errro_WorkEmailIsNotValid; ?>');
                return false;
            }
        }

        // txtOtherEmail
        var otherEmail = document.frmEmp.txtOtherEmail.value;
        if (otherEmail != '') {
            if( !checkEmail(otherEmail) ){
                alert ('<?php echo $lang_Errro_OtherEmailIsNotValid; ?>');
                return false;
            }
        }
    }



	var cntrl = document.frmEmp.txtHmTelep;
	if(cntrl.value != '' && !checkPhone(cntrl)) {
		alert('<?php echo "$lang_hremp_hmtele : $lang_hremp_InvalidPhone"; ?>');
		cntrl.focus();
		return;
	}

	var cntrl = document.frmEmp.txtMobile;
	if(cntrl.value != '' && !checkPhone(cntrl)) {
		alert('<?php echo "$lang_hremp_mobile : $lang_hremp_InvalidPhone"; ?>');
		cntrl.focus();
		return;
	}

	var cntrl = document.frmEmp.txtWorkTelep;
	if(cntrl.value != '' && !checkPhone(cntrl)) {
		alert('<?php echo "$lang_hremp_worktele : $lang_hremp_InvalidPhone"; ?>');
		cntrl.focus();
		return;
	}

	var cntrl = document.frmEmp.taxFederalExceptions;
	if(cntrl.value != '' && !numbers(cntrl)) {
		alert('<?php echo "$lang_hrEmpMain_FederalIncomeTax $lang_hrEmpMain_TaxExemptions : $lang_Error_FieldShouldBeNumeric"; ?>');
		cntrl.focus();
		return;
	}

	var cntrl = document.frmEmp.taxStateExceptions;
	if(cntrl.value != '' && !numbers(cntrl)) {
		alert('<?php echo "$lang_hrEmpMain_StateIncomeTax $lang_hrEmpMain_TaxExemptions : $lang_Error_FieldShouldBeNumeric"; ?>');
		cntrl.focus();
		return;
	}

	document.getElementById("cmbProvince").value=document.getElementById("txtState").value;
	document.frmEmp.sqlState.value = "UpdateRecord";
	document.frmEmp.submit();
}


<?php if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) { 	?>
		function reLoad() {
			location.href ="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $escapedId;?>&capturemode=updatemode&reqcode=<?php echo $escapedReqCode;?>";
		}
<?php } ?>

 function qCombo(lblPane) {
	document.frmEmp.pane.value=lblPane;
	document.frmEmp.submit();
}

function displayLayer(panelNo) {

	hasChanges = (panelNo != 1 && document.frmEmp.personalFlag.value == '1');
	hasChanges |= (panelNo != 2 && document.frmEmp.jobFlag.value == '1');
	hasChanges |= (panelNo != 4 && document.frmEmp.contactFlag.value == '1');
	hasChanges |= (panelNo != 18 && document.frmEmp.taxFlag.value == '1');
	hasChanges |= (panelNo != 20 && document.frmEmp.customFlag.value == '1');
	hasChanges |= (panelNo != 21 && document.frmEmp.photoFlag.value == '1')
  	if(hasChanges) {

  		if(confirm("<?php echo $lang_Error_ChangePane?>")) {
  			editEmpMain();
  			if( !updateEmpMain() ){
                return;
            }
  		} else {
  			document.frmEmp.personalFlag.value=0;
  			document.frmEmp.jobFlag.value=0;
  			document.frmEmp.contactFlag.value=0;
  			document.frmEmp.taxFlag.value=0;
  			document.frmEmp.customFlag.value=0;
  		}
  	}

	switch(panelNo) {
          	case 1 : showPane('personal');break;
          	case 2 : showPane('job');break;
          	case 3 : showPane('dependents');break;
          	case 4 : showPane('contacts'); break;
          	case 5 : showPane('emgcontacts'); break;
          	case 6 : showPane('attachments'); break;
          	case 7 : break;
          	case 8 : break;
          	case 9 : showPane('education'); break;
          	case 10 : showPane('immigration'); break;
          	case 11 : showPane('languages'); break;
          	case 12 : showPane('licenses'); break;
          	case 13 : showPane('memberships'); break;
          	case 14 : showPane('payments'); break;
          	case 15 : showPane('report-to'); break;
          	case 16 : showPane('skills'); break;
          	case 17 : showPane('work-experiance'); break;
          	case 18 : showPane('tax'); break;
          	case 19 : showPane('direct-debit'); break;
          	case 20 : showPane('custom'); break;
          	case 21 : showPane('photo'); break;
	}

	document.frmEmp.pane.value = panelNo;
}

function showPane(paneId) {
	var allPanes = new Array('personal','job','dependents','contacts','emgcontacts','attachments','education','immigration','languages','licenses',
				'memberships','payments','report-to','skills','work-experiance', 'tax', 'direct-debit','custom', 'photo');
	var numPanes = allPanes.length;
	for (i=0; i< numPanes; i++) {
	    pane = allPanes[i];
	    if (pane != paneId) {
	    	var paneDiv = $(pane);
	    	if (paneDiv!= null && paneDiv.className.indexOf('currentpanel') > -1) {
	    		paneDiv.className = paneDiv.className.replace(/\scurrentpanel\b/,'');
	    	}

	    	// style link
	    	var link = $(pane + 'Link');
	    	if (link && (link.className.indexOf('current') > -1)) {
	    	    link.className = '';
	    	}
	    }
	}

	var currentPanel = $(paneId);
	if (currentPanel != null && currentPanel.className.indexOf('currentpanel') == -1) {
		currentPanel.className += ' currentpanel';
	}
	var currentLink = $(paneId + 'Link');
	if (currentLink && (currentLink.className.indexOf('current') == -1)) {
	    currentLink.className = 'current';
	}

}

function setUpdate(opt) {

		switch(eval(opt)) {
          	case 0 : document.frmEmp.main.value=1; break;
          	case 1 : document.frmEmp.personalFlag.value=1; break;
          	case 2 : document.frmEmp.jobFlag.value=1; break;
            case 4 : document.frmEmp.contactFlag.value=1; break;
            case 18: document.frmEmp.taxFlag.value=1; break;
            case 20: document.frmEmp.customFlag.value=1; break;
		}
		document.frmEmp.pane.value = opt;
}


function showPhotoHandler() {
	displayLayer(21);
}

function resetAdd(panel, add) {
	document.frmEmp.action = document.frmEmp.action;
	document.frmEmp.pane.value = panel;
	document.frmEmp.txtShowAddPane.value = add;
	document.frmEmp.submit();
}

function showAddPane(paneName) {
	YAHOO.OrangeHRM.container.wait.show();

	addPane = document.getElementById('addPane'+paneName);
	editPane = document.getElementById('editPane'+paneName);
	parentPane = document.getElementById('parentPane'+paneName);

	if (addPane && addPane.style) {
		addPane.style.display = tableDisplayStyle;
	} else {

		resetAdd(document.frmEmp.pane.value, paneName);
		return;
	}

	if (editPane && parentPane) {
		parentPane.removeChild(editPane);
	}

	YAHOO.OrangeHRM.container.wait.hide();
}

function showHideSubMenu(link) {

    var uldisplay;
	var newClass;

	if (link.className == 'expanded') {

		// Need to hide
	    uldisplay = 'none';
	    newClass = 'collapsed';

	} else {

		// Need to show
	    uldisplay = 'block';
	    newClass = 'expanded';
	}

    var parent = link.parentNode;
    uls = parent.getElementsByTagName('ul');
	for(var i=0; i<uls.length; i++) {
	    ul = uls[i].style.display = uldisplay;
	}

	link.className = newClass;
}

tableDisplayStyle = "table";
//--><!]]></script>
<!--[if IE]>
<script type="text/javaScript">
	tableDisplayStyle = "block";
</script>
<![endif]-->

<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<style type="text/css">
    <!--

	:disabled:not([type="image"]) {
		background-color:#FFFFFF;
		color:#444444;
	}

	input[type=text] {
		border-top: 0px;
		border-left: 0px;
		border-right: 0px;
		border-bottom: 1px solid #888888;
	}

    table.historyTable th {
        border-width: 0px;
        padding: 3px 3px 3px 5px;
        text-align: left;
    }
    table.historyTable td {
        border-width: 0px;
        padding: 3px 3px 3px 5px;
        text-align: left;
    }

	input.fileselect {
		margin:8px 0px 4px 10px;
	}

    .locationDeleteChkBox {
        padding:2px 4px 2px 4px;
        border-style: solid;
        border-width: thin;
        display:block;
    }

	.pimpanel {
	    position:absolute;
	    left:-9999px;
	}
	.currentpanel {
		margin-top: 10px;
<?php if ($_SESSION['PIM_MENU_TYPE'] == 'left') { ?>
	    left:190px;
<?php } else { ?>
	    left:130px;
<?php } ?>
	}
	#photodiv {
		margin-top:19px;
	    float:left;
	    text-align:center;
	    margin-left: 650px;
	    padding: 2px;
	    border: 1px solid #FAD163;
	}
	#photodiv span {
	    color: black;
	    font-weight: bold;
	}

	#empname {
	    display:block;
	    color: black;
	}

	#personalIcons,
	#employmentIcons,
	#qualificationIcons {
	    display:block;
	    position:absolute;
	    left:-999px;
	   	width:400px;
	   	text-align:center;
	   	padding-left:100px;
	   	padding-right:100px;
	}

	#icons div a {
	    display:block;
	    float:left;
		height: 50px;
		width: 54px;
	    text-decoration:none;
		text-align:center;
	    vertial-align:bottom;
		padding-top: 45px;
		outline: 0;
		background-position: top center;
		margin-left:8px;
		margin-right:8px;
	}

	#icons div a:hover {
	    color: black;
	    text-decoration: underline;
	}

	#icons div a.current {
	    font-weight: bold;
	    color:black;
	    cursor:default;
	}

	#icons div a.current:hover {
	    color:black;
	    text-decoration:none;
	}

	#icons {
	    display:block;
	    clear:both;
	    margin-left: 130px;
	    margin-top: 5px;
	    margin-bottom: 2px;#FAD163
	    width:500px;
		height: 60px;
	}
	#pimleftmenu {
	    display:block;
	    float: left;
	    background: #FFFBED;
	    padding: 2px 2px 2px 2px;
	    margin: 10px 0px 0px 5px;
	}
	#pimleftmenu ul {
	    list-style-type: none;
	    padding-left: 0;
	    margin-left: 0;
	    width: 12em;
	}

	#pimleftmenu ul.pimleftmenu li {
	    list-style-type:none;
	    margin-left: 0;
	    margin-bottom: 1px;
		padding-left:5px;
	}

	#pimleftmenu ul li.parent {
	    padding-left: 0px;
	    padding-top:4px;
	    font-weight: bold;
	}

	#pimleftmenu ul.pimleftmenu li a {
	    display:block;
	    outline: 0;
		padding: 2px 2px 2px 4px;
		text-decoration: none;
		background:#FAD163 none repeat scroll 0 0;
		border-color:#CD8500 #8B5A00 #8B5A00 #CD8500;
		border-style:solid;
		border-width:1px;
		color:#d87415;
		font-size: 11px;
		font-weight:bold;
		text-align: left;
	}
	#pimleftmenu ul.pimleftmenu li a:hover {
		color: #FFFBED;
		background-color: #e88d1e;
	}

	#pimleftmenu ul.pimleftmenu li a.current {
		color: #FFFBED;
		background-color: #e88d1e;
	}

	#pimleftmenu ul.pimleftmenu li a.collapsed,
	#pimleftmenu ul.pimleftmenu li a.expanded {
	    display:block;
	    outline: 0;
		padding: 2px 2px 2px 4px;
		text-decoration: none;
		border: 0 ;
		color: #CC6600;
		font-size: 11px;
		font-weight:bold;
		text-align: left;
	}

	#pimleftmenu ul.pimleftmenu li a.expanded {
		background: #FFFBED url(../../themes/orange/icons/expanded.gif) no-repeat center right;
	}

	#pimleftmenu ul.pimleftmenu li a.collapsed {
		background: #FFFBED url(../../themes/orange/icons/collapsed.gif) no-repeat center right;
		border-bottom: 1px solid #d87415;
	}

	#pimleftmenu ul.pimleftmenu li a.collapsed:hover span,
	#pimleftmenu ul.pimleftmenu li a.expanded:hover span {
		color: #8d4700;
	}


	#pimleftmenu ul span {
	    display:block;
	}

	#pimleftmenu li.parent span.parent {
		color: #CC6600;
	}

	#pimleftmenu ul span span {
	    display:inline;
	    text-decoration:underline;
	}

	div.requirednotice {
	    margin-left: 15px;
	}

	#parentPaneDependents {
	    float:left;
		width: 50%;
	}

	#parentPaneChildren {
	    float:left;
	    width: 50%;
	}
    -->
</style>
<!--[if IE]>
<style type="text/css">

	/* following style may not be needed */
	#pimleftmenu ul.pimleftmenu {
	    height:auto;
	}

	/* Give layout in IE (hasLayout) */
	#pimleftmenu a {
	    zoom: 1;
	}

</style>
<![endif]-->

</head>
<body>
<script type="text/javaScript"><!--//--><![CDATA[//><!--
  YAHOO.OrangeHRM.container.init();
//--><!]]></script>
<?php
 if (!isset($this->getArr['pane'])) {
 	$this->getArr['pane'] = 1;
 };
 if (!isset($this->postArr['pane'])) {
 	$this->postArr['pane'] = $this->getArr['pane'];
 };
 ?>
<div id="cal1Container"></div>
<?php
	 if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 	$first = $this->popArr['editMainArr'][0][2];
	 	$last = $this->popArr['editMainArr'][0][1];
	 	$middle = $this->popArr['editMainArr'][0][3];
	 	$currentEmployeeName = $first . ' ' . $middle . ' ' . $last;
	 }

?>
<div align="right" id="status" style="display: none;"><img src="../../themes/beyondT/icons/loading.gif" alt="" width="20" height="20" style="vertical-align:bottom;"/> <span style="vertical-align:text-top"><?php echo $lang_Common_LoadingPage; ?>...</span></div>

<div class="navigation">
<?php if (isset($this->popArr['showBackButton']) && $this->popArr['showBackButton']) { ?>
	<input type="button" class="backbutton" value="<?php echo $lang_Common_Back; ?>" onclick="goBack()" />
<?php } ?>
</div>

<?php	if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) { ?>
<form name="frmEmp" id="frmEmp" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $escapedReqCode;?>&amp;capturemode=<?php echo CommonFunctions::escapeHtml($this->getArr['capturemode'])?>" enctype="multipart/form-data">
<?php
	} elseif ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	$edit = $this->popArr['editMainArr'];
?>
<form name="frmEmp" id="frmEmp" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $escapedId;?>&amp;reqcode=<?php echo $escapedReqCode;?>&amp;capturemode=<?php echo CommonFunctions::escapeHtml($this->getArr['capturemode'])?>" enctype="multipart/form-data">
<?php } ?>

<?php
    $escapedPane = isset($this->postArr['pane']) ? CommonFunctions::escapeHtml($this->postArr['pane']) : '';
?>
<input type="hidden" name="empToken" value="<?php  echo isset($this->popArr['empToken'])?$this->popArr['empToken']:'';?>" />
<input type="hidden" name="sqlState" />
<input type="hidden" name="pane" value="<?php echo (isset($escapedPane) && $escapedPane!='')?$escapedPane:''?>" />
<input type="hidden" name="txtShowAddPane" />

<input type="hidden" name="main" value="<?php echo isset($this->postArr['main'])? CommonFunctions::escapeHtml($this->postArr['main']) : '0'?>" />
<input type="hidden" name="personalFlag" value="<?php echo isset($this->postArr['personalFlag'])? CommonFunctions::escapeHtml($this->postArr['personalFlag']) : '0'?>" />
<input type="hidden" name="jobFlag" value="<?php echo isset($this->postArr['jobFlag'])? CommonFunctions::escapeHtml($this->postArr['jobFlag']) : '0'?>" />

<input type="hidden" name="dependentFlag" value="<?php echo isset($this->postArr['dependentFlag'])? CommonFunctions::escapeHtml($this->postArr['dependentFlag']) : '0'?>" />
<input type="hidden" name="childrenFlag" value="<?php echo isset($this->postArr['childrenFlag'])? CommonFunctions::escapeHtml($this->postArr['childrenFlag']) : '0'?>" />
<input type="hidden" name="contactFlag" value="<?php echo isset($this->postArr['contactFlag'])? CommonFunctions::escapeHtml($this->postArr['contactFlag']) : '0'?>" />
<input type="hidden" name="econtactFlag" value="<?php echo isset($this->postArr['econtactFlag'])? CommonFunctions::escapeHtml($this->postArr['econtactFlag']) : '0'?>" />
<input type="hidden" name="cash-benefitsFlag" value="<?php echo isset($this->postArr['cash-benefitsFlag'])? CommonFunctions::escapeHtml($this->postArr['cash-benefitsFlag']) : '0'?>" />
<input type="hidden" name="noncash-benefitsFlag" value="<?php echo isset($this->postArr['noncash-benefitsFlag'])? CommonFunctions::escapeHtml($this->postArr['noncash-benefitsFlag']) : '0'?>" />
<input type="hidden" name="educationFlag" value="<?php echo isset($this->postArr['educationFlag'])? CommonFunctions::escapeHtml($this->postArr['educationFlag']) : '0'?>" />
<input type="hidden" name="immigrationFlag" value="<?php echo isset($this->postArr['immigrationFlag'])? CommonFunctions::escapeHtml($this->postArr['immigrationFlag']) : '0'?>" />
<input type="hidden" name="languageFlag" value="<?php echo isset($this->postArr['languageFlag'])? CommonFunctions::escapeHtml($this->postArr['languageFlag']) : '0'?>" />
<input type="hidden" name="licenseFlag" value="<?php echo isset($this->postArr['licenseFlag'])? CommonFunctions::escapeHtml($this->postArr['licenseFlag']) : '0'?>" />
<input type="hidden" name="membershipFlag" value="<?php echo isset($this->postArr['membershipFlag'])? CommonFunctions::escapeHtml($this->postArr['membershipFlag']) : '0'?>" />
<input type="hidden" name="paymentFlag" value="<?php echo isset($this->postArr['paymentFlag'])? CommonFunctions::escapeHtml($this->postArr['paymentFlag']) : '0'?>" />
<input type="hidden" name="report-toFlag" value="<?php echo isset($this->postArr['report-toFlag'])? CommonFunctions::escapeHtml($this->postArr['report-toFlag']) : '0'?>" />
<input type="hidden" name="skillsFlag" value="<?php echo isset($this->postArr['skillsFlag'])? CommonFunctions::escapeHtml($this->postArr['skillsFlag']) : '0'?>" />
<input type="hidden" name="work-experianceFlag" value="<?php echo isset($this->postArr['work-experianceFlag'])? CommonFunctions::escapeHtml($this->postArr['work-experianceFlag']) : '0'?>" />
<input type="hidden" name="taxFlag" value="<?php echo isset($this->postArr['taxFlag'])? CommonFunctions::escapeHtml($this->postArr['taxFlag']) : '0'?>" />
<input type="hidden" name="direct-debitFlag" value="<?php echo isset($this->postArr['direct-debitFlag'])? CommonFunctions::escapeHtml($this->postArr['direct-debitFlag']) : '0'?>" />
<input type="hidden" name="customFlag" value="<?php echo isset($this->postArr['customFlag'])? CommonFunctions::escapeHtml($this->postArr['customFlag']) : '0'?>" />
<input type="hidden" name="photoFlag" value="<?php echo isset($this->postArr['photoFlag'])? CommonFunctions::escapeHtml($this->postArr['photoFlag']) : '0'?>" />
<input type="hidden" name="attSTAT" value="" />
<input type="hidden" name="EditMode" value="<?php echo isset($this->postArr['EditMode'])? CommonFunctions::escapeHtml($this->postArr['EditMode']) : '0'?>" />

<?php
	if (isset($this->getArr['message'])) {

		$expString  = $this->getArr['message'];
		$col_def = CommonFunctions::getCssClassForMessage($expString);
?>
	<p align="right">
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif;" style="margin-right:10px">
			<?php echo eval('return $lang_empview_'.$expString.';'); ?>
		</font>
	</p>
<?php } ?>

<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'addmode') {
	$disabled = $locRights['add'] ? '':'disabled="disabled"';
?>

<div class="formpage2col">

	<div class="outerbox">
		<div class="mainHeading"><h2><?php echo $lang_Menu_Pim . ' : ' . $lang_pim_AddEmployee;?></h2></div>
		<label for="txtEmployeeId"><?php echo $lang_Commn_code; ?></label>
		<input name="txtEmployeeId" id="txtEmployeeId" class="formInputText" type="text" value="<?php echo $this->popArr['newID']?>" maxlength="50"/>
		<br class="clear"/>

		<label for="txtEmpLastName"><?php echo $lang_hremp_EmpLastName?> <span class="required">*</span></label>
		<input type="text" name="txtEmpLastName" id="txtEmpLastName" class="formInputText" <?php echo $disabled;?> maxlength="100"
			value="<?php echo isset($this->postArr['txtEmpLastName']) ? CommonFunctions::escapeHtml($this->postArr['txtEmpLastName']):'';?>"/>

		<label for="txtEmpFirstName" id="txtEmpFirstName"><?php echo $lang_hremp_EmpFirstName?> <span class="required">*</span></label>
		<input type="text" name="txtEmpFirstName" id="txtEmpFirstName" class="formInputText" <?php echo $disabled;?>  maxlength="100"
			value="<?php echo (isset($this->postArr['txtEmpFirstName']))?CommonFunctions::escapeHtml($this->postArr['txtEmpFirstName']):''?>" />
		<br class="clear" />

		<label for="txtEmpMiddleName"><?php echo $lang_hremp_EmpMiddleName; ?></label>
		<input type="text" name="txtEmpMiddleName" id="txtEmpMiddleName" class="formInputText"  maxlength="100"
						<?php echo $disabled; ?> value="<?php echo (isset($this->postArr['txtEmpMiddleName']))?CommonFunctions::escapeHtml($this->postArr['txtEmpMiddleName']):''?>"/>
		<label for="txtEmpNickName"><?php echo $lang_hremp_nickname; ?></label>
		<input type="text" name="txtEmpNickName" id="txtEmpNickName" class="formInputText" <?php echo $disabled;?>  maxlength="100"
			value="<?php echo (isset($this->postArr['txtEmpNickName']))?CommonFunctions::escapeHtml($this->postArr['txtEmpNickName']):''?>"/>
		<label for="photofile" ><?php echo $lang_hremp_photo; ?></label>
		<input type="file" name="photofile" id="photofile" class="fileselect" <?php echo $disabled;?>
			value="<?php echo (isset($this->postArr['photofile']))?CommonFunctions::escapeHtml($this->postArr['photofile']):''?>" />
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		<br class="clear"/>
        <div class="formbuttons">
			<input type="button" class="savebutton" id="btnEdit" onclick="addEmpMain(); return false;"
				onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				value="<?php echo $lang_Common_Save;?>" title="<?php echo $lang_Common_Save;?>" />
			<input type="button" class="resetbutton" onclick="document.frmEmp.reset(); return false;"
				onmouseover="moverButton(this);" onmouseout="moutButton(this);"
				 value="<?php echo $lang_Common_Reset;?>" />
        </div>
	</div>
	<div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
</div>
<?php } elseif(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

<?php if ($_SESSION['PIM_MENU_TYPE']=='left') { ?>
<div id="pimleftmenu">
	<ul class="pimleftmenu">
		<li class="l1 parent">
			<a href="#" class="expanded" onclick="showHideSubMenu(this);">
                <span class="parent personal"><?php echo $lang_pim_tabs_Personal;?></span></a>
			<ul class="l2">
				<li class="l2">
					<a href="javascript:displayLayer(1)" id="personalLink" class="personal" accesskey="p">
						<span><?php echo $lang_pim_PersonalDetails;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(4)" id="contactsLink" class="personal" accesskey="c">
						<span><?php echo $lang_pim_tabs_Contact;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(5)" id="emgcontactsLink" class="personal"  accesskey="e">
						<span><?php echo $lang_pim_tabs_EmergencyContacts;?></span></a></li>

				<li class="l2">
					<a href="javascript:displayLayer(3)" id="dependentsLink" class="personal"  accesskey="d">
						<span><?php echo $lang_pim_tabs_Dependents;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(10)" id="immigrationLink" class="personal" accesskey="i" >
						<span><?php echo $lang_pim_tabs_Immigration;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(21)" id="photoLink" class="personal" accesskey="f" >
						<span><?php echo $lang_pim_tabs_Photo;?></span></a></li>
			</ul>
		</li>
		<li class="l1 parent">
			<a href="#" class="expanded" onclick="showHideSubMenu(this);"><span class="parent employment">
                <?php echo $lang_pim_Employment;?></span></a>
			<ul class="l2">
				<li class="l2">
					<a href="javascript:displayLayer(2)" id="jobLink" accesskey="j" class="employment"  >

						<span><?php echo $lang_pim_tabs_Job;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(14)" id="paymentsLink" class="employment" accesskey="s" >
						<span><?php echo $lang_pim_tabs_Payments;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(18)" id="taxLink" class="employment" accesskey="t" >
						<span><?php echo $lang_pim_tabs_Tax;?></span></a></li>

				<li class="l2">
					<a href="javascript:displayLayer(19)" id="direct-debitLink" class="employment" accesskey="o" >
						<span><?php echo $lang_pim_tabs_DirectDebit;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(15)" id="report-toLink" class="employment" accesskey="r" >
						<span><?php echo $lang_pim_tabs_ReportTo;?></span></a></li>
			</ul>
		</li>
		<li class="l1 parent">
			<a href="#" class="expanded" onclick="showHideSubMenu(this);">
                <span class="parent pimqualifications"><?php echo $lang_pim_Qualifications;?></span></a>
			<ul class="l2">
				<li class="l2">
					<a href="javascript:displayLayer(17)" id="work_experienceLink" class="pimqualifications" accesskey="w" >

						<span><?php echo $lang_pim_tabs_WorkExperience;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(9)" id="educationLink" class="pimqualifications" accesskey="n" >
						<span><?php echo $lang_pim_tabs_Education;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(16)" id="skillsLink" class="pimqualifications" accesskey="k" >
						<span><?php echo $lang_pim_tabs_Skills;?></span></a></li>

				<li class="l2">
					<a href="javascript:displayLayer(11)" id="languagesLink" class="pimqualifications" accesskey="g" >
						<span><?php echo $lang_pim_tabs_Languages;?></span></a></li>
				<li class="l2">
					<a href="javascript:displayLayer(12)" id="licensesLink" class="pimqualifications" accesskey="l" >
						<span><?php echo $lang_pim_tabs_License;?></span></a></li>
				</ul>
		</li>
		<li class="l1 parent">
			<a href="#" class="expanded" onclick="showHideSubMenu(this);"><span class="parent other"><?php echo $lang_pim_Other;?></span></a>
			<ul class="l2">
				<li class="l2">
					<a href="javascript:displayLayer(13)" id="membershipsLink" class="pimmemberships" accesskey="m">
						<span><?php echo $lang_pim_tabs_Membership;?></span>
					</a>
				</li>
				<li class="l2">
					<a href="javascript:displayLayer(6)" id="attachmentsLink" class="attachments" accesskey="a">
						<span><?php echo $lang_pim_tabs_Attachments;?></span>
					</a>
				</li>
				<li class="l1">
					<a href="javascript:displayLayer(20)" id="customLink" class="l1_link custom" accesskey="u">
						<span><?php echo $lang_pim_tabs_Custom;?></span>
					</a>
				</li>
			</ul>
		</li>
	</ul>
</div>
<?php }
	$requiredNotice = preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark);
?>
    <div id="personal" class="pimpanel formpage2col<?php echo ($escapedPane == '1') ? ' currentpanel' :'';?>">

	    <div onclick="setUpdate(1)" onkeypress="setUpdate(1)" class="outerbox">
	    	<div class="mainHeading"><h2><?php echo $lang_pim_PersonalDetails;?></h2></div>
	    	<?php require(ROOT_PATH . "/templates/hrfunct/hremppers.php"); ?>
	    </div>
	    <br class="clear"/>
	    <div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
	</div>

    <div id="job" class="pimpanel formpage2col<?php echo ($escapedPane == '2') ? ' currentpanel' :'';?>">
	    <div onclick="setUpdate(2)" onkeypress="setUpdate(2)" class="outerbox">
	    	<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Job;?></h2></div>
<?php require(ROOT_PATH . "/templates/hrfunct/hrempjob.php"); ?>
<?php require(ROOT_PATH . "/templates/hrfunct/hrempconext.php"); ?>
<?php require(ROOT_PATH . "/templates/hrfunct/hrempjobhistory.php"); ?>
	    </div>
	    <br class="clear"/>
	    <div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
	</div>

    <div id="dependents" class="pimpanel formpage2col<?php echo ($escapedPane == '3') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_hremp_dependents;?></h2></div>
    		<div style="width:99%; margin:2px 2px 0px 3px;">
<?php require(ROOT_PATH . "/templates/hrfunct/hrempdependent.php"); ?>
<?php require(ROOT_PATH . "/templates/hrfunct/hrempchildren.php"); ?>
				<br class="clear"/>
			</div>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>

    <div id="contacts" class="pimpanel formpage2col<?php echo ($escapedPane == '4') ? ' currentpanel' :'';?>">
    	<div class="outerbox" onclick="setUpdate(4)" onkeypress="setUpdate(4)">
    		<div class="mainHeading"><h2><?php echo  $lang_pim_tabs_Contact; ?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempcontact.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>

    <div id="emgcontacts" class="pimpanel formpage2col<?php echo ($escapedPane == '5') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo  $lang_pim_tabs_EmergencyContacts; ?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempemgcontact.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="attachments" class="pimpanel formpage2col<?php echo ($escapedPane == '6') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Attachments;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempattachment.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="education" class="pimpanel formpage2col<?php echo ($escapedPane == '9') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Education;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempeducation.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="immigration" class="pimpanel formpage2col<?php echo ($escapedPane == '10') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Immigration;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempimmigration.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="languages" class="pimpanel formpage2col<?php echo ($escapedPane == '11') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Languages;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hremplanguage.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="licenses" class="pimpanel formpage2col<?php echo ($escapedPane == '12') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_License;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hremplicenses.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="memberships" class="pimpanel formpage2col<?php echo ($escapedPane == '13') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Membership;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempmembership.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="payments" class="pimpanel formpage2col<?php echo ($escapedPane == '14') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Payments;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hremppayment.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="report-to" class="pimpanel formpage2col<?php echo ($escapedPane == '15') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_ReportTo;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempreportto.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="skills" class="pimpanel formpage2col<?php echo ($escapedPane == '16') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Skills;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempskill.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="work-experiance" class="pimpanel formpage2col<?php echo ($escapedPane == '17') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_WorkExperience;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempwrkexp.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="tax" class="pimpanel formpage2col<?php echo ($escapedPane == '18') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Tax;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hremptax.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="direct-debit" class="pimpanel formpage2col<?php echo ($escapedPane == '19') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_DirectDebit;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempdirectdebit.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="custom" class="pimpanel formpage2col<?php echo ($escapedPane == '20') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Custom;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/hrempcustom.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
    <div id="photo" class="pimpanel formpage2col<?php echo ($escapedPane == '21') ? ' currentpanel' :'';?>">
    	<div class="outerbox">
    		<div class="mainHeading"><h2><?php echo $lang_pim_tabs_Photo;?></h2></div>
          <?php require(ROOT_PATH . "/templates/hrfunct/photohandler.php"); ?>
       	</div>
    	<br class="clear"/>
    	<div class="requirednotice"><?php echo $requiredNotice; ?>.</div>
    </div>
	<div id="photodiv">
		<img width="100" height="120" src="../../templates/hrfunct/photohandler.php?id=<?php echo $escapedId;?>&amp;action=VIEW"
        	onclick="showPhotoHandler()" alt="<?php echo $lang_pim_ClickToEditPhoto;?>" title="<?php echo $lang_pim_ClickToEditPhoto;?>"/>
        <a href="#" onclick="showPhotoHandler()" title="<?php echo $lang_pim_ClickToEditPhoto;?>">
        	<span id="empname"><?php echo CommonFunctions::escapeHtml($currentEmployeeName);?></span>
        </a>
	</div>
<?php } ?>

	</form>
	<script type="text/javaScript"><!--//--><![CDATA[//><!--
    	if (document.getElementById && document.createElement) {
 			roundBorder('outerbox');
		}
	displayLayer(<?php echo $escapedPane; ?>);
	<?php if (isset($this->postArr['txtShowAddPane']) && !empty($this->postArr['txtShowAddPane'])) { ?>
	showAddPane('<?php echo addslashes($this->postArr['txtShowAddPane']); ?>');
	<?php } ?>
	//--><!]]></script>
	</body>
</html>
