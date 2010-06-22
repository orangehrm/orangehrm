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
	$clearBtnAction = "this.form.reset()";
} else {
	$disabled = 'disabled = "true"';
	$saveBtnAction = "showAccessDeniedMsg()";
	$addAdminBtnAction = "showAccessDeniedMsg()";
	$saveAdminBtnAction = "showAccessDeniedMsg()";
	$delAdminBtnAction = "showAccessDeniedMsg()";
	$clearBtnAction = "showAccessDeniedMsg()";
}

$token = $this->popArr['token'];
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
		_matchAutoCompletionFields();

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
	 * Matches the Id of the employee from the selected employee name of the autocomplete field
	 */
	function _matchAutoCompletionFields() {
		employeeName = $('projAdminName').value;

		for (i = 0; i < employees.length; i++) {
			if (employees[i] == employeeName) {
				$('projAdminID').value = ids[i];
				return true;
			}
		}
		return false;
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

	employees = new Array();
	ids = new Array();
<?php
$employees = $this->popArr['employeeList'];
for ($i=0;$i<count($employees);$i++) {
	echo "employees[" . $i . "] = '" . CommonFunctions::escapeForJavascript($employees[$i][1] . " " . $employees[$i][2]) . "';\n";
	echo "ids[" . $i . "] = \"" . $employees[$i][0] . "\";\n";
}
?>

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

<?php include ROOT_PATH."/lib/common/autocomplete.php"; ?>

<style type="text/css">
#employeeSearchAC {
    width:20em; /* set width here */
    padding-bottom:2em;
    position:relative;
    top:-10px
}

#employeeSearchAC {
    z-index:9000; /* z-index needed on top instance for ie & sf absolute inside relative issue */
    float:left;
    margin-right:5px;
}

#projAdminName {
    _position:absolute; /* abs pos needed for ie quirks */
}
</style>

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
         <input type="hidden" value="<?php echo $token;?>" name="token" />
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
				<div class="yui-skin-sam" style="float:left;margin-right:10px;">
			            <div id="employeeSearchAC" style="width:150px;">
							<input type="text" name="projAdminName" id="projAdminName" style="margin:0px 0px 2px 0px; color:#999999" autocomplete="off"
								value="<?php echo $lang_Common_TypeHereForHints; ?>" onfocus="YAHOO.OrangeHRM.autocomplete.formatAutoCompleteField(this)" />
							<div id="employeeSearchACContainer" style="margin:-4px 0px 0px 0px;"></div>
						</div>
					</div>

                  	<input type="hidden" readonly name="projAdminID" id="projAdminID" value="" />
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

			if ($('projAdminName') != null) {
				YAHOO.OrangeHRM.autocomplete.ACJSArray = new function() {
				   	// Instantiate first JS Array DataSource
				   	this.oACDS = new YAHOO.widget.DS_JSArray(employees);
	
				   	// Instantiate AutoComplete for projAdminName
				   	this.oAutoComp = new YAHOO.widget.AutoComplete('projAdminName','employeeSearchACContainer', this.oACDS);
				   	this.oAutoComp.prehighlightClassName = "yui-ac-prehighlight";
				   	this.oAutoComp.typeAhead = false;
				   	this.oAutoComp.useShadow = true;
				   	this.oAutoComp.minQueryLength = 1;
				   	this.oAutoComp.textboxFocusEvent.subscribe(function(){
				   	    var sInputValue = YAHOO.util.Dom.get('projAdminName').value;
				   	    if(sInputValue.length === 0) {
				   	        var oSelf = this;
				   	        setTimeout(function(){oSelf.sendQuery(sInputValue);},0);
				   	    }
			   	});
			}
		}
        //]]>
        </script>

    <div id="" class="requirednotice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
    </div>
</body>
</html>
