<?php
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/confs/sysConf.php';
/*
 *	Including the language pack
 *
 **/
 
 $lan = new Language();
 
 require_once($lan->getLangPath("leave/leaveCommon.php")); 
 require_once($lan->getLangPath("leave/leaveApply.php")); 

 if (isset($_GET['message'])) {
?>
<var><?php echo $_GET['message']; ?></var>
<?php } ?>
<script>			
	//function goBack() {
	//	location.href = "./CentralController.php?uniqcode=&VIEW=MAIN";
	//}

	function addSave() {
		err = false;
		msg = 'Please correct the following\n\n';
		
		obj = document.frmLeaveApp.txtLeaveDate;
		if ((obj.value == '') || !validDate(obj.value)) {
			err = true;
			msg += ' - Please select a valid From Date\n'
		}		
		
		obj = document.frmLeaveApp.sltLeaveType;
		if (obj.value == -1) {
			err = true;
			msg += ' - Please select a Leave Type\n'
		}			
		
		if (err) {
			alert(msg);
		} else {
			document.frmLeaveApp.submit();
		}	
	}
	
	function validDate(txt) {
		dateExpression = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/
		
		if (!dateExpression.test(txt)) {
			return false;
		}
			
		return true;
	}
	
	//function clearAll() {
	//	document.frmLeaveApp.txtSkillDesc.value = '';
	//}
</script>
<h2><?php echo $lang_Title?><hr/></h2>
<form id="frmLeaveApp" name="frmLeaveApp" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_Apply">
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
  	<tr>
		<td class="tableMiddleLeft"></td>
		<td><?php echo $lang_Date;?></td>
		<td width="25px">&nbsp;</td>
		<td><?php echo $lang_LeaveType; ?></td>
		<td width="25px">&nbsp;</td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td>
			<input name="txtLeaveDate" type="text" id="txtLeaveDate" />
          	<input type="button" name="Submit" value="..." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmLeaveApp.txtLeaveDate);return false;"/>
		</td>
		<td width="25px">&nbsp;</td>
		<td>
			<select name="sltLeaveType" id="sltLeaveType">      		
	  <?php
	  	if (is_array($records[1])) {
	  	 	foreach ($records[1] as $record) {
	  ?>
        		<option value="<?php echo $record->getLeaveTypeID();?>"><?php echo $record->getLeaveTypeName(); ?></option> 
      <?php  }
			} else {?>
      			<option value="-1">-- No Leave Types --</option> 
      <?php } ?>
         	</select>
		 </td>
		<td width="25px">&nbsp;</td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td>&nbsp;<!--<a href="CentralController.php?leavecode=Leave&action=Leave_Apply_Multiple_View">Apply for multiple days</a>--></td>
		<td width="25px">&nbsp;</td>
		<td><?php echo $lang_Length; ?></td>
		<td width="25px">&nbsp;</td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td>&nbsp;</td>
		<td width="25px">&nbsp;</td>
		<td>
			<select name="sltLeaveLength" id="sltLeaveLength">
        	<option value="<?php echo ($records[0]->lengthFullDay);?>"><?php echo $lang_FullDay;?></option>
			<option value="<?php echo ($records[0]->lengthHalfDayMorning);?>"><?php echo $lang_HalfDayMorning;?></option>
			<option value="<?php echo ($records[0]->lengthHalfDayAfternoon);?>"><?php echo $lang_HalfDayAfternoon;?></option>
       </select>
		</td>
		<td width="25px">&nbsp;</td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td>&nbsp;</td>
		<td width="25px">&nbsp;</td>
		<td><?php echo $lang_Comment; ?></td>
		<td width="25px">&nbsp;</td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td>&nbsp;</td>
		<td width="25px">&nbsp;</td>
		<td><input name="txtComments" type="text" id="txtComments" /></td>
		<td width="25px">&nbsp;</td>
		<td class="tableMiddleRight"></td>
	</tr>
	<tr>
		<td class="tableMiddleLeft"></td>
		<td><img border="0" title="Add" onclick="addSave();" onmouseout="this.src='../../themes/beyondT/icons/apply.gif';" onmouseover="this.src='../../themes/beyondT/icons/apply_o.gif';" src="../../themes/beyondT/icons/apply.gif" /></td>
		<td width="25px">&nbsp;</td>
		<td>&nbsp;</td>
		<td width="25px">&nbsp;</td>
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
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"></iframe>
