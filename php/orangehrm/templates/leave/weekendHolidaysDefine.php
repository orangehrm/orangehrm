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

 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<h2><?php echo $lang_Leave_Leave_Holiday_Weeked_Title; ?><hr/></h2>

<script>
	function editSave() {
		document.frmDefineWeekends.submit();
	}
</script>
<form id="frmDefineWeekends" name="frmDefineWeekends" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Holiday_Weekend_Edit">
<table border="0" cellpadding="0" cellspacing="0">
  <thead>
  	<tr>
		<th class="tableTopLeft"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
    	<th class="tableTopMiddle"></th>
		<th class="tableTopRight"></th>
	</tr>
  </thead>
  <tbody>
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

  	for ($i=0; $i<count($records); $i++) {
  		$results[$records[$i]->getDay()] = $records[$i];
  	}

  	foreach ($daysOfTheWeek as $key=>$dayOfTheWeek) {
  		$length = Weekends::WEEKENDS_LENGTH_FULL_DAY;
  		if (isset($results) && isset($results[$key])) {
  			$length = $results[$key]->getLength();
  		}
  ?>
  <tr>
  	<td class="tableMiddleLeft"></td>
  	<td width="90px"><?php echo $dayOfTheWeek;?><input type="hidden" name="txtDay[]" value="<?php echo $key; ?>" /></td>
  	<td >&nbsp;</td>
    <td width="150px"><select name="sltLeaveLength[]" style="width:100px;">
            <option value="<?php echo Weekends::WEEKENDS_LENGTH_FULL_DAY; ?>" <?php echo ($length == Weekends::WEEKENDS_LENGTH_FULL_DAY)?"selected":""; ?>><?php echo $lang_Leave_Common_FullDay; ?></option>
            <option value="<?php echo Weekends::WEEKENDS_LENGTH_HALF_DAY;?>" <?php echo ($length == Weekends::WEEKENDS_LENGTH_HALF_DAY)?"selected":""; ?>><?php echo $lang_Leave_Common_HalfDay; ?></option>
            <option value="<?php echo Weekends::WEEKENDS_LENGTH_WEEKEND;?>" <?php echo ($length == Weekends::WEEKENDS_LENGTH_WEEKEND)?"selected":""; ?>><?php echo $lang_Leave_Common_Weekend; ?></option>
         </select>
    </td>
    <td >&nbsp;</td>
	<td class="tableMiddleRight"></td>
  </tr>
  <?php
  	}
  ?>
  <tr>
  	<td class="tableMiddleLeft"></td>
  	<td >&nbsp;</td>
  	<td >&nbsp;</td>
    <td >&nbsp;</td>
    <td ><img border="0" title="Add" onclick="editSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.gif';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif" /></td>
	<td class="tableMiddleRight"></td>
  </tr>
  </tbody>
  <tfoot>
  	<tr>
		<td class="tableBottomLeft"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomMiddle"></td>
		<td class="tableBottomRight"></td>
	</tr>
  </tfoot>
</table>
</form>