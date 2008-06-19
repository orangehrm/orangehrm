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
				'<select name="txtProvince" id="txtProvince" name="txtProvince" tabindex="8"><option value="0">--- '.$GLOBALS['lang_Common_Select'].' ---</option></select>');
		$objResponse = $xajaxFiller->cmbFillerById($objResponse, $provinceList, 1, 'fromJobApplication.state', 'txtProvince');
	} else {
		$objResponse->addAssign('state','innerHTML','<input type="text" id="txtProvince" name="txtProvince" tabindex="8" >');
	}
	$objResponse->addScript('hideLoading();formJobApplication.txtProvince.focus();');
	return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->processRequests();

$vacancy = $records['vacancy'];
$countryList = $records['countryList'];

$formAction = $_SERVER['PHP_SELF'] . '?recruitcode=ApplicantApply';

$picDir = "../../themes/{$styleSheet}/pictures/";
$iconDir = "../../themes/{$styleSheet}/icons/";

$backImg = $picDir . 'btn_back.gif';
$backImgPressed = $picDir . 'btn_back_02.gif';

$saveImg = $picDir . 'btn_save.gif';
$saveImgPressed = $picDir . 'btn_save_02.gif';

$clearImg = $picDir . 'btn_clear.gif';
$clearImgPressed = $picDir . 'btn_clear_02.gif';

?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<?php
	$objAjax->printJavascript();
?>
<script>

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
		var compFields = new Array(0, 2, 3, 6, 7, 8, 11, 12);
		var emailFields = new Array();
		emailFields[0] = 11;
		var phoneFields = new Array();
		phoneFields[0] = 9;
		phoneFields[0] = 10;

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

	function reset() {
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
		status = $('status');
		status.style.display = 'none';
	}

	function showLoading() {
		status = $('status');
		status.style.display = 'block';
	}

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

    <style type="text/css">
    <!--

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 8px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }
    input[type=checkbox] {
		width: 15px;
		background-color: transparent;
		vertical-align: bottom;
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type=hidden] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 110px;
        padding-left: 10px;
    }

    select,input,textarea {
        margin-left: 10x;
    }

    input,textarea {
        padding-left: 4px;
        padding-right: 4px;
    }

    textarea {
        width: 500px;
        height: 90px;
    }

    form {
        min-width: 550px;
        max-width: 770px;
    }

    br {
        clear: left;
    }

    .roundbox {
        margin-top: 10px;
        margin-left: auto;
        margin-right: auto;
        width: 760px;
    }

    body {
    	margin-top: 10px;
        margin-left: auto;
        margin-right: auto;
        width: 780px;
    }

    .roundbox_content {
        padding:5px;
    }

	.hidden {
		display: none;
	}

	.display-block {
		display: block;
	}

	.positionApplyingFor {
        padding-left: 17px;
	}
    -->
</style>
</head>
<body>
	<p><h2 class="moduleTitle"><?php echo $lang_Recruit_ApplicationForm_Heading; ?></h2></p>
	<div id="status" style="float:right;display:none;">
		<image src='<?php echo $iconDir; ?>/loading.gif' width='20' height='20' style="vertical-align: bottom;">
		<?php echo $lang_Commn_PleaseWait;?>
	</div>
  	<div id="navigation" style="margin:0;">
  		<img title="<?php echo $lang_Common_Back;?>" onMouseOut="this.src='<?php echo $backImg; ?>';"
  			 onMouseOver="this.src='<?php echo $backImgPressed;?>';" src="<?php echo $backImg;?>"
  			 onClick="goBack();">
	</div>
    <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
    	if (isset($message)) {
			$col_def = CommonFunctions::getCssClassForMessage($message);
			$message = "lang_Common_" . $message;
	?>
	<div class="message">
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
			<?php echo (isset($$message)) ? $$message: ""; ?>
		</font>
	</div>
	<?php }	?>
  <div class="roundbox">
  <form name="fromJobApplication" id="fromJobApplication" method="post" action="<?php echo $formAction;?>">
  		<div class="positionApplyingFor">
  		<?php echo $lang_Recruit_ApplicationForm_Position . ' : ' . $vacancy->getJobTitleName(); ?><br/>
  		</div>
		<input type="hidden" id="txtVacancyId" name="txtVacancyId" value="<?php echo $vacancy->getId();?>"/>

		<label for="txtFirstName"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_FirstName; ?></label>
        <input type="text" id="txtFirstName" name="txtFirstName" tabindex="1" >

		<label for="txtMiddleName"><span class="error">&nbsp;</span> <?php echo $lang_Recruit_ApplicationForm_MiddleName; ?></label>
        <input type="text" id="txtMiddleName" name="txtMiddleName" tabindex="2" ><br/>

		<label for="txtLastName"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_LastName; ?></label>
        <input type="text" id="txtLastName" name="txtLastName" tabindex="3" ><br/>

		<label for="txtStreet1"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_Street1; ?></label>
        <input type="text" id="txtStreet1" name="txtStreet1" tabindex="4" >

		<label for="txtStreet2"><span class="error">&nbsp;</span> <?php echo $lang_Recruit_ApplicationForm_Street2; ?></label>
        <input type="text" id="txtStreet2" name="txtStreet2" tabindex="5" ><br/>

		<label for="txtCity"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_City; ?></label>
        <input type="text" id="txtCity" name="txtCity" tabindex="6" >

		<label for="txtCountry"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_Country; ?></label>
		<select  id="txtCountry" name="txtCountry" tabindex="7"
			onChange="getProvinceList(this.value);">
	  		<option value="0">-- <?php echo $lang_districtinformation_selectcounlist?> --</option>
			<?php
				  foreach($countryList as $country) {
	    				echo "<option value='" . $country[0] . "'>" . $country[1] . "</option>";
				  }
		    ?>
		 </select><br/>

		<label for="txtProvince"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_StateProvince; ?></label>
        <div id="state"><input type="text" id="txtProvince" name="txtProvince" tabindex="8" ></div>

		<label for="txtZip"><span class="error">&nbsp;</span> <?php echo $lang_Recruit_ApplicationForm_Zip; ?></label>
        <input type="text" id="txtZip" name="txtZip" tabindex="9" ></br>

		<label for="txtPhone"><span class="error">&nbsp;</span> <?php echo $lang_Recruit_ApplicationForm_Phone; ?></label>
        <input type="text" id="txtPhone" name="txtPhone" tabindex="10" >

		<label for="txtMobile"><span class="error">&nbsp;</span> <?php echo $lang_Recruit_ApplicationForm_Mobile; ?></label>
        <input type="text" id="txtMobile" name="txtMobile" tabindex="11" ><br/>

		<label for="txtEmail"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_Email; ?></label>
        <input type="text" id="txtEmail" name="txtEmail" tabindex="12" ><br/>

		<label for="txtQualifications"><span class="error">*</span> <?php echo $lang_Recruit_ApplicationForm_Qualifications; ?></label>
        <textarea id="txtQualifications" name="txtQualifications" tabindex="13" ></textarea><br/><br/>

        <div align="left">
            <img onClick="save();" id="saveBtn"
				onMouseOut="this.src='<?php echo $saveImg;?>';"
            	onMouseOver="this.src='<?php echo $saveImgPressed;?>';"
            	src="<?php echo $saveImg;?>">
			<img onClick="reset(); id="resetBtn"
				onMouseOut="this.src='<?php echo $clearImg;?>';"
            	onMouseOver="this.src='<?php echo $clearImgPressed;?>';"
            	src="<?php echo $clearImg;?>">
        </div>
	</form>
    </div>
    <script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
   	 			$('txtFirstName').focus();
			}
        -->
    </script>

    <div id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
</body>
</html>
