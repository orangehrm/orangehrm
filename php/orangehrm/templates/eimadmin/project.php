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
 * @author zanfer
 */
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';

require_once($lan->getLangPath("full.php"));

$formAction="{$_SERVER['PHP_SELF']}?uniqcode=PRJ";
$saveBtnAction="addProject()";

$adminFormAction="";
$new = false;
if (isset($this->getArr['capturemode'])) {
	$captureMode = $this->getArr['capturemode'];

	if ($captureMode == 'updatemode') {

		$formAction="{$formAction}&id={$this->getArr['id']}&capturemode=updatemode";
		$saveBtnAction="updateProject()";

		$project = $this->popArr['editArr'];

		// Admin form only shown in update mode
		$adminFormAction="{$_SERVER['PHP_SELF']}?uniqcode=PAD&id={$this->getArr['id']}&capturemode=updatemode";
	} else if ($captureMode == 'addmode') {

		$project = new Projects();
		$new = true;
	}
}

$locRights=$_SESSION['localRights'];
if ($locRights['edit']) {
	$disabled =  "";
	$addAdminBtnAction  = "addAdmin()";
	$delAdminBtnAction  = "delAdmin()";
	$saveAdminBtnAction = "saveAdmin()";
	$clearBtnAction = "clearAll()";
} else {
	$disabled = 'disabled = "true"';
	$saveBtnAction = "showAccessDeniedMsg()";
	$addAdminBtnAction = "showAccessDeniedMsg()";
	$saveAdminBtnAction = "showAccessDeniedMsg()";
	$delAdminBtnAction = "showAccessDeniedMsg()";
	$clearBtnAction = "showAccessDeniedMsg()";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_view_Project_Heading;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script type="text/javascript">
//<![CDATA[
	/**
	 * Add a new project
	 */
 	function addProject() {

		if (validateFields()) {

        	document.frmProject.sqlState.value = "NewRecord";
        	document.frmProject.submit();
        	return true;
		}

		return false;
    }

   /**
    * Update project details
    */
   function updateProject() {

		if (validateFields()) {

			document.frmProject.sqlState.value  = "UpdateRecord";
			document.frmProject.submit();
			return true;
		}

		return false;
	}

   /**
    * Validates the form fields in the project details form
    * returns true if validated, false otherwise.
    */
   function validateFields() {

        if(document.frmProject.cmbCustomerId.value == -1) {
            alert("<?php echo $lang_Admin_Project_Error_PleaseSelectACustomer; ?>");
            document.frmProject.cmbCustomerId.focus();
            return false;
        }

        if (document.frmProject.txtName.value == '') {
            alert ("<?php echo $lang_Admin_Project_Error_PleaseSpecifyTheName; ?>");
            document.frmProject.txtName.focus();
            return false;
        }

		return true;
   }

	/**
	 * Clear all form fields in the project details form
	 */
	function clearAll() {
		document.frmProject.cmbCustomerId.value='0';
		document.frmProject.txtName.value='';
		document.frmProject.txtDescription.value=''
	}

	/**
	 * Go back to the project list page.
	 */
	function goBack() {
        location.href = "./CentralController.php?uniqcode=PRJ&VIEW=MAIN";
    }

	/**
	 * Show acccess denied message.
	 */
    function showAccessDeniedMsg() {
    	alert("<?php echo $lang_Error_AccessDenied; ?>")
    }

	/**
	 * Run when the "add" button is clicked.
	 * Shows the employee select fields
	 */
	function addAdmin() {
		oLayer = document.getElementById("addAdminLayer").style.display = 'block';
	}

	/**
	 * Check or uncheck all project admin check boxes.
	 */
	function checkUncheckAll() {
		var checked;

		with (document.frmProjectAdmins) {

			checked = elements['allCheck'].checked;

			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = checked;
				}
			}
		}
	}

	/**
	 * Popup the employee list.
	 */
	function popEmployeeList() {
		var popup = window.open('../../templates/hrfunct/emppop.php?reqcode=REP&PROJECT=<?php echo $project->getProjectId();?>','Employees','height=450,width=400,scrollbars=1');
    	if(!popup.opener) {
    		popup.opener=self;
		}
		popup.focus();
	}

	/**
	 * Delete selected admins.
	 */
	function delAdmin() {

		var check = false;
		with (document.frmProjectAdmins) {

			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = true;
					break;
				}
			}

			if (check) {
				delState.value = 'DeleteMode';
				submit();
			} else {
				alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
			}
		}
	}

	/**
	 * Save an admin.
	 */
	function saveAdmin() {

		with (document.frmProjectAdmins) {

	        if (projAdminID.value == '') {
	            alert("<?php echo $lang_Error_PleaseSelectAnEmployee; ?>");
	            empPop.focus();
	        } else if (isAdmin(projAdminID.value)) {
	        	alert("<?php echo $lang_Admin_Project_EmployeeAlreadyAnAdmin; ?>");
	        } else {
	        	sqlState.value = "NewRecord";
	        	submit();
			}
		}
	}

	/**
	 * Checks whether the passed employee id is already
	 * a project admin.
	 */
	function isAdmin(empId) {
		var admin = false;
		var empIdInt = trimLeadingZeros(empId);

		with (document.frmProjectAdmins) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].value == empIdInt)){
					admin = true;
					break;
				}
			}
		}

		return admin;
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
            <div class="mainHeading"><h2><?php echo $lang_view_Project_Heading;?></h2></div>

        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>
        <?php } ?>

      <form name="frmProject" method="post" action="<?php echo $formAction;?>" onSubmit="return <?php echo $saveBtnAction; ?>;">
            <input type="hidden" name="sqlState" value=""/>
            <input type="hidden" id="txtId" name="txtId" value="<?php echo $project->getProjectId(); ?>" />
            <br class="clear"/>

            <label for="cmbCustomerId"><?php echo $lang_view_CustomerName; ?><span class="required">*</span></label>
            <select name="cmbCustomerId" id="cmbCustomerId" <?php echo $disabled; ?> class="formSelect"
                    tabindex="1">
				<option value="-1">-- <?php echo $lang_Admin_Project_SelectCutomer; ?> --</option>
				<?php
					$customers = $this->popArr['cusid'];
					if ($customers) {
						foreach ($customers as $customer) {
							$selected = ($project->getCustomerId() == $customer->getCustomerId()) ? 'selected="selected"' : '';

							echo "<option $selected value=\"{$customer->getCustomerId()}\">{$customer->getCustomerName()}</option>";
   						}
					}
   				?>
   			</select>
            <br class="clear"/>

			<label for="txtName"><?php echo $lang_Commn_name; ?><span class="required">*</span></label>
            <input type="text" id="txtName" name="txtName" value="<?php echo $project->getProjectName(); ?>"
            	tabindex="2" class="formInputText" <?php echo $disabled; ?> />
            <br class="clear"/>

            <label for="txtDescription"><?php echo $lang_Commn_description; ?></label>
            <textarea name="txtDescription" id="txtDescription" rows="3" cols="30" class="formTextArea"
            	tabindex="3" <?php echo $disabled; ?> ><?php echo $project->getProjectDescription() ; ?></textarea>
            <br class="clear"/>

             <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                <input type="button" class="savebutton" id="saveBtn"
                    onclick="<?php echo $saveBtnAction; ?>;" tabindex="4" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Save;?>" />
                <input type="button" class="clearbutton" onclick="<?php echo $clearBtnAction;?>" tabindex="5"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                     value="<?php echo $lang_Common_Reset;?>" />
<?php } ?>
            </div>
      </form>

      <?php if (!$new) { ?>
      <div class="subHeading">
        <h3><?php echo $lang_Admin_Project_Administrators; ?></h3>
      </div>
      <form name="frmProjectAdmins" method="post" action="<?php echo $adminFormAction;?>">
        	<input type="hidden" name="delState" value=""/>
        	<input type="hidden" name="sqlState" value=""/>
			<input type="hidden" id="projectId" name="projectId" value="<?php echo $project->getProjectId(); ?>"/>

		<?php
			$admins = isset($this->popArr['admins']) ? $this->popArr['admins'] : null;
			if (!empty($admins)) {
		?>

      <div style="float:left">
		<table width="250" class="simpleList" >
			<thead>
				<tr>
				<th class="listViewThS1">
					<input type='checkbox' class='checkbox' name='allCheck' value=''
						<?php echo $disabled; ?> onClick="checkUncheckAll();">
				</th>
				<th class="listViewThS1"><?php echo $lang_Admin_Project_EmployeeName; ?></th>
				</tr>
    		</thead>
			<?php
				$odd = false;
				foreach ($admins as $admin) {
	 	 	 		$cssClass = ($odd) ? 'even' : 'odd';
	 	 	 		$odd = !$odd;
	 		?>
    		<tr>
       			<td class="<?php echo $cssClass?>">
       				<input type='checkbox' class='checkbox' name='chkLocID[]'
       					<?php echo $disabled; ?> value='<?php echo $admin->getEmpNumber();?>'></td>
		 		<td class="<?php echo $cssClass?>"><?php echo $admin->getName(); ?></td>
			</tr>
		 	<?php
		 		}
		  	?>
 		</table>
		</div>
		 	<?php
			 }
		  	?>

            <br class="clear"/>

             <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                <input type="button" class="addbutton" id="addBtn"
                    onclick="<?php echo $addAdminBtnAction; ?>;" tabindex="6" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Add;?>" />
            <?php
                if (!empty($admins)) {
            ?>
                <input type="button" class="delbutton" onclick="<?php echo $delAdminBtnAction;?>" tabindex="7"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                     value="<?php echo $lang_Common_Delete;?>" />
            <?php
                }
            ?>
<?php } ?>
            </div>
            <br class="clear"/>

			<div id ="addAdminLayer" style="display:none;">
		    	<label for="projAdminName"><?php echo $lang_Admin_Users_Employee; ?></label>
	               	<input type="text" readonly name="projAdminName" value="" >
                  	<input type="hidden" readonly name="projAdminID" value="">
                   	<input class="button" style="width:30px;" type="button" name="empPop" value=".."
                   		onClick="popEmployeeList();" tabindex="4" <?php echo $disabled; ?> >
                    <input type="button" class="addbutton" id="addBtn"
                        onclick="<?php echo $saveAdminBtnAction; ?>;" tabindex="7"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Assign;?>" />
			</div>
            <br class="clear"/>
      </form>
	  <?php } ?>
    </div>

        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');
            }
        //]]>
        </script>

    <div id="" class="requirednotice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
</body>
</html>
