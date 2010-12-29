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
?>
<script type="text/javascript">
//<![CDATA[
    function editSave() {
        document.frmDefineWeekends.submit();
    }
//]]>
</script>
<div class="formpage">
<div class="outerbox">
    <div class="mainHeading"><h2><?php echo $lang_Leave_Leave_Holiday_Weeked_Title;?></h2></div>

<?php
 if (isset($_GET['message']) && !empty($_GET['message'])) {
?>
    <div class="messagebar">
        <span><?php echo CommonFunctions::escapeHtml($_GET['message']); ?></span>
    </div>
<?php } ?>

<form id="frmDefineWeekends" name="frmDefineWeekends" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&amp;action=Holiday_Weekend_Edit">
   <input type="hidden" name="token" value="<?php echo $records['token']; ?>" />
  <?php
  	$daysOfTheWeek = array(
  							Weekends::WEEKENDS_MONDAY => $lang_Common_Monday,
  							Weekends::WEEKENDS_TUESDAY => $lang_Common_Tuesday,
  							Weekends::WEEKENDS_WEDNESDAY => $lang_Common_Wednesday,
  							Weekends::WEEKENDS_THURSDAY => $lang_Common_Thursday,
  							Weekends::WEEKENDS_FRIDAY => $lang_Common_Friday,
  							Weekends::WEEKENDS_SATURDAY => $lang_Common_Saturday,
  							Weekends::WEEKENDS_SUNDAY => $lang_Common_Sunday);

  	$results = null;

  	for ($i=0; $i<7; $i++) {
  		$results[$records[$i]->getDay()] = $records[$i];
  	}

  	foreach ($daysOfTheWeek as $key=>$dayOfTheWeek) {
  		$length = Weekends::WEEKENDS_LENGTH_FULL_DAY;
  		if (isset($results) && isset($results[$key])) {
  			$length = $results[$key]->getLength();
  		}
  ?>
    <input type="hidden" name="txtDay[]" value="<?php echo $key; ?>" />
    <span class="formLabel"><?php echo $dayOfTheWeek;?></span>
    <select name="sltLeaveLength[]" style="width:100px;" class="formSelect" <?php if(!$records['changeWeekends']) echo "disabled='disabled'";  ?>>
            <option value="<?php echo Weekends::WEEKENDS_LENGTH_FULL_DAY; ?>" <?php echo ($length == Weekends::WEEKENDS_LENGTH_FULL_DAY)? 'selected="selected"':""; ?>><?php echo $lang_Leave_Common_FullDay; ?></option>
            <option value="<?php echo Weekends::WEEKENDS_LENGTH_HALF_DAY;?>" <?php echo ($length == Weekends::WEEKENDS_LENGTH_HALF_DAY)?'selected="selected"':""; ?>><?php echo $lang_Leave_Common_HalfDay; ?></option>
            <option value="<?php echo Weekends::WEEKENDS_LENGTH_WEEKEND;?>" <?php echo ($length == Weekends::WEEKENDS_LENGTH_WEEKEND)?'selected="selected"':""; ?>><?php echo $lang_Leave_Common_Weekend; ?></option>
    </select>
    <br class="clear"/>
  <?php
  	}
  ?>
<div class="formbuttons">
<?php if($records['changeWeekends']) { ?>
    <input type="button" class="savebutton" id="saveBtn"
        onclick="editSave();"onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_Save;?>" />
    <input type="button" class="clearbutton" onclick="reset();" tabindex="3"
        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_Reset;?>" />
<?php } ?>
</div>
</form>
<?php 
	if(!$records['changeWeekends']) {
		echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Leave_Weekend_Disabled_Warning);
	}
?>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</div>
