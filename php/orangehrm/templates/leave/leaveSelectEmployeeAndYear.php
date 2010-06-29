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

 if (isset($modifier)) {
 	switch ($modifier) {
 		case "summary" : $action = "?leavecode=Leave&amp;action=Leave_Summary";
 						 break;
 		default 		: $action = "";
 						  break;
 	}
 }

 $years = $records[0];
 $employees = $records[1];
 if (isset($records[2])) {
 	$leaveTypes = $records[2];
 }

 if (isset($records[3])) {
 	$role = $records[3];
 }

 $token = "";
 if($records['token']) {
   $token = $records['token'];
 }
?>
<script type="text/javascript">
//<![CDATA[
    function validate() {
        err = false;
        errors = "<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n";

        if (document.frmSelectEmployee.year.value == -1) {
            errors += "-  <?php echo $lang_Error_PleaseSelectAYear; ?>\n";
            err = true;
        }
        if (document.frmSelectEmployee.id.value == -1) {
            errors += "-  <?php echo $lang_Error_PleaseSelectAnEmployee; ?>\n";
            err = true;
        }

        if (err) {
            errors = errors+"\n";
            alert(errors);
            return false;
        } else {
            with (document.frmSelectEmployee) {
                 var searchMode = searchBy.value;
        <?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
                if (cmbId.selectedIndex > 0) {
                    searchMode = 'employee';
                }

                if (leaveTypeId.selectedIndex > 0) {
                    searchMode = 'leaveType';
                }
                if (cmbId.selectedIndex > 0 && leaveTypeId.selectedIndex > 0) {
                    searchMode = 'both';
                }
    <?php } else { ?>
                searchMode = 'employee';
    <?php } ?>
                searchBy.value = searchMode;

            }
            return true;
        }
    }

    function view() {
        if (validate()) {
            document.frmSelectEmployee.submit();
        }
    }

    function changeEmployeeSelection() {

        objCmbId = document.frmSelectEmployee.cmbId;
        objRow = document.getElementById("idSelectRow");
        objId = document.frmSelectEmployee.id;

        switch (objCmbId.value) {
            case '0' : objRow.className = 'hide';
                     objId.value = 0;
                     break;
            case '1' : objRow.className = 'show';
                     objId.value = -1;
                     break;
        }
    }

    function returnEmpDetail(){
        var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=REP&LEAVE=SUMMARY','Employees','height=450,width=400,scrollbars=1');
        if(!popup.opener) popup.opener=self;
        popup.focus();
    }
//]]>
</script>

<div class="formpage3col">
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_Leave_Select_Employee_Title;?></h2></div>

    <?php $message =  isset($_GET['msg']) ? $_GET['msg'] : (isset($_GET['message']) ? $_GET['message'] : null);
        if (isset($message)) {
            $messageType = CommonFunctions::getCssClassForMessage($message);
            $message = "lang_Common_" . $message;
    ?>
        <div class="messagebar">
            <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
        </div>
    <?php } ?>


<form method="post" name="frmSelectEmployee" action="<?php echo $action; ?>" onsubmit="return validate();">
<input type="hidden" name="searchBy" value="<?php echo ($_GET['action'] == 'Leave_Select_Employee_Leave_Summary') ? 'employee' : 'leaveType' ?>" />
<input type="hidden" name="token" value="<?php echo $token;?>" />
<table border="0" cellpadding="2" cellspacing="0" style="margin:0 0 0 0">
  <tbody>
	<tr>
		<td class="odd"></td>
    	<td width="70px" class="odd"><?php echo $lang_Leave_Common_Year;?></td>

    	<td width="130px" class="odd">
		    	  <select name="year">
		    	   	   <?php
		   		if (is_array($years)) {
		   			foreach ($years as $year) {
                                             $selectYear = "";
                                           if(date("Y") == $year){
                                                $selectYear = "selected";


                                        }
		  ?>
		 		  	<option value="<?php echo $year ?>" <?php echo $selectYear ?>><?php echo $year ?></option>
		  <?php 	}
		   		}else{
		 ?>
                                       <option value="-1">--<?php echo $lang_Error_NoYearSpecified;?> --</option>
                                       <?php
                                }?>
  	    		  </select>
   	    </td>

    	<td width="180px" class="odd"><?php echo $lang_Leave_Common_EmployeeName;?></td>

    	<td width="180px" class="odd">
    	<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
    		<select name="cmbId" onchange="changeEmployeeSelection();">
				<option value="0"><?php echo $lang_Leave_Common_AllEmployees;?></option>
				<option value="1"><?php echo $lang_Leave_Common_Select;?></option>
			</select>
		<?php } else if ($role == authorize::AUTHORIZE_ROLE_SUPERVISOR) { ?>
			<select name="id">
				<option value="-1">-<?php echo $lang_Leave_Common_Select;?>-</option>
				<?php
		   			if (is_array($employees)) {
		   				sort($employees);
		   				foreach ($employees as $employee) {
		  ?>
		 		<option value="<?php echo $employee[0] ?>"><?php echo $employee[1] ?></option>
		  <?php 		}
		   			}
         ?>
                </select>
         <?php
    		}
		 ?>

		</td>
    	<td width="100px" class="odd">
            <input type="button" class="viewbutton" id="btnView"
                onclick="view();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_View;?>" />
        </td>
		<td class="odd"></td>
	</tr>
	<?php if ($role == authorize::AUTHORIZE_ROLE_ADMIN) { ?>
	<tr class="hide" id="idSelectRow">
		<td class="odd"></td>
    	<td class="odd">&nbsp;</td>
    	<td class="odd">&nbsp;</td>
    	<td class="odd">&nbsp;</td>
		<td class="odd">
			<input type="text" name="cmbEmpID" id="cmbEmpID" value="" disabled="disabled" />
			<input type="hidden" name="id" id="id" value="0" />
			<input type="button" value="..." onclick="returnEmpDetail();" />
		</td>
		<td class="odd">&nbsp;</td>
		<td class="odd"></td>
	</tr>
	<tr>
		<td class="odd"></td>
    	<td width="70px" class="odd"></td>

    	<td width="130px" class="odd">
   	    </td>

    	<td width="180px" class="odd"><?php echo $lang_Leave_Common_LeaveType;?></td>

    	<td width="150px" class="odd">
				<select name="leaveTypeId">
					<option value="0"><?php echo $lang_Leave_Common_All;?></option>
					<?php
		   				if (isset($leaveTypes) && is_array($leaveTypes)) {
		   					foreach ($leaveTypes as $leaveType) {
		  ?>
		 		  	<option value="<?php echo $leaveType->getLeaveTypeId(); ?>"><?php echo $leaveType->getLeaveTypeName(); ?></option>
		  <?php 			}
		   				}
		 ?>
  	    		</select>
		</td>
    	<td width="100px" class="odd"></td>
		<td class="odd"></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="odd"></td>
    	<td width="70px" class="odd"></td>

    	<td width="130px" class="odd">
   	    </td>

    	<td width="180px" class="odd"></td>

    	<td width="150px" class="odd">
		</td>
    	<td width="100px" class="odd"></td>
		<td class="odd"></td>
	</tr>
  </tbody>
</table>
</form>
</div>
    <script type="text/javascript">
    //<![CDATA[
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    //]]>
    </script>
</div>
