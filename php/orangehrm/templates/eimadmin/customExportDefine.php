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
$btnAction="addSave()";
if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	$formAction="{$formAction}&id={$this->getArr['id']}&capturemode=updatemode";
	$btnAction="addUpdate()";
}

$headings = $this->popArr['headings'];
$availableFields = $this->popArr['available'];
$assignedFields = $this->popArr['assigned'];
$name = $this->popArr['exportName'];
$id = $this->popArr['id'];
$customExportList = $this->popArr['customExportList'];

?>
<html>
<head>
<title><?php echo $lang_DataExport_DefineCustomField_Heading; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>
	names = new Array();
<?php
	if($customExportList) {
	   	foreach($customExportList as $export) {
	   		if (empty($id) || ($id != $export->getId())) {
	   			print "\tnames.push(\"{$export->getName()}\");\n";
	   		}
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
			msg += "\t- <?php echo $lang_DataExport_PleaseSpecifyExportName; ?>\n";
        } else if (isNameInUse(name)) {
			err = true;
			msg += "\t- <?php echo $lang_DataExport_Error_NameInUse; ?>\n";
        }

		if ($('cmbAssignedFields').length == 0) {
			err = true;
			msg += "\t- <?php echo $lang_DataExport_Error_AssignAtLeastOneField; ?>\n";
		}

		if (err) {
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

    function addSave() {

		if (validate()) {
        	document.frmCustomExport.sqlState.value = "NewRecord";
			selectAllOptions($('cmbAssignedFields'));
        	document.frmCustomExport.submit();
		} else {
			return false;
		}
    }

  function addUpdate() {

		if (validate()) {
			document.frmCustomExport.sqlState.value  = "UpdateRecord";
			selectAllOptions($('cmbAssignedFields'));
			document.frmCustomExport.submit();
		} else {
			return false;
		}
	}

	function clearAll() {
		document.frmCustomExport.txtFieldName.value='';
	}

	function assignFields() {
		moveSelectOptions($('cmbAvailableFields'), $('cmbAssignedFields'), '<?php echo $lang_DataExport_Error_NoFieldSelected; ?>');
	}

	function removeFields() {
		moveSelectOptions($('cmbAssignedFields'), $('cmbAvailableFields'), '<?php echo $lang_DataExport_Error_NoFieldSelected; ?>');
	}

	function moveUp() {
		res = moveSelectionsUp($('cmbAssignedFields'), '<?php echo $lang_DataExport_Error_NoFieldSelectedForMove;?>');
	}

	function moveDown() {
		res = moveSelectionsDown($('cmbAssignedFields'), '<?php echo $lang_DataExport_Error_NoFieldSelectedForMove;?>');
	}

	function isNameInUse(name) {
		n = names.length;
		for (var i=0; i<n; i++) {
			if (names[i] == name) {
				return true;
			}
		}
		return false;
	}

	function checkName() {
		name = trim($('txtFieldName').value);
		oLink = document.getElementById("messageCell");

		if (isNameInUse(name)) {
			oLink.innerHTML = "<?php echo $lang_DataExport_Error_NameInUse; ?>";
		} else {
			oLink.innerHTML = "&nbsp;";
		}

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

    /* this is needed because otherwise, hidden fields break the alignment of the other fields */
    input[type=hidden] {
        display: none;
        border: none;
        background-color: red;
    }

    label {
        text-align: left;
        width: 75px;
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
		  			<h2><?php echo $lang_DataExport_DefineCustomField_Heading; ?></h2>
		  		</td>
	  			<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
	  		</tr>
		</table>
	</p>
  	<div id="navigation" style="margin:0;">
  		<img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
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
  <form name="frmCustomExport" id="frmCustomExport" method="post" action="<?php echo $formAction;?>">
        <input type="hidden" name="sqlState" value="">
			<input type="hidden" id="txtId" name="txtId" value="<?php echo $id;?>"/>
			<label for="txtFieldName"><span class="error">*</span> <?php echo $lang_Commn_name; ?></label>
            <input type="text" id="txtFieldName" name="txtFieldName" tabindex="2" value="<?php echo $name; ?>" onkeyup="checkName();"/>
            <div id="messageCell" class="error" style="display:block; float: left; margin:10px;">&nbsp;</div>
			<br/>
            <div align="left">
	            <img onClick="<?php echo $btnAction; ?>;" onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif">
				<img src="../../themes/<?php echo $styleSheet;?>/pictures/btn_clear.gif" onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" onClick="clearAll();" >
            </div>
	<table border="0">
		<tr>
		   	<th width="100" style="align:center;"><?php echo $lang_DataExport_AvailableFields; ?></th>
			<th width="100"/>
		   	<th width="125" style="align:center;"><?php echo $lang_DataExport_AssignedFields; ?></th>
		</tr>
		<tr><td width="100" >
			<select size="10" id="cmbAvailableFields" name="cmbAvailableFields[]" style="width:125px;"
					multiple="multiple">
       			<?php
       				foreach($availableFields as $field) {
           				echo "<option value='{$field}'>{$field}</option>";
       				}
				?>
			</select></td>
			<td align="center" width="100">
				<input type="button" name="btnAssignField" id="btnAssignField" onClick="assignFields();" value=" <?php echo $lang_DataExport_Add; ?> >" style="width:80%"><br><br>
				<input type="button" name="btnRemoveField" id="btnRemoveField" onClick="removeFields();" value="< <?php echo $lang_DataExport_Remove; ?>" style="width:80%">
			</td>
			<td>
			<select size="10" name="cmbAssignedFields[]" id="cmbAssignedFields" style="width:125px;"
			        multiple="multiple">
       			<?php
       				foreach($assignedFields as $field) {
           				echo "<option value='{$field}'>{$field}</option>";
       				}
				?>
			</select></td>
			<td>
			<img id="btnMoveUp" onClick="moveUp();" alt="<?php echo $lang_DataExport_MoveUp; ?>"
				title="<?php echo $lang_DataExport_MoveUp; ?>"
				src="../../themes/<?php echo $styleSheet;?>/icons/up.gif"/><br><br>
			<img id="btnMoveDown" onClick="moveDown();" alt="<?php echo $lang_DataExport_MoveDown; ?>"
				title="<?php echo $lang_DataExport_MoveDown; ?>"
				src="../../themes/<?php echo $styleSheet;?>/icons/down.gif"/>
			</td>
		</tr>
	</table>
	<?php foreach ($headings as $heading) { ?>
		<input type="hidden" name="headerValues[]" value="<?php echo $heading;?>"/>
	<?php } ?>
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
