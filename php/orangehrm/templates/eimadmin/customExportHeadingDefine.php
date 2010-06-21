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

$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}&id={$this->getArr['id']}&capturemode=updatemode";
$btnAction="addUpdate()";

$headings = $this->popArr['headings'];
$assignedFields = $this->popArr['assigned'];
$name = $this->popArr['exportName'];
$id = $this->popArr['id'];
$numFields = count($assignedFields);
$tabIndex = 1;
$token = $this->popArr['token'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_DataExport_DefineCustomFieldHeadings_Heading; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[
    function goBack() {
        location.href = "./CentralController.php?uniqcode=CEX&id=<?php echo $this->getArr['id'];?>&capturemode=updatemode";
    }

	function validate() {
		err = false;
		msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

		errors = new Array();

		var headerValues = document.frmCustomExport.elements["headerValues[]"];

		for (var i = 0; i < headerValues.length; i++) {
			if (trim(headerValues[i].value) == '') {
				err = true;
				msg = '<?php echo $lang_DataExport_Error_AllHeadingsMustBeSpecified; ?>';
				headerValues[i].focus();
				break;
			}
			if (headerValues[i].value.indexOf(',') != -1) {
				err = true;
				msg = '<?php echo $lang_DataExport_Error_CommaNotAllowedInHeadings; ?>';
				headerValues[i].focus();
				break;
			}
		}

		if (!err) {

			// check for duplicates


		}

		if (err) {
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

  function addUpdate() {

		if (validate()) {
			document.frmCustomExport.sqlState.value  = "UpdateRecord";
			document.frmCustomExport.submit();
		} else {
			return false;
		}
	}

	/**
	 * Reset form, undoing any changes done
	 */
	function resetFields() {

		var headerValues = document.frmCustomExport.elements["headerValues[]"];

		for (var i = 0; i < headerValues.length; i++) {
			headerValues[i].value = '';
		}

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
	    	<input type="button" class="savebutton"
		        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
		        value="<?php echo $lang_Common_Back;?>" />
        </div>
        <div class="outerbox">
            <div class="mainHeading">
                <h2><?php echo $lang_DataExport_DefineCustomFieldHeadings_Heading . ' : ' . $name; ?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

  <form name="frmCustomExport" id="frmCustomExport" method="post" action="<?php echo $formAction;?>">
     <input type="hidden" name="token" value="<?php echo $token;?>" />
    <input type="hidden" name="sqlState" value="">
	<input type="hidden" id="txtId" name="txtId" value="<?php echo $id;?>"/>
	<input type="hidden" id="txtFieldName" name="txtFieldName" value="<?php echo $name; ?>" />
	<table class="simpleList" >
		<tr>
		   	<th width="125" style="align:left;"><?php echo $lang_DataExport_AssignedFields; ?></th>
			<th width="40"/>
		   	<th width="125" style="align:left;"><?php echo $lang_DataExport_ColumnHeadings; ?></th>
		</tr>
		<?php
			$odd = false;
			for ($i = 0; $i < $numFields; $i++) {
				$cssClass = ($odd) ? 'even' : 'odd';
				$odd = !$odd;
		?>
		<tr><td class="<?php echo $cssClass;?>"><input type="hidden" name="cmbAssignedFields[]" value="<?php echo $assignedFields[$i];?>"/><?php echo $assignedFields[$i];?></td>
			<td class="<?php echo $cssClass;?>"></td>
			<td class="<?php echo $cssClass;?>">
                <input type="text" name="headerValues[]" tabindex="<?php echo $tabIndex++;?>"
                    value="<?php echo $headings[$i];?>"/></td>
		</tr>
		<?php } ?>
	</table>
     <div class="formbuttons">
        <input type="button" class="savebutton" id="saveBtn" onclick="<?php echo $btnAction; ?>;"
            tabindex="<?php echo $tabIndex++;?>" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
            value="<?php echo $lang_Common_Save;?>" />
        <input type="button" class="clearbutton" onclick="resetFields();" tabindex="<?php echo $tabIndex++;?>"
            onmouseover="moverButton(this);" onmouseout="moutButton(this);"
             value="<?php echo $lang_Common_Reset;?>" />
    </div>

	</form>
    </div>
    <div class="notice"><?php echo $lang_DataExport_EditColumnHeadings; ?></div>
    <script type="text/javascript">
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    //]]>
    </script>
</div>
</body>
</html>
