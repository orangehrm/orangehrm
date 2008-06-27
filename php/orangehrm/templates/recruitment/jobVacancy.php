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

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$action = $_GET['action'];

if ($action == 'ViewAdd') {
	$new = true;
	$btnAction="addSave()";
	$heading = $lang_Recruit_JobVacancy_Add_Heading;
	$formAction = "{$baseURL}&action=Add";
	$disabled = '';
} else {
	$new = false;
	$btnAction="addUpdate()";
	$heading = $lang_Recruit_JobVacancy_Edit_Heading;
	$formAction = "{$baseURL}&action=Update";
	$disabled = "disabled='true'";
}

$noOfEmployees = $records['noOfEmployees'];
$manager = $records['manager']; 
$jobTitles = $records['jobTitles'];
$vacancy = $records['vacancy'];
$locRights=$_SESSION['localRights'];
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script type="text/javascript" src="../../scripts/enhancedSearchBox.js"></script>
<script>
	url = "../../lib/controllers/CentralController.php?recruitcode=AJAXCalls&action=LoadApproverList";
	table = "`hs_hr_employee` AS em";
	valueField = "em.`emp_number`";
	labelField = "CONCAT(em.`emp_firstname`, \' \', em.`emp_lastname`)";
	descField = "jt.`jobtit_name`";
	joinTable = "`hs_hr_job_title` AS jt";
	joinConditions = "jt.`jobtit_code` = em.`job_title_code`";
	var editMode = <?php echo $new ? 'true' : 'false'; ?>;


    function goBack() {
        location.href = "<?php echo $baseURL; ?>&action=List";
    }

	function validate() {
		err = false;
		msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

		errors = new Array();
        if ($('hidEnhancedSearchBox').value == -1) {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_JobVacancy_PleaseSpecifyHiringManager; ?>\n";
        }

        if ($('cmbJobTitle').value == -1) {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_JobVacancy_PleaseSpecifyJobTitle; ?>\n";
        }

		if (err) {
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

    function save() {

		if (validate()) {
        	$('frmJobVacancy').submit();
		} else {
			return false;
		}
    }

	function reset() {
		$('frmJobVacancy').reset();
	}


	function mout() {
		if(editMode) {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif';
		} else {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_edit.gif';
		}
	}

	function mover() {
		if(editMode) {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_save_02.gif';
		} else {
			$('editBtn').src='../../themes/<?php echo $styleSheet;?>/pictures/btn_edit_02.gif';
		}
	}

	function edit()	{

<?php if($locRights['edit']) { ?>
		if (editMode) {
			save();
			return;
		}
		editMode = true;
		var frm = $('frmJobVacancy');

		for (var i=0; i < frm.elements.length; i++) {
			frm.elements[i].disabled = false;
		}
		$('editBtn').src="../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif";
		$('editBtn').title="<?php echo $lang_Common_Save; ?>";

<?php } else {?>
		alert('<?php echo $lang_Common_AccessDenied;?>');
<?php } ?>
	}

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

    <style type="text/css">
    <!--

	.items {
		border-top: none;
		border-left: solid 1px #999999;
		border-right: solid 1px #999999;
		border-bottom: solid 1px #999999;
		padding: 4px;
		display: none;
		width: 240px;
	}

	#container {
		 display: table-row !important;
	}
	
	#dropdownPane {
		display: table-cell;
		border: none !important;
		text-align: left !important;
	}

	#txtEnhancedSearchBox {
		display: block;
		border-top: solid 1px #000000;
		border-left: solid 1px #000000;
		border-right: solid 1px #000000;
		border-bottom: solid 1px #000000;
	}

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 10px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }
    input[type=checkbox] {
		width: 15px;
		background-color: transparent;
		vertical-align: bottom;
    }

    #active {
        width: 15px;
        height: 15px;
        background-color: transparent;
        vertical-align: bottom;
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type=hidden] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 110px;
        padding-left: 10px;
    }

    select,input,textarea {
        margin-left: 10px;
    }

    input,textarea {
        padding-left: 4px;
        padding-right: 4px;
    }

    textarea {
        width: 330px;
        height: 150px;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    br {
        clear: left;
    }

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width: 500px;
    }

    .roundbox_content {
        padding:15px;
    }

	.hidden {
		display: none;
	}

	.display-block {
		display: block;
	}

	#nohiringmanagers {
		font-style: italic;
		color: red;
        padding-left: 10px;
        width: 400px;
        border: 1px;
	}
    -->
</style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2><?php echo $heading; ?></h2>
		  		</td>
	  			<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
	  		</tr>
		</table>
	</p>
  	<div id="navigation" style="margin:0;">
  		<img title="Back" onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_back.gif';"
  			 onMouseOver="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_back_02.gif';"
  			 src="../../themes/<?php echo $styleSheet;?>/pictures/btn_back.gif" onClick="goBack();">
	</div>
    <?php $message =  isset($_GET['message']) ? $_GET['message'] : null;
    	if (isset($message)) {
			$col_def = CommonFunctions::getCssClassForMessage($message);
			$message = "lang_Common_" . $message;
	?>
	<div class="message">
		<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
			<?php echo (isset($$message)) ? $$message: ""; ?>
		</font>
	</div>
	<?php }	?>
  <div class="roundbox">
  <form name="frmJobVacancy" id="frmJobVacancy" method="post" action="<?php echo $formAction;?>" onSubmit="return false;">
		<input type="hidden" id="txtId" name="txtId" value="<?php echo $vacancy->getId();?>"/><br/>
		<label for="cmbJobTitle"><span class="error">*</span> <?php echo $lang_Recruit_JobTitleName; ?></label>
        <select id="cmbJobTitle" name="cmbJobTitle" tabindex="1" <?php echo $disabled;?>>
	        <option value="-1">-- <?php echo $lang_Recruit_JobVacancy_JobTitleSelect;?> --</option>
                <?php
                $prevTitleCode = isset($this->postArr['cmbJobTitle']) ? $this->postArr['cmbJobTitle'] : $vacancy->getJobTitleCode();
                foreach ($jobTitles as $jobTitle) {
                	$jobTitleCode = $jobTitle[0];
                    $selected = ($prevTitleCode == $jobTitleCode) ? 'selected' : '';
	                echo "<option " . $selected . " value=". $jobTitleCode . ">" . $jobTitle[1] . "</option>";
                }
                ?>
        </select><br/>
		<?php
			$prevEmpNum = isset($this->postArr['cmbHiringManager']) ? $this->postArr['cmbHiringManager'] : $vacancy->getManagerId();
			if ($prevEmpNum == '') {
				$prevEmpNum = '-1';
				$empName = '';
			} else {
				$empName = $manager;
			}
		?>
		<label for="container"><span class="error">*</span> <?php echo $lang_Recruit_HiringManager; ?></label>
       	<span id="container" style="width: 250px;">
			<span style="display: table-row !important;">
				<span style="display: table-cell !important;">
					<input type="text" style="width: 250px; " onKeyUp="refreshList(this, event);" value="<?php echo $empName ?>" onBlur="" <?php echo $disabled; ?> />
					<input type="hidden" name="cmbHiringManager" id="hidEnhancedSearchBox" value="<?php echo $prevEmpNum ?>" />
				</span>
			</span><span style="display: table-row !important;">
				<span id="dropdownPane" style="display: table-cell !important; padding-left: 10px"></span>
			</span>
		</span><br/>
		<?php
				if ($noOfEmployees == 0) {
		?>
			<div id="nohiringmanagers">
				<?php echo $lang_Recruit_NoHiringManagersNotice; ?>
			</div>
		<?php
				}
		?>
		<label for="txtDesc"><?php echo $lang_Commn_description; ?></label>
        <textarea id="txtDesc" name="txtDesc" tabindex="3"
        	<?php echo $disabled;?>><?php echo htmlspecialchars($vacancy->getDescription()); ?></textarea><br/>
		<label for="active"><?php echo $lang_Recruit_JobVacancy_Active; ?></label>
        <input type="checkbox" id="active" name="active" tabindex="4" <?php echo $disabled;?>
        	<?php echo $vacancy->isActive() ? 'checked="1"':"";?> />
		<br/><br/>

        <div align="left">
            <img onClick="edit();" id="editBtn"
            	onMouseOut="mout();" onMouseOver="mover();"
            	src="../../themes/<?php echo $styleSheet;?>/pictures/<?php echo $new ? 'btn_save.gif' : 'btn_edit.gif';?>">
			<img id="saveBtn" src="../../themes/<?php echo $styleSheet;?>/pictures/btn_clear.gif"
			onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_clear.gif';"
			onMouseOver="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_clear_02.gif';" onClick="reset();" >
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

    <div id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
</body>
</html>
