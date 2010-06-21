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

require_once($lan->getLangPath("full.php"));

$locRights=$_SESSION['localRights'];

$GLOBALS['lang_Common_Select'] = $lang_Common_Select;

function populateStates($value, $oldState) {

    $view_controller = new ViewController();
    $provlist = $view_controller->xajaxObjCall($value,'LOC','province');

    $objResponse = new xajaxResponse();
    $xajaxFiller = new xajaxElementFiller();
    $xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
    if ($provlist) {
        $objResponse->addAssign('lrState','innerHTML','<select name="txtState" id="txtState" class="formSelect"><option value="0">--- '.$GLOBALS['lang_Common_Select'].' ---</option></select>');
        $objResponse = $xajaxFiller->cmbFillerById($objResponse,$provlist,1,'frmGenInfo.lrState','txtState');

    } else {
        $objResponse->addAssign('lrState','innerHTML','<input type="text" name="txtState" id="txtState" class="formInputText" value="'. $oldState .'">');
    }

    $objResponse->addAssign('status','innerHTML', '');

    return $objResponse->getXML();
}

$objAjax = new xajax();
$objAjax->registerFunction('populateStates');
$objAjax->processRequests();

$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}";

$disabled = '';
$skillId = '';
$skillName = '';
$skillDesc = '';
$editArr = $this->popArr['editArr'];
$token = $this->popArr['token'];
$disabled = "disabled='disabled'";

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

    var editMode = false;

    function onCountryChange(newValue) {

        document.getElementById('status').innerHTML = '<?php echo $lang_Commn_PleaseWait; ?>...';

        // keep the old value only if state is a text input
        var oldVal = "";
        var state =  document.getElementById('txtState');
        if (state.type == 'text') {
            oldVal = state.value;
        }

        xajax_populateStates(newValue, oldVal);
    }

    function showCommentLengthExceedWarning() {
        totalFieldLength = 800;
        marginOffset = 35;
        usedLength = 0;

        with (document.forms['frmGenInfo']) {
            usedLength += txtCompanyName.value.length;
            usedLength += txtTaxID.value.length;
            usedLength += txtNAICS.value.length;
            usedLength += txtPhone.value.length;
            usedLength += txtFax.value.length;
            usedLength += cmbCountry.options[cmbCountry.selectedIndex].value.length;
            usedLength += txtStreet1.value.length;
            usedLength += txtStreet2.value.length;
            usedLength += cmbCity.value.length;
            usedLength += (txtState.type == 'text') ? txtState.value.length : txtState.options[txtState.selectedIndex].value.length;
            usedLength += txtZIP.value.length;

            availableLength = totalFieldLength - (usedLength + marginOffset);
            commentLengthWarning = document.getElementById('commentLengthWarningLabel');

            if (txtComments.value.length > availableLength) {
                commentLengthWarning.style.display = 'block';
            } else {
                commentLengthWarning.style.display = 'none';
            }
        }
    }

    function validate() {
        var err = false;
        var msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';
        var errors = new Array();

        if ($('txtCompanyName').value == '') {
            err = true;
            msg += "\t- <?php echo $lang_geninfo_err_CompanyName; ?>\n";
        }

        var cntrl = $('txtPhone');
        if(cntrl.value != '' && !checkPhone(cntrl)) {
            err = true;
            msg += "\t- <?php echo $lang_geninfo_err_Phone; ?>\n";
        }

        var cntrl = $('txtFax');
        if(cntrl.value != '' && !checkPhone(cntrl)) {
            err = true;
            msg += "\t- <?php echo $lang_geninfo_err_Fax; ?>\n";
        }

        if (err) {
            alert(msg);
            return false;
        } else {
            $("cmbState").value = $("txtState").value;
            return true;
        }
    }

    function resetForm() {
        $('frmGenInfo').reset();
        $('lrState').innerHTML = initialProvinceContent;
        $('txtState').disabled = false;
    }

    function edit() {

<?php if($locRights['edit']) { ?>
        if (editMode) {
            if (validate()) {
                $('frmGenInfo').submit();
            }
            return;
        }
        editMode = true;
        var frm = $('frmGenInfo');

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
<style type="text/css">
.style1 {color: #FF0000}
</style>
</head>

<body>

    <div class="formpage2col">
        <div id="status"></div>

        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_geninfo_heading;?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

            <form name="frmGenInfo" id="frmGenInfo" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">
               <input type="hidden" value="<?php echo $token;?>" name="token" />
                <input type="hidden" name="STAT" value="EDIT"/>

                <label for="txtCompanyName"><?php echo $lang_geninfo_compname; ?><span class="required">*</span></label>
                <input id="txtCompanyName" name="txtCompanyName" type="text" <?php echo $disabled;?>
                    class="formInputText"
                    value="<?php echo isset($editArr['COMPANY']) ? $editArr['COMPANY'] : ''?>" maxlength="250"/>

                <span class="formLabel"><?php echo $lang_geninfo_numEmployees;?></span>
                <span class="formValue"><?php echo $this->popArr['empcount'];?></span>
                <br class="clear"/>

                <label for="txtTaxID"><?php echo $lang_geninfo_taxID; ?></label>
                <input id='txtTaxID' name='txtTaxID' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo isset($editArr['TAX']) ? $editArr['TAX'] : ''?>" maxlength="25"/>

                <label for="txtNAICS"><?php echo $lang_geninfo_naics; ?></label>
                <input id='txtNAICS' name='txtNAICS' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo isset($editArr['NAICS']) ? $editArr['NAICS'] : ''?>" maxlength="15"/>
                <br class="clear"/>

                <label for="txtPhone"><?php echo $lang_compstruct_Phone; ?></label>
                <input id='txtPhone' name='txtPhone' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo isset($editArr['PHONE']) ? $editArr['PHONE'] : ''?>" maxlength="20"/>

                <label for="txtFax"><?php echo $lang_comphire_fax; ?></label>
                <input id="txtFax" name="txtFax" type="text" <?php echo $disabled;?>  class="formInputText"
                    value="<?php echo isset($editArr['FAX']) ? $editArr['FAX'] : ''?>" maxlength="20"/>
                <br class="clear"/>

                <label for="cmbCountry"><?php echo $lang_compstruct_country; ?></label>
                <select id='cmbCountry' name='cmbCountry' <?php echo $disabled;?> class="formSelect countrySelect"
                        onchange="onCountryChange(this.value);">
                    <option value="0">--- <?php echo $lang_Common_Select;?> ---</option>
                    <?php
                        $countryList = $this->popArr['cntlist'];
                        if (!empty($countryList)) {
                            foreach ($countryList as $country) {
                                $selected = (isset($editArr['COUNTRY']) && ($editArr['COUNTRY'] == $country[0])) ? 'selected="selected"' : '';
                                echo "<option {$selected} value='{$country[0]}'>{$country[1]}</option>";
                            }
                        }
                    ?>
                </select>
                <br class="clear"/>

                <label for="txtStreet1"><?php echo $lang_compstruct_Address; ?>1</label>
                <input id='txtStreet1' name='txtStreet1' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo isset($editArr['STREET1']) ? $editArr['STREET1'] : ''?>" maxlength="40"/>

                <label for="txtStreet2"><?php echo $lang_compstruct_Address; ?>2</label>
                <input id='txtStreet2' name='txtStreet2' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo isset($editArr['STREET2']) ? $editArr['STREET2'] : ''?>" maxlength="40"/>
                <br class="clear"/>

                <label for="cmbCity"><?php echo $lang_compstruct_city; ?></label>
                <input id="cmbCity"  name="cmbCity" type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo isset($editArr['CITY']) ? $editArr['CITY'] : ''?>" maxlength="30"/>

                <label for="cmbState"><?php echo $lang_compstruct_state; ?></label>
                <div id="lrState">
                <?php if (isset($editArr['COUNTRY']) && ($editArr['COUNTRY'] == 'US')) { ?>
                    <select name="txtState" id="txtState" <?php echo $disabled;?> class="formSelect">
                        <option value="0">--- <?php echo $lang_Common_Select;?>---</option>
                    <?php
                        $stateList = $this->popArr['provlist'];
                        if (!empty($stateList)) {
                            foreach ($stateList as $state) {
                                $selected = (isset($editArr['STATE']) && ($editArr['STATE'] == $state[1])) ? 'selected="selected"' : '';
                                echo "<option $selected value='{$state[1]}'>{$state[2]}</option>";
                            }
                        }
                    ?>
                    </select>
                <?php } else { ?>
                    <input id="txtState" name="txtState" type="text" <?php echo $disabled;?> class="formInputText"
                        value="<?php echo isset($editArr['STATE']) ? $editArr['STATE'] : ''?>" maxlength="30"/>
                <?php } ?>
                </div>
                <input type="hidden" name="cmbState" id="cmbState"
                    value="<?php echo isset($editArr['STATE']) ? $editArr['STATE'] : ''?>"/>
                <br class="clear"/>

                <label for="txtZIP"><?php echo $lang_compstruct_ZIP_Code; ?></label>
                <input id='txtZIP' name='txtZIP' type="text" <?php echo $disabled;?> class="formInputText"
                    value="<?php echo isset($editArr['ZIP']) ? $editArr['ZIP'] : ''?>" maxlength="20"/>
                <br class="clear"/>

                <label for="txtComments"><?php echo $lang_Leave_Common_Comments; ?></label>
                <span id="commentLengthWarningLabel" style="display: none" class="style1"><?php echo $lang_geninfo_err_CommentLengthWarning; ?></span>
                <textarea id='txtComments' name='txtComments' <?php echo $disabled;?> class="formTextArea"
                    rows="3" cols="20"
                    onkeyup="showCommentLengthExceedWarning()"
                    ><?php echo isset($editArr['COMMENTS']) ? $editArr['COMMENTS'] : ''?></textarea>
                <br class="clear"/>

                <div class="formbuttons" align="center">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="editbutton" id="editBtn"
                        onclick="edit();" tabindex="2" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Edit;?>" />
                    <input type="button" class="clearbutton" onclick="resetForm();" tabindex="3"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                         value="<?php echo $lang_Common_Reset;?>" />
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

