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

$directors = $records['directors'];
$application = $records['application'];
$locRights=$_SESSION['localRights'];

$baseURL = "{$_SERVER['PHP_SELF']}?recruitcode={$_GET['recruitcode']}";
$action = JobApplication::ACTION_SEEK_APPROVAL;
$formAction = $baseURL . '&action=' . $action;

$picDir = "../../themes/{$styleSheet}/pictures/";
$iconDir = "../../themes/{$styleSheet}/icons/";

$backImg = $picDir . 'btn_back.gif';
$backImgPressed = $picDir . 'btn_back_02.gif';
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>

<script>

    function goBack() {
        location.href = "<?php echo $baseURL; ?>&action=List";
    }

	function validate() {

		err = false;
		msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

		errors = new Array();
        if ($('cmbDirector').value == -1) {
			err = true;
			msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyDirector; ?>\n";
        }
        if ($('txtNotes').value.trim() == '') {
            err = true;
            msg += "\t- <?php echo $lang_Recruit_JobApplication_PleaseSpecifyNotes; ?>\n";
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
        	$('frmSeekApproval').submit();
		} else {
			return false;
		}
    }

	function reset() {
		$('frmSeekApproval').reset();
	}

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

    <style type="text/css">
    <!--

    label,select,input,textarea {
        display: block;  /* block float the labels to left column, set a width */
        width: 150px;
        float: left;
        margin: 10px 0px 2px 0px; /* set top margin same as form input - textarea etc. elements */
    }
    input[type="checkbox"] {
		width: 15px;
		background-color: transparent;
		vertical-align: bottom;
    }

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type="hidden"] {
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

    .calendarBtn {
        width: auto;
        border-style: none !important;
        border: 0px !important;
    }

    #txtDate, #txtTime {
        width: 100px;
    }

	#nodirectors {
		font-style: italic;
		color: red;
        padding-left: 10px;
        width: 400px;
        border: 1px;
	}
    .desc {
        padding-left: 15px;
        font-style: italic;
        padding-bottom: 20px;
    }
    -->
</style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2>

<?php
$applicantName = $application->getFirstName() . ' ' . $application->getLastName();
    echo $lang_Recruit_JobApplication_SeekApproval_Heading . ' ' . CommonFunctions::escapeHtml($applicantName);
?>
                    </h2>
		  		</td>
	  			<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
	  		</tr>
		</table>
	</p>
  	<div id="navigation" style="margin:0;">
  		<img title="Back" onMouseOut="this.src='<?php echo $backImg;?>';"
  			 onMouseOver="this.src='<?php echo $backImgPressed;?>'"
  			 src="<?php echo $backImg;?>" onClick="goBack();">
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

  <form name="frmSeekApproval" id="frmSeekApproval" method="post" action="<?php echo $formAction;?>">
		<input type="hidden" id="txtId" name="txtId" value="<?php echo $application->getId();?>"/><br/>

		<label for="cmbDirector"><span class="error">*</span> <?php echo $lang_Recruit_JobApplication_SeekApproval_GetApprovedBy; ?></label>
        <select id="cmbDirector" name="cmbDirector" tabindex="3">
	        <option value="-1">-- <?php echo $lang_Recruit_JobApplication_Select;?> --</option>
                <?php
                foreach ($directors as $director) {

                	// Ugly, but this is how EmpInfo returns employees
                	$empNum = $director[2];
                	$empName = CommonFunctions::escapeHtml($director[1]);
	                echo "<option value=". $empNum . ">" . $empName . "</option>";
                }
                ?>
        </select><br/>
		<?php
				if (count($directors) == 0) {
		?>
			<div id="nodirectors">
				<?php echo $lang_Recruit_NoDirectorsNotice; ?>
			</div>
		<?php
				}
		?>
		<label for="txtNotes"><span class="error">*</span><?php echo $lang_Recruit_JobApplication_SeekApproval_Notes; ?></label>
        <textarea id="txtNotes" name="txtNotes" tabindex="4"></textarea><br/>
		<br/><br/>
        <div class="desc"><?php echo $lang_Recruit_JobApplication_SeekApproval_Desc; ?></div>
        <div align="left">
            <img onClick="save();" id="saveBtn"
            	onMouseOut="this.src='<?php echo $picDir;?>btn_save.gif';"
                onMouseOver="this.src='<?php echo $picDir;?>btn_save_02.gif';"
            	src="<?php echo $picDir;?>btn_save.gif">
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
