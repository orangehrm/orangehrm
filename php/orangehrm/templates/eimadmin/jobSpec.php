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

$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}";
$new = true;
$disabled = '';
if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	$formAction="{$formAction}&id={$this->getArr['id']}&capturemode=updatemode";
	$new = false;
	$disabled = "disabled='true'";
}

$jobSpec = $this->popArr['jobSpec'];
$jobSpecs = $this->popArr['jobSpecList'];
$locRights=$_SESSION['localRights'];
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>

	var editMode = <?php echo $new ? 'true' : 'false'; ?>;

    var names = new Array();
<?php
	$nameOfThisSpec = $jobSpec->getName();
	foreach($jobSpecs as $spec) {
		$name = $spec->getName();
		if ($name != $nameOfThisSpec) {
			$name = strtolower($name);
	   		print "\tnames.push(\"{$name}\");\n";
		}
	}
?>

    function goBack() {
        location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
    }

	function validate() {
		err = false;
		msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

		errors = new Array();

		name = trim($('txtFieldName').value);
        if (name == '') {
			err = true;
			msg += "\t- <?php echo $lang_jobspec_PleaseSpecifyJobSpecName; ?>\n";
        } else if (isNameInUse(name)) {
			err = true;
			msg += "\t- <?php echo $lang_jobspec_NameInUse_Error; ?>\n";
        }

		if (err) {
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

	function reset() {
		$('frmJobSpec').reset();
	}


	function isNameInUse(name) {
		var lowerCaseName = name.toLowerCase(); 
		n = names.length;
		for (var i=0; i<n; i++) {
			if (names[i] == lowerCaseName) {
				return true;
			}
		}
		return false;
	}

	function checkName() {
		name = trim($('txtFieldName').value);
		oLink = $('messageCell');

		if (isNameInUse(name)) {
			oLink.innerHTML = "<?php echo $lang_jobspec_NameInUse_Error; ?>";
		} else {
			oLink.innerHTML = "&nbsp;";
		}
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
			if (validate()) {
				$('frmJobSpec').submit();
			}
			return;
		}
		editMode = true;
		var frm = $('frmJobSpec');

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
        width: 250px;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    br {
        clear: left;
    }

    .version_label {
        display: block;
        float: left;
        width: 150px;
        font-weight: bold;
        margin-left: 10px;
        margin-top: 10px;
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
    -->
</style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2><?php echo $lang_jobspec_heading; ?></h2>
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
	<?php }	?>
  <div class="roundbox">
  <form name="frmJobSpec" id="frmJobSpec" method="post" onsubmit="return validate()" action="<?php echo $formAction;?>">        	  
        <input type="hidden" name="sqlState" value="<?php echo $new ? 'NewRecord' : 'UpdateRecord'; ?>">
		<?php if ($new) { ?>
			<label for="txtId"><?php echo $jobSpec->getId(); ?></label>
		<?php } ?>
			<input type="hidden" id="txtId" name="txtId" value="<?php echo $jobSpec->getId();?>"/><br/>
			<label for="txtFieldName"><span class="error">*</span> <?php echo $lang_Commn_name; ?></label>
            <input type="text" id="txtFieldName" name="txtFieldName" tabindex="1"
            	value="<?php echo $jobSpec->getName(); ?>" onkeyup="checkName();" <?php echo $disabled;?> />
            <div id="messageCell" class="error" style="display:block; float: left; margin:10px;">&nbsp;</div><br/>
			<label for="txtDesc"><?php echo $lang_Commn_description; ?></label>
            <textarea type="text" id="txtDesc" name="txtDesc" tabindex="2"
            	<?php echo $disabled;?>><?php echo $jobSpec->getDesc(); ?></textarea><br/>
			<label for="txtDuties"><?php echo $lang_jobspec_duties; ?></label>
            <textarea type="text" id="txtDuties" name="txtDuties" tabindex="3"
            	<?php echo $disabled;?>><?php echo $jobSpec->getDuties(); ?></textarea><br/>
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
