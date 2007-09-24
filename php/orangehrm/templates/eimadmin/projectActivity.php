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
<html>
<head>
<title><?php echo $lang_Admin_ProjectActivities; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>

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
				echo "activityNames[{$i}] = \"{$activities[$i]->getName()}\";";
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
		document.frmActivity.activityName.value = activityName;
		document.frmActivity.activityId.value = activityId;
		if (addMode) {
			addMode = false;
		}
		displayAddLayer();
	}

</script>

<style type="text/css">
    <!--
    @import url("../../themes/<?php echo $styleSheet;?>/css/style.css");
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

    .notice {
    	font-family: Verdana, Arial, Helvetica, sans-serif;
    	font-size: -1;
    }

    .message {
		float:left;
		width:500px;
		text-align:right;
		font-family: Verdana, Arial, Helvetica, sans-serif;
    }

    -->
</style>

</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2><?php echo $lang_Admin_ProjectActivities; ?></h2>
		  		</td>
	  		<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td></tr>
		</table>
	</p>

<?php if (empty($projects)) { ?>
	<div class="notice"><?php echo $lang_Admin_Project_Error_NoProjects; ?></div>
<?php
	  } else {
?>

    <?php $message =  isset($this->getArr['msg']) ? $this->getArr['msg'] : (isset($this->getArr['message']) ? $this->getArr['message'] : null);
    	if (isset($message)) {
			$col_def = CommonFunctions::getCssClassForMessage($message);
			$message = "lang_Common_" . $message;
	?>
	<div class="message">
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
			<?php echo (isset($$message)) ? $$message: ""; ?>
		</font>
	</div>
	<?php
		}
	?>
	<br/>

    <div class="roundbox">
		<form name="frmActivity" method="post" action="<?php echo $formAction;?>">
        	<input type="hidden" name="sqlState" value="">
        	<input type="hidden" name="delState" value="">
        	<input type="hidden" name="activityId" value="">
            <label for="cmbProjectId"><?php echo $lang_Admin_Project; ?></label>
            <select name="cmbProjectId" onchange="<?php echo $selectProjectAction; ?>;">
				<?php
				  foreach ($projects as $project) {
					  $selected = ($project->getProjectId() == $projectId) ? 'selected' : '';
					  $projectName = htmlspecialchars($project->getProjectName());
					  echo "<option $selected value=\"{$project->getProjectId()}\">{$projectName}</option>";
				  }
   				?>
   			</select>
            <br/>
			<hr style="width:420px;float:left;margin:15px 0px 15px 0px"/></br>
      <?php if (empty($activities)) { ?>
			<div class="notice"><?php echo $lang_Admin_Project_NoActivitiesDefined; ?></div>
	  <?php } else { ?>
	      <div style="float:left">
			<table width="250" class="simpleList" >
				<thead>
					<tr>
					<th class="listViewThS1" align="center">
						<input type='checkbox' class='checkbox' name='allCheck' value=''
							<?php echo $disableEdit; ?> onClick="checkUncheckAll();">
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
	       			<td class="<?php echo $cssClass?>" align="center">
	       				<input type='checkbox' class='checkbox' name='chkLocID[]'
	       					<?php echo $disableEdit; ?> value='<?php echo $activity->getId();?>'>
	       			</td>
			 		<td class="<?php echo $cssClass?>">
			 		<?php
			 		$activityName = htmlspecialchars($activity->getName());
			 		$activityId = $activity->getId();
			 		if (empty($disableEdit)) {
			 			echo "<a href='#' onclick='editActivity({$activityId},\"{$activityName}\");'>{$activityName}</a>";
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

			</br>
            <div align="left">
	            <img onClick="<?php echo $addBtnAction; ?>;"
	            	onMouseOut="this.src='../../themes/beyondT/pictures/btn_add.jpg';"
	            	onMouseOver="this.src='../../themes/beyondT/pictures/btn_add_02.jpg';"
	            	src="../../themes/beyondT/pictures/btn_add.jpg">
	        <?php
	        	if (!empty($activities)) {
			?>
				<img
					onClick="<?php echo $delBtnAction; ?>"
				    src="../../themes/beyondT/pictures/btn_delete.jpg"
					onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.jpg';"
					onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';">
			<?php
				}
	        ?>
            </div>
			<div id ="addActivityLayer" style="display:none;height:20px;">
		    	<label for="activityName"><?php echo $lang_Admin_Activity; ?></label>
	            <input type="text" name="activityName" value="" >
	            	<img onClick="<?php echo $saveBtnAction; ?>;"
	            		style="margin-top:10px;"
	            		onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.jpg';"
	            		onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';"
	            		src="../../themes/beyondT/pictures/btn_save.jpg">
	            	<img onClick="<?php echo $cancelBtnAction; ?>;"
	            		style="margin-top:10px;"
	            		onMouseOut="this.src='../../themes/beyondT/icons/cancel.png';"
	            		onMouseOver="this.src='../../themes/beyondT/icons/cancel_o.png';"
	            		src="../../themes/beyondT/icons/cancel.png">
			</div>
      </form>
    </div>

    <script type="text/javascript">
    <!--
    	if (document.getElementById && document.createElement) {
 			initOctopus();
		}
    -->
    </script>

<?php
	  }
	  if (!empty($activities)) {
?>
    <div id="notice"><?php echo $lang_Admin_Project_Activity_ClickOnActivityToEdit; ?>.</div>
<?php
	  }
?>

</body>
</html>
