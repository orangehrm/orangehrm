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

	$hspDefined = Config::getHspCurrentPlan();
	$checked["$hspDefined"] = 'checked';

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

<script type="text/javascript">
	<!--
	function validate() {

		valid = false;

		for (i = 1; i <= 6; i++) {
			valid |= (document.getElementById('HspType' + i).checked);
		}

		if (!valid) {
		    alert('<?php echo $lang_HSP_Plan_Not_Selected ?>');
			return false;
		} else {
			return true;
		}

	}
	-->
</script>

<h2><?php echo $lang_Define_Health_Savings_Plans; ?></h2>
<form action="?benefitcode=Benefits&action=Save_Health_Savings_Plans" method="post" id="frmHelthSavingPlan" name="frmHelthSavingPlan" onSubmit="return validate()">
<table width="141" border="0" cellpadding="4" cellspacing="0">
    <tr>
      <td width="94">&nbsp;<?php echo $lang_Hsp_Key_Hsa; ?></td>
      <td width="47">
        <input name="HspType" id="HspType1" type="radio" value="1" <?php echo $checked[1]; ?> />
      </td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hra; ?></td>
      <td>
        <input name="HspType" id="HspType2" type="radio" value="2" <?php echo $checked[2]; ?> />
      </td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Fsa; ?></td>
      <td>
        <input name="HspType" id="HspType3" type="radio" value="3" <?php echo $checked[3]; ?> />
      </td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hsa_Fsa; ?></td>
      <td>
        <input name="HspType" id="HspType4" type="radio" value="4" <?php echo $checked[4]; ?> />
      </td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hra_Fsa; ?></td>
      <td>
        <input name="HspType" id="HspType5" type="radio" value="5" <?php echo $checked[5]; ?> />
      </td>
    </tr>
    <tr>
      <td>&nbsp;<?php echo $lang_Hsp_Key_Hsa_Hra; ?></td>
      <td>
        <input name="HspType" id="HspType6" type="radio" value="6" <?php echo $checked[6]; ?> />
      </td>
    </tr>
  </table>

  <br>
  <input type="submit" name="save" value="<?php echo $lang_Common_Save;?>">
</form>


