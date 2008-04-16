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

$formAction="{$_SERVER['PHP_SELF']}?uniqcode={$this->getArr['uniqcode']}&id={$this->getArr['id']}&capturemode=updatemode";
$btnAction="addUpdate()";

$headings = $this->popArr['headings'];
$assignedFields = $this->popArr['assigned'];
$name = $this->popArr['exportName'];
$id = $this->popArr['id'];
$numFields = count($assignedFields);

?>
<html>
<head>
<title><?php echo $lang_DataExport_DefineCustomFieldHeadings_Heading; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script>

    function goBack() {
        location.href = "./CentralController.php?uniqcode=CEX&id=<?php echo $this->getArr['id'];?>&capturemode=updatemode";
    }

	function validate() {
		err = false;
		msg = '<?php echo $lang_Error_PleaseCorrectTheFollowing; ?>\n\n';

		errors = new Array();

		var headerValues = document.frmCustomExport.elements["headerValues[]"];

		for (var i = 0; i < headerValues.length; i++) {
			if (trim(headerValues[i].value) == '') {
				err = true;
				msg = '<?php echo $lang_DataExport_Error_AllHeadingsMustBeSpecified; ?>';
				headerValues[i].focus();
				break;
			}
			if (headerValues[i].value.indexOf(',') != -1) {
				err = true;
				msg = '<?php echo $lang_DataExport_Error_CommaNotAllowedInHeadings; ?>';
				headerValues[i].focus();
				break;
			}
		}

		if (!err) {

			// check for duplicates


		}

		if (err) {
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

  function addUpdate() {

		if (validate()) {
			document.frmCustomExport.sqlState.value  = "UpdateRecord";
			document.frmCustomExport.submit();
		} else {
			return false;
		}
	}

	/**
	 * Reset form, undoing any changes done
	 */
	function reset() {
		var headerValues = document.frmCustomExport.elements["headerValues[]"];

		for (var i = 0; i < headerValues.length; i++) {
			headerValues[i].value = '';
		}
	}

</script>

    <link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
    <style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>

    <style type="text/css">
    <!--

    input[type=text] {
        border-top: none;
        border-left: none;
        border-right: none;
        border-bottom: solid 1px black;
    }

    form {
        min-width: 550px;
        max-width: 600px;
    }

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width: 500px;
    }

    -->
</style>
</head>
<body>
	<p>
		<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
			<tr>
		  		<td width='100%'>
		  			<h2><?php echo $lang_DataExport_DefineCustomFieldHeadings_Heading . ' : ' . $name; ?></h2>
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
	<input type="hidden" id="txtFieldName" name="txtFieldName" tabindex="2" value="<?php echo $name; ?>" />
    <div align="left">
        <img onClick="<?php echo $btnAction; ?>;" onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif';" onMouseOver="this.src='../../themes/<?php echo $styleSheet;?>/pictures/btn_save_02.gif';" src="../../themes/<?php echo $styleSheet;?>/pictures/btn_save.gif">
		<img src="../../themes/<?php echo $styleSheet;?>/icons/reset.gif" onMouseOut="this.src='../../themes/<?php echo $styleSheet;?>/icons/reset.gif';" onMouseOver="this.src='../../themes/<?php echo $styleSheet;?>/icons/reset_o.gif';" onClick="reset();" >
    </div>
    <?php echo $lang_DataExport_EditColumnHeadings; ?><br /><br />
	<table class="simpleList" >
		<tr>
		   	<th width="125" style="align:left;"><?php echo $lang_DataExport_AssignedFields; ?></th>
			<th width="40"/>
		   	<th width="125" style="align:left;"><?php echo $lang_DataExport_ColumnHeadings; ?></th>
		</tr>
		<?php
			$odd = false;
			for ($i = 0; $i < $numFields; $i++) {
				$cssClass = ($odd) ? 'even' : 'odd';
				$odd = !$odd;
		?>
		<tr><td class="<?php echo $cssClass;?>"><input type="hidden" name="cmbAssignedFields[]" value="<?php echo $assignedFields[$i];?>"/><?php echo $assignedFields[$i];?></td>
			<td class="<?php echo $cssClass;?>"></td>
			<td class="<?php echo $cssClass;?>"><input type="text" name="headerValues[]" value="<?php echo $headings[$i];?>"/></td>
		</tr>
		<?php } ?>
	</table>
	</form>
    </div>
    <script type="text/javascript">
        <!--
        	if (document.getElementById && document.createElement) {
   	 			initOctopus();
			}
        -->
    </script>
</body>
</html>
