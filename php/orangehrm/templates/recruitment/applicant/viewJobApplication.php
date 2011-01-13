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

/**
 * Xajax call to get list of provinces for the selected country.
 */

// Unfortunately, the only way to make this variable available to populateStates()
$GLOBALS['lang_Common_Select'] = $lang_Common_Select;

/**
 * Populates the states list based on selected country
 *
 * @param String $country Country code of currently selected country.
 */
function populateStates($country) {

	$objResponse = new xajaxResponse();
	$provinceList = RecruitmentController::getProvinceList($country);

	if ($provinceList) {
		$xajaxFiller = new xajaxElementFiller();
		$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
		$objResponse->addAssign('state','innerHTML',
				'<select name="txtProvince" id="txtProvince" name="txtProvince" tabindex="8" class="formSelect"><option value="0">--- '.$GLOBALS['lang_Common_Select'].' ---</option></select>');
		$objResponse = $xajaxFiller->cmbFillerById($objResponse, $provinceList, 1, 'fromJobApplication.state', 'txtProvince');
	} else {
		$objResponse->addAssign('state','innerHTML','<input type="text" id="txtProvince" name="txtProvince" tabindex="8" class="formInputText">');
	}
	$objResponse->addScript('_changeToSavedProvince();hideLoading();formJobApplication.txtProvince.focus();');
	return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->processRequests();

$vacancy = $records['vacancy'];
$countryList = $records['countryList'];
$company = $records['company'];

if ($records['retrySubmission'] && isset($records['savedData'])) {
	foreach ($records['savedData'] as $varName => $value) {
		$$varName = $value;
	}
}

$formAction = $_SERVER['PHP_SELF'] . '?recruitcode=ApplicantApply';

$iconDir = "../../themes/{$styleSheet}/icons/";
$token = "";
if(isset($records['token'])) {
   $token = $records['token'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="../../scripts/archive.js"></script>
<?php
	$objAjax->printJavascript();
?>
<script>
//<![CDATA[

    function goBack() {
        location.href = "<?php echo "{$_SERVER['PHP_SELF']}?recruitcode=ApplicantViewJobs"; ?>";
    }

	function validate() {
		err = false;
		var msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';
		var errors = new Array();

		var fields = new Array("txtFirstName", "txtMiddleName", "txtLastName", "txtStreet1",
						"txtStreet2", "txtCity", "txtCountry", "txtProvince", "txtZip", "txtPhone",
						"txtMobile", "txtEmail", "txtQualifications");

		var fieldNames = new Array('<?php echo $lang_Recruit_ApplicationForm_FirstName;?>',
						'<?php echo $lang_Recruit_ApplicationForm_MiddleName;?>',
						'<?php echo $lang_Recruit_ApplicationForm_LastName;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Street1;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Street2;?>',
						'<?php echo $lang_Recruit_ApplicationForm_City;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Country;?>',
						'<?php echo $lang_Recruit_ApplicationForm_StateProvince;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Zip;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Phone;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Mobile;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Email;?>',
						'<?php echo $lang_Recruit_ApplicationForm_Qualifications;?>');

		// compulsary fields
		var compFields = new Array(0, 2, 3, 5, 6, 7, 8, 11, 12);
		var emailFields = new Array();
		emailFields[0] = 11;
		var phoneFields = new Array();
		phoneFields[0] = 9;
		phoneFields[1] = 10;

		// validate compulsary fields
		var numCompFields = compFields.length;
		for (var i = 0; i < numCompFields; i++ ) {
			var fieldNdx = compFields[i];
			var fieldName = fields[fieldNdx];
		    var value = $(fieldName).value.trim();
		    if (value == '') {
				err = true;
				msg += "\t- <?php echo $lang_Recruit_ApplicationForm_PleaseSpecify ?>" + fieldNames[fieldNdx] + "\n";
		    }
		}

		if ($('txtCountry').value == '0') {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_ApplicationForm_PleaseSelect . $lang_Recruit_ApplicationForm_Country?>\n";
		}

		if ($('txtProvince').value == '0') {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_ApplicationForm_PleaseSelect . $lang_Recruit_ApplicationForm_StateProvince ?>\n";
		}

		//
		// Only check validation if all compulsary fields have been specified
		//
		if (err == false) {

			// validate email fields
			var numEmailFields = emailFields.length;
			for (var i = 0; i < numEmailFields; i++ ) {
				var fieldNdx = emailFields[i];
				var fieldName = fields[fieldNdx];
			    var value = $(fieldName).value.trim();
			    if (!checkEmail(value)) {
					err = true;
					msg += "\t- <?php echo $lang_Recruit_ApplicationForm_PleaseSpecifyValidEmail ?>" + fieldNames[fieldNdx] + "\n";
			    }
			}

			// validate phone fields
			var numPhoneFields = phoneFields.length;
			for (var i = 0; i < numPhoneFields; i++ ) {
				var fieldNdx = phoneFields[i];
				var fieldName = fields[fieldNdx];
			    var field = $(fieldName);
			    if (!checkPhone(field)) {
					err = true;
					msg += "\t- <?php echo $lang_Recruit_ApplicationForm_PleaseSpecifyValidPhone ?>" + fieldNames[fieldNdx] + "\n";
			    }
			}
		}

		if (err) {
			alert(msg);
			return false;
		} else {
            //validation for qualification field
            if(($('txtQualifications').value.trim()).length > 5000) {
                alert("Qualification can't exceed more than 5000 characters");
                return false;
            }
			if (!numbers($('txtZip'))) {
	            if (!confirm('<?php echo $lang_Recruit_ZipContainsNonNumericChars; ?>')) {
	                $('txtZip').focus();
	                return false;
	            }
			}

			return true;
		}
	}

    function save() {

		if (validate()) {
        	$('fromJobApplication').submit();
		} else {
			return false;
		}
    }

	function resetForm() {
		$('fromJobApplication').reset();
	}

	/*
	 * Get list of provinces for the selected country
	 */
	function getProvinceList(country) {
		showLoading();
		xajax_populateStates(country);
	}

	function hideLoading() {
		var status = $('status');
		status.style.display = 'none';
	}

	function showLoading() {
		var status = $('status');
		status.style.display = 'block';
	}
//]]>
</script>

    <script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css"/>
    <!--[if lte IE 6]>
    <link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
    <![endif]-->
    <!--[if IE]>
    <link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
    <![endif]-->
</head>
<body>
    <div class="formpage3col">
        <div class="navigation">
        	<input type="button" class="backbutton" value="<?php echo $lang_Common_Back;?>"
        		onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);" />
        </div>
        <div id="status" style="float:right;display:none;">
            <image src='<?php echo $iconDir; ?>/loading.gif' width='20' height='20' style="vertical-align: bottom;">
            <?php echo $lang_Commn_PleaseWait;?>
        </div>
        <div class="outerbox">
            <div class="mainHeading">
                <h2><?php echo $lang_Recruit_ApplicationForm_Heading; echo empty($company) ? "({$lang_Recruit_Application_CompanyNameNotSet})" : $company; ?></h2></div>

        <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

  <form name="fromJobApplication" id="fromJobApplication" method="post" action="<?php echo $formAction;?>" enctype="multipart/form-data">
<input type="hidden" name="token" value="<?php echo $token; ?>" />
        <input type="hidden" id="txtVacancyId" name="txtVacancyId" value="<?php echo $vacancy->getId();?>"/>

  		<span class="formLabel"><?php echo $lang_Recruit_ApplicationForm_Position;?></span>
  		<span class="formValue"><?php echo $vacancy->getJobTitleName(); ?></span>
        <br class="clear"/>

		<label for="txtFirstName"><?php echo $lang_Recruit_ApplicationForm_FirstName; ?><span class="required">*</span></label>
        <input type="text" id="txtFirstName" name="txtFirstName" tabindex="1" class="formInputText"
               value="<?php echo (isset($txtFirstName)) ? $txtFirstName : ''; ?>" maxlength="30" />

		<label for="txtMiddleName"><?php echo $lang_Recruit_ApplicationForm_MiddleName; ?></label>
        <input type="text" id="txtMiddleName" name="txtMiddleName" tabindex="2" class="formInputText"
        	value="<?php echo (isset($txtMiddleName)) ? $txtMiddleName : ''; ?>" maxlength="30" />
        <br class="clear"/>

		<label for="txtLastName"><?php echo $lang_Recruit_ApplicationForm_LastName; ?><span class="required">*</span></label>
        <input type="text" id="txtLastName" name="txtLastName" tabindex="3" class="formInputText"
               value="<?php echo (isset($txtLastName)) ? $txtLastName : ''; ?>" maxlength="30"/>
        <br class="clear"/>

		<label for="txtStreet1"><?php echo $lang_Recruit_ApplicationForm_Street1; ?><span class="required">*</span></label>
        <input type="text" id="txtStreet1" name="txtStreet1" tabindex="4" class="formInputText"
               value="<?php echo (isset($txtStreet1)) ? $txtStreet1 : ''; ?>" maxlength="50" />

		<label for="txtStreet2"><?php echo $lang_Recruit_ApplicationForm_Street2; ?></label>
        <input type="text" id="txtStreet2" name="txtStreet2" tabindex="5" class="formInputText"
               value="<?php echo (isset($txtStreet2)) ? $txtStreet2 : ''; ?>" maxlength="50" />
        <br class="clear"/>

		<label for="txtCity"><?php echo $lang_Recruit_ApplicationForm_City; ?><span class="required">*</span></label>
        <input type="text" id="txtCity" name="txtCity" tabindex="6" class="formInputText"
               value="<?php echo (isset($txtCity)) ? $txtCity : ''; ?>" maxlength="50" />

		<label for="txtCountry"><?php echo $lang_Recruit_ApplicationForm_Country; ?><span class="required">*</span></label>
		<select  id="txtCountry" name="txtCountry" tabindex="7" class="formSelect"
			onChange="getProvinceList(this.value);">
	  		<option value="0">-- <?php echo $lang_districtinformation_selectcounlist?> --</option>
			<?php
				  foreach($countryList as $country) {
				  		$selected = (isset($txtCountry) && $country[0] == $txtCountry) ? 'selected="selected"' : '';
	    				echo '<option value="' . $country[0] . '" ' . $selected . '>' . $country[1] . '</option>';
				  }
		    ?>
		 </select>
         <br class="clear"/>

		<label for="txtProvince"><?php echo $lang_Recruit_ApplicationForm_StateProvince; ?><span class="required">*</span></label>
        <div id="state"><input type="text" id="txtProvince" name="txtProvince" tabindex="8" class="formInputText"
                               value="<?php echo (isset($txtProvince)) ? $txtProvince : ''; ?>" maxlength="50" /></div>

		<label for="txtZip"><?php echo $lang_Recruit_ApplicationForm_Zip; ?><span class="required">*</span></label>
        <input type="text" id="txtZip" name="txtZip" tabindex="9" class="formInputText"
               value="<?php echo (isset($txtZip)) ? $txtZip : ''; ?>" maxlength="20" />
        <br class="clear"/>

		<label for="txtPhone"><?php echo $lang_Recruit_ApplicationForm_Phone; ?></label>
        <input type="text" id="txtPhone" name="txtPhone" tabindex="10" class="formInputText"
               value="<?php echo (isset($txtPhone)) ? $txtPhone : ''; ?>" maxlength="20"/>

		<label for="txtMobile"><?php echo $lang_Recruit_ApplicationForm_Mobile; ?></label>
        <input type="text" id="txtMobile" name="txtMobile" tabindex="11" class="formInputText"
               value="<?php echo (isset($txtMobile)) ? $txtMobile : ''; ?>" maxlength="20" />
        <br class="clear"/>

		<label for="txtEmail"><?php echo $lang_Recruit_ApplicationForm_Email; ?><span class="required">*</span></label>
        <input type="text" id="txtEmail" name="txtEmail" tabindex="12" class="formInputText"
               value="<?php echo (isset($txtEmail)) ? $txtEmail : ''; ?>" maxlength="50" />
        <br class="clear"/>

		<label for="txtQualifications"><?php echo $lang_Recruit_ApplicationForm_Qualifications; ?><span class="required">*</span></label>
        <textarea id="txtQualifications" name="txtQualifications" tabindex="13" rows="8" cols="80" class="formTextArea"
            style="width:450px;"><?php echo (isset($txtQualifications)) ? $txtQualifications : ''; ?></textarea>
        <br class="clear"/>

		<label for="txtResume"><?php echo $lang_Recruit_ApplicationForm_Resume; ?></label>
        <input type="file" id="txtResume" name="txtResume" tabindex="14" class="formFileInput"/><br class="clear"/>
        <div class="formHint" style="padding-left:10px;"><?php echo $lang_Recruit_ApplicationForm_ResumeDescription; ?></div>
        <div class="formbuttons">
            <input type="button" class="savebutton" id="saveBtn" tabindex="15"
                onclick="save();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" />
            <input type="button" class="clearbutton" id="resetBtn" tabindex="16"
                onclick="resetForm();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                 value="<?php echo $lang_Common_Reset;?>" />
        </div>
        <br class="clear"/>

	</form>
    </div>

    <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    <script type="text/javascript">
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }

		getProvinceList($('txtCountry').value);

		function _changeToSavedProvince() {
        <?php if ($records['retrySubmission'] && isset($txtProvince)) { ?>
	        provinceInput = $('txtProvince');
	        if (provinceInput.type == 'select-one') {
	        	for (i = 0; i < provinceInput.options.length; i++) {
	        		if (provinceInput.options[i].value == '<?php echo $txtProvince; ?>') {
	        			provinceInput.options.selectedIndex = i;
	        			break;
	        		}
	        	}
	        } else {
			provinceInput.value = "<?php echo $txtProvince; ?>";
	        }
        <?php } ?>
		}
    //]]>
    </script>
    </div>
</body>
</html>
