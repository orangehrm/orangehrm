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

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once($lan->getLangPath("full.php"));

$GLOBALS['lang_Common_Select'] = $lang_Common_Select;

function populateStates($value) {

    $view_controller = new ViewController();
    $provlist = $view_controller->xajaxObjCall($value,'LOC','province');

    $objResponse = new xajaxResponse();
    $xajaxFiller = new xajaxElementFiller();
    $xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
    if ($provlist) {
        $objResponse->addAssign('lrState','innerHTML','<select name="txtState" id="txtState" class="formSelect" tabindex="3"><option value="0">--- '.$GLOBALS['lang_Common_Select'].' ---</option></select>');
        $objResponse = $xajaxFiller->cmbFillerById($objResponse,$provlist,1,'lrState','txtState');

    } else {
        $objResponse->addAssign('lrState','innerHTML','<input type="text" name="txtState" id="txtState" class="formInputText" tabindex="3" value="">');
    }
    $objResponse->addScript('document.getElementById("txtState").Focus();');

    $objResponse->addScript("document.frmLocation.txtDistrict.options.length = 1;");
    $objResponse->addAssign('status','innerHTML','');

    return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->processRequests();

$locRights=$_SESSION['localRights'];

$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}";
$new = true;
$disabled = '';
$locationCode = '';
$locationName = '';
$skillDesc = '';
$locationCountry = '';
$locationState = '';
$locationCity = '';
$locationAddress = '';
$locationZip = '';
$locationPhone = '';
$locationFax = '';
$locationComments = '';

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
    $formAction="{$formAction}&amp;id={$this->getArr['id']}&amp;capturemode=updatemode";
    $new = false;
    $disabled = "disabled='disabled'";
    $editData = $this->popArr['editArr'];
    $locationCode = CommonFunctions::escapeHtml($editData[0][0]);
    $locationName = CommonFunctions::escapeHtml($editData[0][1]);
    $locationCountry = CommonFunctions::escapeHtml($editData[0][2]);
    $locationState = CommonFunctions::escapeHtml($editData[0][3]);
    $locationCity = CommonFunctions::escapeHtml($editData[0][4]);
    $locationAddress = CommonFunctions::escapeHtml($editData[0][5]);
    $locationZip = CommonFunctions::escapeHtml($editData[0][6]);
    $locationPhone = CommonFunctions::escapeHtml($editData[0][7]);
    $locationFax = CommonFunctions::escapeHtml($editData[0][8]);
    $locationComments = CommonFunctions::escapeHtml($editData[0][9]);

}
$token = $this->popArr['token'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<?php $objAjax->printJavascript(); ?>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

    var editMode = <?php echo $new ? 'true' : 'false'; ?>;

    function goBack() {
        location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
    }

    function validate() {
        var err = false;
        var msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

        var frm = document.frmLocation;

        if (frm.txtLocDescription.value.trim() == '') {
            if (!err) {
                frm.txtLocDescription.focus();
            }
            err = true;
            msg += "\t- <?php echo $lang_locations_NameHasToBeSpecified; ?>\n";
        }

        if (frm.cmbCountry.value == '0') {
            if (!err) {
                frm.cmbCountry.focus();
            }
            err = true;
            msg += "\t- <?php echo $lang_locations_CountryShouldBeSelected; ?>\n";
        }

        if ( frm.txtAddress.value.trim() == '') {
            if (!err) {
                frm.txtAddress.focus();
            }
            err = true;
            msg += "\t- <?php echo $lang_locations_AddressShouldBeSpecified; ?>\n";
        }

        if ( frm.txtZIP.value.trim() == '' ){
            if (!err) {
                frm.txtZIP.focus();
            }
            err = true;
            msg += "\t- <?php echo $lang_locations_ZipCodeShouldBeSpecified; ?>\n";
        } else if (!numbers(frm.txtZIP)) {
            if (!confirm('<?php echo $lang_locations_ZipContainsNonNumericChars; ?>')) {
                frm.txtZIP.focus();
                return false;
            }
        }

        if (frm.txtPhone.value.trim() != '' && !numeric(frm.txtPhone)) {
            if (!err) {
                frm.txtPhone.focus();
            }
            err = true;
            msg += "\t- <?php echo $lang_locations_InvalidCharsInPhone; ?>\n";
        }

        if (frm.txtFax.value.trim() != '' && !numeric(frm.txtFax)) {
            if (!err) {
                frm.txtFax.focus();
            }
            err = true;
            msg += "\t- <?php echo $lang_locations_InvalidCharsInFax; ?>\n";
        }

        if (err) {
            alert(msg);
            return false;
        } else {
            $("cmbProvince").value = $("txtState").value;
            return true;
        }
    }

    function resetForm() {
        $('frmLocation').reset();
        $('lrState').innerHTML = initialProvinceContent;
        $('txtState').disabled = false;
    }

    function edit() {

<?php if($locRights['edit']) { ?>
        if (editMode) {
            if (validate()) {
                $('frmLocation').submit();
            }
            return;
        }
        editMode = true;
        var frm = $('frmLocation');

        for (var i=0; i < frm.elements.length; i++) {
            frm.elements[i].disabled = false;
        }
        $('editBtn').value="<?php echo $lang_Common_Save; ?>";
        $('editBtn').title="<?php echo $lang_Common_Save; ?>";
        $('editBtn').className = "savebutton";

<?php } else {?>
        alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
    }

    function onCountryChange(country) {
        document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait; ?>...';
        xajax_populateStates(country);
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
    <div class="formpage">
        <div class="navigation">
        	<input type="button" class="backbutton" onclick="goBack();"
        	  onmouseover="moverButton(this);" onmouseout="moutButton(this);"
              value="<?php echo $lang_Common_Back;?>" tabindex="13" />
        </div>
        <div id="status"></div>
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_locations_heading;?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">[0][2]
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

            <form name="frmLocation" id="frmLocation" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">
               <input type="hidden" value="<?php echo $token;?>" name="token" />
                <input type="hidden" name="sqlState" value="<?php echo $new ? 'NewRecord' : 'UpdateRecord'; ?>"/>

                <?php if (!$new) { ?>
                    <label for="txtLocationCode"><?php echo $lang_Commn_code; ?></label>
                    <input type="hidden" id="txtLocationCode" name="txtLocationCode" value="<?php echo $locationCode;?>" tabindex="1" />
                    <span class="formValue"><?php echo $locationCode;?></span><br class="clear"/>
                <?php } ?>

                <label for="txtLocDescription"><?php echo $lang_compstruct_Name; ?><span class="required">*</span>
                </label>
                <input id="txtLocDescription" name="txtLocDescription" type="text" <?php echo $disabled;?> class="formInputText"
                        value="<?php echo $locationName;?>" tabindex="2" />
               <br class="clear"/>

                <label for="cmbCountry"><?php echo $lang_compstruct_country; ?><span class="required">*</span></label>
                <select id='cmbCountry' name='cmbCountry' <?php echo $disabled;?> class="formSelect countrySelect"
                        onchange="onCountryChange(this.value);" tabindex="3" >
                    <option value="0">--- <?php echo $lang_districtinformation_selectcounlist;?> ---</option>
                    <?php
                        $countryList = $this->popArr['cntlist'];
                        if (!empty($countryList)) {
                            foreach ($countryList as $country) {
                                $selected = ($locationCountry == $country[0]) ? 'selected="selected"' : '';
                                echo "<option {$selected} value='{$country[0]}'>{$country[1]}</option>";
                            }
                        }
                    ?>
                </select>
                <br class="clear"/>

                <label for="txtState"><?php echo $lang_compstruct_state; ?></label>
                <div id="lrState">
                <?php if ($locationCountry == 'US') { ?>
                    <select name="txtState" id="txtState" <?php echo $disabled;?> class="formSelect" tabindex="4" >
                        <option value="0">--- <?php echo $lang_districtinformation_selstatelist;?>---</option>
                    <?php
                        $stateList = $this->popArr['provlist'];
                        if (!empty($stateList)) {
                            foreach ($stateList as $state) {
                                $selected = ($locationState == $state[1]) ? 'selected="selected"' : '';
                                echo "<option $selected value='{$state[1]}'>{$state[2]}</option>";
                            }
                        }
                    ?>
                    </select>
                <?php } else { ?>
                    <input id="txtState" name="txtState" type="text" <?php echo $disabled;?> class="formInputText"
                        value="<?php echo $locationState;?>" tabindex="4" />
                <?php } ?>
                </div>
                <br class="clear"/>

                <input type="hidden" name="cmbProvince" id="cmbProvince" value="<?php echo $locationState;?>"/>

                <label for="cmbDistrict"><?php echo $lang_compstruct_city; ?></label>
                <input id="cmbDistrict"  name="cmbDistrict" type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo $locationCity; ?>" tabindex="5" />
                <br class="clear"/>

                <label for="txtAddress"><?php echo $lang_compstruct_Address; ?><span class="required">*</span></label>
                <textarea id='txtAddress' name='txtAddress' <?php echo $disabled;?> class="formTextArea"
                    rows="3" cols="20" tabindex="6"><?php echo $locationAddress;?></textarea>
                <br class="clear"/>

                <label for="txtZIP"><?php echo $lang_compstruct_ZIP_Code; ?><span class="required">*</span></label>
                <input id='txtZIP' name='txtZIP' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo $locationZip;?>" tabindex="7" />
                <br class="clear"/>

                <label for="txtPhone"><?php echo $lang_compstruct_Phone; ?></label>
                <input id='txtPhone' name='txtPhone' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo $locationPhone;?>" tabindex="8" />
                <br class="clear"/>

                <label for="txtFax"><?php echo $lang_comphire_fax; ?></label>
                <input id="txtFax" name="txtFax" type="text" <?php echo $disabled;?>  class="formInputText"
                    value="<?php echo $locationFax;?>" tabindex="9"/>
                <br class="clear"/>

                <label for="txtComments"><?php echo $lang_Leave_Common_Comments; ?></label>
                <textarea id='txtComments' name='txtComments' <?php echo $disabled;?> class="formTextArea"
                    rows="3" cols="20" tabindex="10" ><?php echo $locationComments;?></textarea>
                <br class="clear"/>

                <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="<?php echo $new ? 'savebutton': 'editbutton';?>" id="editBtn"
                        onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $new ? $lang_Common_Save : $lang_Common_Edit;?>" tabindex="11" />
                    <input type="button" class="clearbutton" onclick="resetForm();"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                         value="<?php echo $lang_Common_Reset;?>" tabindex="12" />
<?php } ?>
                </div>
            </form>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }
        //]]>
        </script>
        <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
    <script type="text/javascript">
    var initialProvinceContent = $('lrState').innerHTML;
    </script> 
</body>
</html>
