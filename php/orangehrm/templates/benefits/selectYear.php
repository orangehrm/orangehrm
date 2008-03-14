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

$heading = $records[0];
$years = $records[1];
$action = $records[2]
?>
<script type="text/javascript">
function viewYear() {
	window.location = $('frmSelectYear').action+$('cmbYear').value;
}
</script>
<h2>
	<?php echo ${"lang_Benefits_$heading"}; ?>
	<hr/>
</h2>
<form action="?benefitcode=Benefits&amp;action=<?php echo $action; ?>&amp;year=" method="post" id="frmSelectYear" onsubmit="viewYear(); return false;">
<table border="0" cellpadding="2" cellspacing="0">
	<thead>
	  	<tr>
			<th class="tableTopLeft"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
	    	<th class="tableTopMiddle"></th>
			<th class="tableTopRight"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="tableMiddleLeft"></td>
			<td><label for="cmbYear"><?php echo $lang_Benefits_ChooseYear; ?></label></td>
			<td></td>
			<td>
				<select name="cmbYear" id="cmbYear">
				<?php for ($i=0; $i<count($years); $i++) { ?>
				    <option value="<?php echo $years[$i]; ?>"><?php echo $years[$i]; ?></option>
				<?php } ?>
				</select>
			</td>
			<td></td>
			<td>
				<input  type="image" name="btnView"
		    			onclick="view('employee');"
		    			src="../../themes/beyondT/icons/view.gif"
		    			onmouseover="this.src='../../themes/beyondT/icons/view_o.gif';"
		    			onmouseout="this.src='../../themes/beyondT/icons/view.gif';" />
		    </td>
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
			<td class="tableBottomMiddle"></td>
			<td class="tableBottomRight"></td>
		</tr>
  	</tfoot>
</table>
</form>