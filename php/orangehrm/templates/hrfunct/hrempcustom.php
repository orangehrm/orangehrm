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
$customFieldList = $this->popArr['customFieldList'];
$customValues = $this->popArr['editCustomInfoArr'];
?>
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>

	<table onclick="setUpdate(20)" onkeypress="setUpdate(20)" style="margin:0 5px 0 5px;padding-top:5px;"
		border="0" cellpadding="0" cellspacing="2">
<?php
	$disabled = (isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled="disabled"';
	foreach ($customFieldList as $customField) {
		$fieldName = "custom" . $customField->getFieldNumber();
		$value = (isset($customValues[$fieldName])) ? $customValues[$fieldName] : "";
		?>
    <tr>
		<td><?php echo CommonFunctions::escapeHtml($customField->getName());?></td>
		<td width="5">&nbsp;</td>
		<td>
<?php
		if ($customField->getFieldType() == CustomFields::FIELD_TYPE_SELECT) {
			$options = $customField->getOptions();
?>
		<select <?php echo $disabled;?> name="<?php echo CommonFunctions::escapeHtml($fieldName); ?>" >
<?php
			foreach($options as $option) {
				$option = CommonFunctions::escapeHtml(trim($option));
				$selected = ($option == $value) ? "selected" : "";
?>
				<option <?php echo $selected; ?> value="<?php echo CommonFunctions::escapeHtml($option);?>"><?php echo CommonFunctions::escapeHtml($option);?></option>
<?php
			}
?>
<?php
		} else {
?>
		<input type="text" size="20" <?php echo $disabled;?> name="<?php echo CommonFunctions::escapeHtml($fieldName); ?>" id="<?php echo CommonFunctions::escapeHtml($fieldName); ?>"
  				value="<?php echo CommonFunctions::escapeHtml($value);?>"/>
<?php
		}
?>


		</td>
	</tr>
<?php
	}

	if (count($customFieldList) == 0) {

?>
<tr><td>
<?php echo $lang_pim_CustomFields_NoCustomFieldsDefined;?></td></tr>
<?php
	}
?>

</table>
<?php if (count($customFieldList) > 0) { ?>
<div class="formbuttons">
    <input type="button" class="<?php echo $editMode ? 'editbutton' : 'savebutton';?>" name="EditMain" id="btnEditCustom"
    	value="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
    	title="<?php echo $editMode ? $lang_Common_Edit : $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="editEmpMain(); return false;"/>
	<input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
<?php }
	}
?>
