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
$addMode = false;
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
		$project->setProjectId($this->popArr['newID']);
		$addMode = true;
	}
}

$locRights=$_SESSION['localRights'];
if ($locRights['edit']) {
	$disableEdit =  "";
	$addAdminBtnAction  = "addAdmin()";
	$delAdminBtnAction  = "delAdmin()";
	$saveAdminBtnAction = "saveAdmin()";
	$clearBtnAction = "clearAll()";
} else {
	$disableEdit = 'disabled = "true"';
	$saveBtnAction = "showAccessDeniedMsg()";
	$addAdminBtnAction = "showAccessDeniedMsg()";
	$saveAdminBtnAction = "showAccessDeniedMsg()";
	$delAdminBtnAction = "showAccessDeniedMsg()";
	$clearBtnAction = "showAccessDeniedMsg()";
}
?>
<html>
<head>
<title><?php echo $lang_Admin_Project; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>

	/**
	 * Add a new project
	 */
 	function addProject() {

		if (validateFields()) {

        	document.frmProject.sqlState.value = "NewRecord";
        	document.frmProject.submit();
		}
    }

   /**
    * Update project details
    */
   function updateProject() {

		if (validateFields()) {

			document.frmProject.sqlState.value  = "UpdateRecord";
			document.frmProject.submit();
		}
	}

   /**
    * Validates the form fields in the project details form
    * returns true if validated, false otherwise.
    */
   function validateFields() {

        if(document.frmProject.txtId.value == '') {
            alert("<?php echo $lang_Admin_Project_Error_PleaseDSpecifyTheProjectId; ?>");
            document.frmProject.txtId.focus();
            return false;
        }

        if(document.frmProject.cmbCustomerId.value == 0) {
            alert("<?php echo $lang_Admin_Project_Error_PleaseSelectACustomer; ?>");
            document.frmProject.customerId.focus();
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
		var popup = window.open('../../templates/hrfunct/emppop.php?reqcode=REP&PROJECT=<?php echo $project->getProjectId();?>','Employees','height=450,width=400');
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

</script>


 	<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

    <style type="text/css">
    <!--
    @import url("../../themes/beyondT/css/octopus.css");

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width:500px;
    }

    .roundbox_content {
        padding:15px 15px 25px 35px;
    }

    input[type=checkbox] {
		border:0px;
		background-color: transparent;
		margin: 0px;
		width: 12px;
		vertical-align: bottom;
    }

    -->
 </style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2><?php echo $lang_Admin_Project; ?></h2>
		  		</td>
	  		<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td></tr>
		</table>
	</p>
  	<div id="navigation">
  		<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onClick="goBack();">
	</div>
    <div class="roundbox">
      <form name="frmProject" method="post" action="<?php echo $formAction;?>">
        <input type="hidden" name="sqlState" value="">
            <label for="txtId"><?php echo $lang_Commn_code; ?></label>
			<input type="text" id="txtId" name="txtId" value="<?php echo $project->getProjectId(); ?>"
				tabindex="1" readonly="true"/>
            <br/>
            <label for="cmbCustomerId"><span class="error">*</span> <?php echo $lang_view_CustomerName; ?></label>
            <select name="cmbCustomerId" <?php echo $disableEdit; ?> >
				<option value="0">-- <?php echo $lang_Admin_Project_SelectCutomer; ?> --</option>
				<?php
					$customers = $this->popArr['cusid'];
					if ($customers) {
						foreach ($customers as $customer) {
							$selected = ($project->getCustomerId() == $customer->getCustomerId()) ? 'selected' : '';

							echo "<option $selected value=\"{$customer->getCustomerId()}\">{$customer->getCustomerName()}</option>";
   						}
					}
   				?>
   			</select>
            <br/>
			<label for="txtName"><span class="error">*</span> <?php echo $lang_Commn_name; ?></label>
            <input type="text" id="txtName" name="txtName" value="<?php echo $project->getProjectName(); ?>"
            	tabindex="2" <?php echo $disableEdit; ?> />
			<br/>
            <label for="txtDescription"><?php echo $lang_Commn_description; ?></label>
            <textarea name="txtDescription" id="txtDescription" rows="3" cols="30"
            	tabindex="3" <?php echo $disableEdit; ?> ><?php echo $project->getProjectDescription() ; ?></textarea>
            <br/>
            <div align="center">
	            <img onClick="<?php echo $saveBtnAction; ?>;" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
				<img src="../../themes/beyondT/pictures/btn_clear.jpg" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.jpg';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.jpg';" onClick="<?php echo $clearBtnAction;?>" >
            </div>
      </form>

      <?php if (!$addMode) { ?>

      <h3><?php echo $lang_Admin_Project_Administrators; ?></h3>
      <form name="frmProjectAdmins" method="post" action="<?php echo $adminFormAction;?>">
        	<input type="hidden" name="delState" value="">
        	<input type="hidden" name="sqlState" value="">
			<input type="hidden" id="projectId" name="projectId" value="<?php echo $project->getProjectId(); ?>"/>

		<?php
			$admins = isset($this->popArr['admins']) ? $this->popArr['admins'] : null;
			if (!empty($admins)) {
		?>

      <div style="float:left">
		<table width="250" class="simpleList" >
			<thead>
				<tr>
				<th class="listViewThS1" align="center">
					<input type='checkbox' class='checkbox' name='allCheck' value=''
						<?php echo $disableEdit; ?> onClick="checkUncheckAll();">
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
       			<td class="<?php echo $cssClass?>" align="center">
       				<input type='checkbox' class='checkbox' name='chkLocID[]'
       					<?php echo $disableEdit; ?> value='<?php echo $admin->getEmpNumber();?>'></td>
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

			</br>
            <div align="left">
	            <img onClick="<?php echo $addAdminBtnAction; ?>;"
	            	onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.jpg';"
	            	onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';"
	            	src="../../themes/beyondT/pictures/btn_add.jpg">
	        <?php
	        	if (!empty($admins)) {
			?>
				<img
					onClick="<?php echo $delAdminBtnAction; ?>"
				    src="../../themes/beyondT/pictures/btn_delete.jpg"
					onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.jpg';"
					onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';">
			<?php
				}
	        ?>
            </div>
			<div id ="addAdminLayer" style="display:none;height:20px;">
		    	<label for="projAdminName"><?php echo $lang_Admin_Users_Employee; ?></label>
	               	<input type="text" readonly name="projAdminName" value="" >
                  	<input type="hidden" readonly name="projAdminID" value="">
                   	<input class="button" style="width:30px;" type="button" name="empPop" value=".."
                   		onClick="popEmployeeList();" tabindex="4" <?php echo $disableEdit; ?> >
	            	<img onClick="<?php echo $saveAdminBtnAction; ?>;"
	            		style="margin-top:10px;"
	            		onMouseOut="this.src='../../themes/beyondT/icons/assign.gif';"
	            		onMouseOver="this.src='../../themes/beyondT/icons/assign_o.gif';"
	            		src="../../themes/beyondT/icons/assign.gif">
			</div>
      </form>
	  <?php } ?>
    </div>

    <script type="text/javascript">
    <!--
    	if (document.getElementById && document.createElement) {
 			initOctopus();
		}
    -->
    </script>

    <div id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>

</body>
</html>
