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
	$plans = array(null, $lang_Hsp_Key_Hsa, $lang_Hsp_Key_Hra, $lang_Hsp_Key_Fsa, $lang_Hsp_Key_Hsa_Fsa, $lang_Hsp_Key_Hra_Fsa, $lang_Hsp_Key_Hsa_Hra);
	$checked = array(null, '', '', '', '', '', '');

	$checkTable = "hs_hr_config";
	$selectFields[0] = "`value`";
	$checkHspSelected[0]="`key` = 'hsp_current_plan'";

	$sqlBuilder = new SQLQBuilder();

	$query = $sqlBuilder->simpleSelect($checkTable, $selectFields, $checkHspSelected);
	$dbConnection = new DMLFunctions();

	$result = $dbConnection->executeQuery($query);

	if($result) {
		$row = mysql_fetch_array($result);
		$hspDefined = $row['value'];
		$checked["$hspDefined"] = 'checked';
	}

	mysql_free_result($result);

	if((!isset($_REQUEST['message']))){
			if ($hspDefined != '0')
				echo "<font color='#006600'><b>{$plans[$hspDefined]}</b> $lang_Defined_Hsp.</font>";
			else
				echo "<font color='#FF0000'>$lang_Hsp_No_HSP_defined.</font>";
	} else {
		if($_REQUEST['message']=="SAVE_SUCCESS"){
			echo "<font color ='#006600'>$lang_Hsp_Succesfully_Saved. $lang_Hsp_Current_HSP_is <b>{$plans[$hspDefined]}</b></font>";
		}

		if($_REQUEST['message']=="SAVE_FAILIURE"){
			echo "<font color ='red'>$lang_Hsp_Saving_Error</font>";
 			$checked[1] = 'checked';
		}
	}
?>

</script>

<h2><?php echo $lang_Define_Health_Savings_Plans; ?></h2>
<form action="?benefitcode=Benefits&action=Save_Health_Savings_Plans" method="post" id="frmHelthSavingPlan" name="frmHelthSavingPlan">
<table width="141" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="94">&nbsp;<?php echo $lang_Hsp_Key_Hsa; ?></td>
      <td width="47"><label for="inputhsa">
	<?php ?>
        <input name="HspType" type="radio" value="1" <?php echo $checked[1]; ?>>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hra; ?></td>
      <td><label for="inputhra">
        <input name="HspType" type="radio" value="2" <?php echo $checked[2]; ?>>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Fsa; ?></td>
      <td><label for="inputfsa">
        <input name="HspType" type="radio" value="3" <?php echo $checked[3]; ?>>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hsa_Fsa; ?></td>
      <td><label for="inputhsa+fsa">
        <input name="HspType" type="radio" value="4" <?php echo $checked[4]; ?>>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hra_Fsa; ?></td>
      <td><label for="inputhra+fsa">
        <input name="HspType" type="radio" value="5" <?php echo $checked[5]; ?>>
      </label></td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hsa_Hra; ?></td>
      <td><label for="inputhsa+hra">
        <input name="HspType" type="radio" value="6" <?php echo $checked[6]; ?>>
      </label></td>
    </tr>
  </table>

  <br>
  <label for="inputsave">
  <input type="submit" name="save" value="<?php echo $lang_Common_Save;?>">
  </label>
</form>


