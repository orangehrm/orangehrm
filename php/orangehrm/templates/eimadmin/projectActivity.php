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
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/models/eimadmin/Projects.php';
require_once ROOT_PATH . '/lib/models/eimadmin/ProjectActivity.php';

require_once($lan->getLangPath("full.php"));

$locRights=$_SESSION['localRights'];
if ($locRights['edit']) {
	$disableEdit =  "";
	$addBtnAction  = "displayAddLayer()";
	$saveBtnAction= "saveActivity()";
	$delBtnAction  = "deleteActivities()";
	$selectProjectAction = "selectProject()";
	$cancelBtnAction = "cancelEdit()";
} else {
	$disableEdit = 'disabled = "true"';
	$addBtnAction  = "showAccessDeniedMsg()";
	$saveBtnAction="showAccessDeniedMsg()";
	$delBtnAction  = "showAccessDeniedMsg()";
	$selectProjectAction = "";
	$cancelBtnAction = "showAccessDeniedMsg()";
}

$projects = $this->popArr['projects'];
$projectId = $this->popArr['projectId'];
$activities = $this->popArr['activities'];

$formAction = "{$_SERVER['PHP_SELF']}?uniqcode=PAC";
if (!empty($projectId)) {
	$formAction .= "&projectId={$projectId}";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $lang_Admin_ProjectActivities; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[

	/** Global edit mode of form */
	var addMode = true;

	/**
	 * Add a new project activity
	 */
 	function saveActivity() {

		if (validateFields()) {

			formAction = "<?php echo $formAction; ?>";
			with (document.frmActivity) {
				if (addMode) {
					action = formAction;
	        		sqlState.value = "NewRecord";
				} else {
					action = formAction + "&id=" + activityId.value;
					sqlState.value = "UpdateRecord";
				}
			}
        	document.frmActivity.submit();
		}
    }


   /**
    * Validates the form fields in the form
    * returns true if validated, false otherwise.
    */
   function validateFields() {
   		var name = document.frmActivity.activityName;
        if(name.value == '') {
            alert("<?php echo $lang_Admin_Project_Activity_Error_PleaseSpecifyTheActivityName; ?>");
            name.focus();
            return false;
        }

        if (isDuplicateName(trim(name.value))) {
            alert("<?php echo $lang_Admin_Project_Activity_Error_NameAlreadyDefined; ?>");
            name.focus();
            return false;
        }
		return true;
   }

   /**
    * Checks if an activity with the given name already exists in this project
    */
    function isDuplicateName(name) {

    	var duplicate = false;
    	var activityNames = new Array();
    	<?php
    		for ($i=0; $i<count($activities); $i++) {
				echo "activityNames[{$i}] = \"" . CommonFunctions::escapeForJavascript($activities[$i]->getName()) . "\";";
    		}
    	?>
    	for (var i=0; i < activityNames.length; i++) {
    		if (activityNames[i] == name) {
    			duplicate = true;
    			break;
    		}
    	}
		return duplicate;
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
	function displayAddLayer() {
		document.getElementById("addActivityLayer").style.display = 'block';
        document.getElementById("addActivityLayer").style.float = 'left';
	}

	/**
	 * Run when the cancel button is pressed
	 */
	function cancelEdit() {
		document.getElementById("addActivityLayer").style.display = 'none';
		document.frmActivity.activityName.value = "";
		document.frmActivity.activityId.value = "";
		addMode = true;
	}

	/**
	 * Check or uncheck all project activity check boxes.
	 */
	function checkUncheckAll() {

		var projectId = document.frmActivity.cmbProjectId.value;
		document.frmActivity.action = "./CentralController.php?uniqcode=PAC&VIEW=MAIN&projectId=" + projectId;

		var checked;

		with (document.frmActivity) {

			checked = elements['allCheck'].checked;

			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = checked;
				}
			}
		}
	}
	
	/**
	 * If at least one activity is unchecked, main check box would be unchecked
	 */
	
	function unCheckMain() {
	    
		var allCheck = document.frmActivity.allCheck;
		
		with (document.frmActivity) {

			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox' && elements[i] != allCheck && elements[i].checked == true) {
					allCheck.checked = false;
					return;
				}
			}
			
		}
	    
	}

	/**
	* When a check box is clicked, form action is changed according to selected ProjectID
	*/

	function setFormAction() {
		unCheckMain();
		var projectId = document.frmActivity.cmbProjectId.value;
		document.frmActivity.action = "./CentralController.php?uniqcode=PAC&VIEW=MAIN&projectId=" + projectId;
	}

	/**
	 * Delete selected activities.
	 */
	function deleteActivities() {

		var check = false;
		with (document.frmActivity) {

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
	 * Change the selected project
	 */
	function selectProject() {
		var projectId = document.frmActivity.cmbProjectId.value;
		location.href = "./CentralController.php?uniqcode=PAC&VIEW=MAIN&projectId=" + projectId;
	}

	/**
	 * Edit the given activity's name
	 */
	function editActivity(activityId, activityName) {
		activityName = replaceAll('&amp;', '&', activityName);
		activityName = replaceAll('&lt;', '<', activityName);
		activityName = replaceAll('&gt;', '>', activityName);
		
		document.frmActivity.activityName.value = activityName;
		document.frmActivity.activityId.value = activityId;
		if (addMode) {
			addMode = false;
		}
		displayAddLayer();
	}

	function replaceAll(needle, replacement, haystack) {
		while (haystack.indexOf(needle) != -1) {
			haystack = haystack.replace(needle, replacement);
		}

		return haystack;
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
        <div class="outerbox">
            <div class="mainHeading"><h2><?php echo $lang_Admin_ProjectActivities;?></h2></div>
        
        <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
            if (isset($message)) {
                $messageType = CommonFunctions::getCssClassForMessage($message);
                $message = "lang_Common_" . $message;
        ?>
            <div class="messagebar">
                <span class="<?php echo $messageType; ?>"><?php echo (isset($$message)) ? $$message: ""; ?></span>
            </div>  
        <?php } ?>

<?php if (empty($projects)) { ?>
	<div class="notice"><?php echo $lang_Admin_Project_Error_NoProjects; ?></div>
<?php
	  } else {
?>
		<form name="frmActivity" method="post" action="<?php echo $formAction;?>">
        	<input type="hidden" name="sqlState" value=""/>
        	<input type="hidden" name="delState" value=""/>
        	<input type="hidden" name="activityId" value=""/>
            <label for="cmbProjectId"><?php echo $lang_Admin_Project; ?></label>
            <select id="cmbProjectId" name="cmbProjectId" onchange="<?php echo $selectProjectAction; ?>;"
                class="formSelect">
				<?php
				  foreach ($projects as $project) {
					  $selected = ($project->getProjectId() == $projectId) ? 'selected="selected"' : '';
					  $projectName = $project->getProjectName();
                      $customerName = $project->getCustomerName();
                      $displayString = $customerName . ' - ' . $projectName;
					  echo "<option $selected value=\"{$project->getProjectId()}\">{$displayString}</option>";
				  }
   				?>
   			</select>
            <br class="clear"/>
            
			<hr style="width:420px;float:left;margin:15px 0px 15px 0px"/>
            <br class="clear"/>
      <?php if (empty($activities)) { ?>
			<div class="notice"><?php echo $lang_Admin_Project_NoActivitiesDefined; ?></div>
	  <?php } else { ?>
	      <div style="float:left">
			<table width="250" class="simpleList" >
				<thead>
					<tr>
					<th class="listViewThS1">
						<input type='checkbox' class='checkbox' name='allCheck' value=''
							<?php echo $disableEdit; ?> onclick="checkUncheckAll();"/>
					</th>
					<th class="listViewThS1"><?php echo $lang_Admin_Activity; ?></th>
					</tr>
	    		</thead>
				<?php
					$odd = false;
					foreach ($activities as $activity) {
		 	 	 		$cssClass = ($odd) ? 'even' : 'odd';
		 	 	 		$odd = !$odd;
		 		?>
	    		<tr>
	       			<td class="<?php echo $cssClass?>">
	       				<input type='checkbox' class='checkbox' name='chkLocID[]'
	       					<?php echo $disableEdit; ?> value='<?php echo $activity->getId();?>' onclick="setFormAction();"/>
	       			</td>
			 		<td class="<?php echo $cssClass?>">
			 		<?php
			 		$activityName = htmlspecialchars($activity->getName(),ENT_QUOTES);
			 		$activityId = $activity->getId();
			 		if (empty($disableEdit)) {
			 			echo "<a href=\"#\" onclick=\"editActivity({$activityId}, this.innerHTML);\">{$activityName}</a>";
			 		} else {
			 			echo $activityName;
			 		}
			 		?>
			 		</td>
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
                <input type="button" class="savebutton" id="saveBtn" 
                    onclick="<?php echo $addBtnAction; ?>;" tabindex="4" onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                    value="<?php echo $lang_Common_Add;?>" />
            <?php
                if (!empty($activities)) {
            ?>
                <input type="button" class="clearbutton" onclick="<?php echo $delBtnAction;?>" tabindex="5"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);" 
                     value="<?php echo $lang_Common_Delete;?>" />
            <?php
                }
            ?>
<?php } ?>                         
            </div>
            <br class="clear"/>
                        
			<div id ="addActivityLayer" style="display:none;">
		    	<label for="activityName"><?php echo $lang_Admin_Activity; ?></label>
                <input type="text" name="activityName" id="activityName" value="" class="formInputText" maxlength="60" />
                <br class="clear"/>
                                
                 <div class="formbuttons">
    <?php if($locRights['edit']) { ?>                
                    <input type="button" class="savebutton" id="adminSaveBtn" 
                        onclick="<?php echo $saveBtnAction; ?>;" tabindex="7" onmouseover="moverButton(this);" onmouseout="moutButton(this);"                          
                        value="<?php echo $lang_Common_Save;?>" />
                    <input type="button" class="clearbutton" onclick="<?php echo $cancelBtnAction;?>" tabindex="8"
                        id="adminCancelBtn"
                        onmouseover="moverButton(this);" onmouseout="moutButton(this);" 
                         value="<?php echo $lang_Common_Cancel;?>" />
    <?php } ?>                         
                </div>                
    <?php                
    ?>                
			</div>
            <br class="clear"/>            
      </form>
    </div>

        <script type="text/javascript">
        //<![CDATA[
            if (document.getElementById && document.createElement) {
                roundBorder('outerbox');                
            }
        //]]>
        </script>
<?php
	  }
      if (!empty($activities)) {
?>
    <div id="notice"><?php echo $lang_Admin_Project_Activity_ClickOnActivityToEdit; ?>.</div>
<?php
      }
?>
</div>
</body>
</html>
