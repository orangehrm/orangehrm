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
		obj = document.frmLeaveApp.txtLeaveDate;
		if ((obj.value == '') || !validDate(obj.value)) {
			alert ("Invalid Date or Date in the Past!");
			return false;
		}
		obj = document.frmLeaveApp.sltLeaveType;
		if (obj.value == -1) {
			alert ("Please select a leave type!");
			return false;
		}
			
		//document.frmSkills.sqlState.value = "NewRecord";
		document.frmLeaveApp.submit();		
	}
	
	function validDate(txt) {
		dateExpression = /^[0-9]{4}-[0-9]{2}-[0-9]{2}$/
		
		if (!dateExpression.test(txt)) {
			return false;
		}
			
		todayTxt = '<?php echo date('Y-m-d');	?>';
			
		txtDate = new Date();
		today = new Date();	
		
		txtArr = txt.split(/-/);
		todayArr = todayTxt.split(/-/);
		
		txtDate.setYear(txtArr[0]);	
		today.setYear(todayArr[0]);
		
		if (txtDate.getYear() < today.getYear()) {
			return false;
		}
		
		txtDate.setMonth(txtArr[1]);
		today.setMonth(todayArr[1]);
				
		if (txtDate.getMonth() < today.getMonth()) {
			return false;
		}
		
		txtDate.setDate(txtArr[2]);
		today.setDate(todayArr[2]);
				
		if (txtDate.getDate() < today.getDate()) {
			return false;
		}				
		
		return true;
	}
	
	//function clearAll() {
	//	document.frmLeaveApp.txtSkillDesc.value = '';
	//}
</script>

<h3><?php echo $lang_Title?><hr/></h3>
<form id="frmLeaveApp" name="frmLeaveApp" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?leavecode=Leave&action=Leave_Apply">
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="13" height="12"><img src="../../themes/beyondT/pictures/table_r1_c1.gif" alt="" name="table_r1_c1" width="13" height="12" border="0" id="table_r1_c1" /></td>
      <td colspan="4" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r1_c2" width="1" height="1" border="0" id="table_r1_c2" /></td>
      <td width="14"><img src="../../themes/beyondT/pictures/table_r1_c3.gif" alt="" name="table_r1_c3" width="13" height="12" border="0" id="table_r1_c3" /></td>
    </tr>
    <tr>
      <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r2_c1" width="1" height="1" border="0" id="table_r2_c1" /></td>
      <td width="188"><?php echo $lang_Date;?></td>
      <td width="33" align="right">&nbsp;</td>
      <td colspan="2" align="left"><?php echo $lang_LeaveType; ?></td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif."><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r2_c3" width="1" height="1" border="0" id="table_r2_c3" /></td>
    </tr>
    <tr>
      <td height="28" valign="top" background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td rowspan="6" valign="top"><input name="txtLeaveDate" type="text" id="txtLeaveDate" />
          <input type="submit" name="Submit" value="...." onclick="if(self.gfPop)gfPop.fPopCalendar(document.frmLeaveApp.txtLeaveDate);return false;"/></td>
      <td align="right" valign="top">&nbsp;</td>
      <td colspan="2" align="left" valign="top">
      	<select name="sltLeaveType" id="sltLeaveType">      		
	  <?php
	  	if (is_array($records[1]))
	  	 	foreach ($records[1] as $record) {
	  ?>
        	<option value="<?php echo $record->getLeaveTypeID();?>"><?php echo $record->getLeaveTypeName(); ?></option> 
      <?php } else {?>
      		<option value="-1">-- No Leave Types --</option> 
      <?php } ?>
         </select>
      </td>
      <td rowspan="4" background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
    <tr>
      <td height="27" valign="top" background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td align="right" valign="top">&nbsp;</td>
      <td colspan="2" align="left" valign="top"><?php echo $lang_Length; ?></td>
    </tr>
    <tr>
      <td height="55" valign="top" background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td align="right" valign="top">&nbsp;</td>
      <td colspan="2" align="left" valign="top">
      	<select name="sltLeaveLength" id="sltLeaveLength">
        	<option value="<?php echo ($records[0]->lengthFullDay);?>"><?php echo $lang_FullDay;?></option>
			<option value="<?php echo ($records[0]->lengthHalfDay);?>"><?php echo $lang_HalfDay;?></option>
       </select>
    </td>
    </tr>
    <tr>
      <td height="19" valign="top" background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td align="right" valign="top">&nbsp;</td>
      <td colspan="2" align="left" valign="top"><?php echo $lang_Comment; ?></td>
    </tr>
    <tr>
      <td height="22" valign="top" background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td align="right" valign="top">&nbsp;</td>
      <td colspan="2" align="left" valign="top"><input name="txtComments" type="text" id="txtComments" /></td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
    <tr>
      <td height="29" valign="top" background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td align="right" valign="top">&nbsp;</td>
      <td colspan="2" align="left" valign="top">&nbsp;</td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
    <tr>
      <td background="../../themes/beyondT/pictures/table_r2_c1.gif">&nbsp;</td>
      <td>  <img border="0" title="Add" onclick="addSave();" onmouseout="this.src='../../themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';" src="../../themes/beyondT/pictures/btn_add.jpg" /></td>
      <td align="right">&nbsp;</td>
      <td width="132" align="right">&nbsp;</td>
      <td width="220">&nbsp;</td>
      <td background="../../themes/beyondT/pictures/table_r2_c3.gif.">&nbsp;</td>
    </tr>
    <tr>
      <td><img src="../../themes/beyondT/pictures/table_r3_c1.gif" alt="" name="table_r3_c1" width="13" height="16" border="0" id="table_r3_c1" /></td>
      <td colspan="4" background="../../themes/beyondT/pictures/table_r3_c2.gif"><img src="../../themes/beyondT/pictures/spacer.gif" alt="" name="table_r3_c2" width="1" height="1" border="0" id="table_r3_c2" /></td>
      <td><img src="../../themes/beyondT/pictures/table_r3_c3.gif" alt="" name="table_r3_c3" width="13" height="16" border="0" id="table_r3_c3" /></td>
    </tr>
  </table>
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../../scripts/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"></iframe>
