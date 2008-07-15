<?php
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
all the essential functionalities required for any enterprise.
Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
the GNU General Public License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA
*/

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once($lan->getLangPath("full.php"));

	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];
	$common_func = new CommonFunctions();

	$_SERVER['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'];

	$idens = split('uniqcode=', isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER']);

	$idens = split('&', $idens[1]);

	if ($idens[0] == 'JOB') {
		$backtype=1;
	} else {
		$backtype=0;
	};

if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) {


	if ($backtype == 1) {

		$refcapturemode = split('capturemode=', isset($postArr['referer']) ? $postArr['referer'] : $_SERVER['HTTP_REFERER']);
		$refcapturemode = split('&', $refcapturemode[1]);

		if ($refcapturemode[0] == 'updatemode') {

			$refcapturemode = $refcapturemode[0];

			$refid = split('id=', isset($postArr['referer']) ? $postArr['referer'] : $_SERVER['HTTP_REFERER']);

			$refid = split('&', $refid[1]);

			$refid = $refid[0];
		} else {

			$refcapturemode = 'addmode';
			$refid = '';

		}
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
	function goBack() {

	<?php	if ($backtype == 1) { ?>
		history.back();
	<?php } else { ?>
		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
	<?php } ?>

	}

	function addSave() {

		if (document.frmSalGrd.txtSalGrdDesc.value == '') {
			alert ('<?php echo $lang_salarygrades_NameCannotBeBlank; ?>');
			return false;
		}

		document.frmSalGrd.sqlState.value = "NewRecord";
		document.frmSalGrd.submit();
		return true;
	}

	function clearAll() {
		document.frmSalGrd.txtSalGrdDesc.value = '';
	}
</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?php echo $lang_salarygrades_heading; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<form name="frmSalGrd" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>" onSubmit="return addSave()">
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">

  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
		<input type="hidden" name="refcapturemode" value="<?php echo isset($refcapturemode)? $refcapturemode : ''?>">
		<input type="hidden" name="refid" value="<?php echo isset($refid) ? $refid : ''?>">
		<input type="hidden" name="backtype" value="<?php echo isset($backtype) ? $backtype : 0?>">
		<input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']?>">
      </p></td>
    <td width="254" align='left' valign='bottom'>&nbsp;</td>
  </tr><td width="177">
</table>
				<font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
		      	<?php
				if (isset($this->getArr['msg'])) {
					$expString  = $this->getArr['msg'];
					$errorMsg = "lang_Error_salarygrades_$expString";
					echo $$errorMsg;
				}
				?>
		      </font>
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <tr>
						    <td width="16%" nowrap><span class="error">*</span> <?php echo $lang_compstruct_Name; ?></td>
						    <td><input type="text" name="txtSalGrdDesc" size="40" tabindex="3">
						    </td>
						  </tr>
					  <tr><td></td><td align="right" width="84%"><img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
        <img onClick="clearAll();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" src="../../themes/beyondT/pictures/btn_clear.gif"></td>
					  </tr>

                  </table></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
</form>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
<?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 $backtype = isset($_GET['backtype']) ? $_GET['backtype'] : $backtype;

	 if (isset($_GET['backtype']) && ($_GET['backtype'] == 1)) {
	 	if (isset($_GET['refcapturemode']) && ($_GET['refcapturemode'] == 'addmode')) {

			$referer = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?uniqcode=JOB&capturemode=".$_GET['refcapturemode'];

		} else if (isset($_GET['refcapturemode']) && ($_GET['refcapturemode'] == 'updatemode')) {

			$referer = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?id=".$_GET['refid']."&uniqcode=JOB&capturemode=".$_GET['refcapturemode'];

		} else {
	 		$referer = isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER'];
	 	}
	 } else {
	 	$referer = isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER'];
	 }
	 $message = $this->popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript">
	function numeric(txt) {
		var flag=true;
		var i,code;

		if(txt.value=="")
		   return false;

		for(i=0;txt.value.length>i;i++)
			{
			code=txt.value.charCodeAt(i);
		    if(code>=48 && code<=57)
			   flag=true;
			else
			   {
			   flag=false;
			   break;
			   }
			}
		return flag;
	}

	function decimal(txt) {
		regExp = /^[0-9]*(\.[0-9]+)*$/;

		return regExp.test(txt.value);
	}

	function goBack() {
	<?php if ($backtype == 1) {
			if (preg_match('/index\.php/', $referer)) {
				$referer = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
			}
	?>
		location.href = "<?php echo $referer?>";
	<?php } else { ?>
		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
	<?php } ?>

	}

	function mout() {
		if(document.Edit.title=='Save')
			document.Edit.src='../../themes/beyondT/pictures/btn_save.gif';
		else
			document.Edit.src='../../themes/beyondT/pictures/btn_edit.gif';
	}

	function mover() {
		if(document.Edit.title=='Save')
			document.Edit.src='../../themes/beyondT/pictures/btn_save_02.gif';
		else
			document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.gif';
	}

	function edit() {
		if(document.Edit.title=='Save') {
			addUpdate();
			return;
		}

		var frm=document.frmSalGrd;

		for (var i=0; i < frm.elements.length; i++)
			frm.elements[i].disabled = false;
		document.Edit.src="../../themes/beyondT/pictures/btn_save.gif";
		document.Edit.title="Save";
	}

	function addUpdate() {

		if (document.frmSalGrd.txtSalGrdDesc.value == '') {
			alert ('<?php echo $lang_salarygrades_NameCannotBeBlank; ?>');
			return false;
		}

		document.frmSalGrd.sqlState.value = "UpdateRecord";
		document.frmSalGrd.submit();

	}

	function addEXT() {

		if(document.frmSalCurDet.cmbUnAssCurrency.value=='0') {
			alert("<?php echo $lang_salarygrades_selectcurrency; ?>");
			document.frmSalCurDet.cmbUnAssCurrency.focus();
			return;
		}

		var cnt=document.frmSalCurDet.txtMinSal;
		var bMinEmpty = (cnt.value == '' || cnt.value == null);

		if(!bMinEmpty) {
			if(!decimal(cnt)) {
				alert("<?php echo $lang_salarygrades_minSalNumeric; ?>");
				cnt.focus();
				return;
			} else {
				var min=eval(cnt.value);
			}
		}

		var cnt=document.frmSalCurDet.txtMaxSal;
		var bMaxEmpty = (cnt.value == '' || cnt.value == null);

		if(!bMaxEmpty) {
			if(!decimal(cnt)) {
				alert("<?php echo $lang_salarygrades_maxSalNumeric; ?>");
				cnt.focus();
				return;
			} else {
				var max=eval(cnt.value);
			}
		}

		if(!bMaxEmpty && !bMinEmpty && min > max) {
			alert("<?php echo $lang_salarygrades_minGreaterThanMax; ?>");
			return;
		}

		var cnt=document.frmSalCurDet.txtStepSal;
		var bStepSalEmpty = (cnt.value == '' || cnt.value == null);

		if(!bStepSalEmpty) {
			if(!decimal(cnt)) {
				alert("<?php echo $lang_salarygrades_stepSalNumeric; ?>");
				cnt.focus();
				return;
			} else if (!bMaxEmpty){
				var minPlusStep = eval(cnt.value);
				if (!bMinEmpty) {
					errMsg = "<?php echo $lang_salarygrades_stepPlusMinGreaterThanMax; ?>";
					minPlusStep += min;
				} else {
					errMsg = "<?php echo $lang_salarygrades_stepGreaterThanMax; ?>";
				}

				if (minPlusStep > max) {
					alert(errMsg);
					cnt.focus();
					return;
				}
			}
		}

		document.frmSalCurDet.STAT.value="ADD";
		document.frmSalCurDet.submit();
	}

	function editEXT() {

		var cnt=document.frmSalCurDet.txtMinSal;
		var bMinEmpty = (cnt.value == '' || cnt.value == null);

		if(!bMinEmpty) {
			if(!decimal(cnt)) {
				alert("<?php echo $lang_salarygrades_minSalNumeric; ?>");
				cnt.focus();
				return;
			} else {
				var min=eval(cnt.value);
			}
		}

		var cnt=document.frmSalCurDet.txtMaxSal;
		var bMaxEmpty = (cnt.value == '' || cnt.value == null);

		if(!bMaxEmpty) {
			if(!decimal(cnt)) {
				alert("<?php echo $lang_salarygrades_maxSalNumeric; ?>");
				cnt.focus();
				return;
			} else {
				var max=eval(cnt.value);
			}
		}

		if(!bMaxEmpty && !bMinEmpty && min > max) {
			alert("<?php echo $lang_salarygrades_minGreaterThanMax; ?>");
			return;
		}

		var cnt=document.frmSalCurDet.txtStepSal;
		var bStepSalEmpty = (cnt.value == '' || cnt.value == null);

		if(!bStepSalEmpty) {
			if(!decimal(cnt)) {
				alert("<?php echo $lang_salarygrades_stepSalNumeric; ?>");
				cnt.focus();
				return;
			} else if (!bMaxEmpty){
				var minPlusStep = eval(cnt.value);
				if (!bMinEmpty) {
					errMsg = "<?php echo $lang_salarygrades_stepPlusMinGreaterThanMax; ?>";
					minPlusStep += min;
				} else {
					errMsg = "<?php echo $lang_salarygrades_stepGreaterThanMax; ?>";
				}

				if (minPlusStep > max) {
					alert(errMsg);
					cnt.focus();
					return;
				}
			}
		}

		document.frmSalCurDet.STAT.value="EDIT";
	        document.frmSalCurDet.submit();
	}

	function delEXT() {

	      var check = 0;
			with (document.frmSalCurDet) {
				for (var i=0; i < elements.length; i++) {
					if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
						check = 1;
					}
				}
	        }

	        if(check==0) {
	          alert('<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>');
	          return;
	        }

		document.frmSalCurDet.STAT.value="DEL";
	    document.frmSalCurDet.submit();
	}

	function moutAss() {

		if(document.EditAss.title=='Save')
			document.EditAss.src='../../themes/beyondT/pictures/btn_save.gif';
		else
			document.EditAss.src='../../themes/beyondT/pictures/btn_edit.gif';
	}

	function moverAss() {
		if(document.EditAss.title=='Save')
			document.EditAss.src='../../themes/beyondT/pictures/btn_save_02.gif';
		else
			document.EditAss.src='../../themes/beyondT/pictures/btn_edit_02.gif';
	}

	function editAss() {

		if(document.EditAss.title=='Save') {
			editEXT();
			return;
		}

		var frm=document.frmSalCurDet;

		for (var i=0; i < frm.elements.length; i++)
			frm.elements[i].disabled = false;
		document.EditAss.src="../../themes/beyondT/pictures/btn_save.gif";
		document.EditAss.title="Save";
	}

	function editCurrency(currID) {

		location.href = document.frmSalCurDet.action + "&editID=" + currID;
	}

	function clearAll() {
		if(document.Edit.title!='Save')
			return;

		document.frmSalGrd.txtSalGrdDesc.value = '';
	}


</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?php echo $lang_salarygrades_heading; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><div id="status"></div></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmSalGrd" id="frmSalGrd" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=<?php echo $this->getArr['uniqcode']?>" onsubmit="return addUpdate();">

  <tr>
    <td height="27" valign='top'> <p>  <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';" src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
		<input type="hidden" name="backtype" value="<?php echo $backtype?>">
		<input type="hidden" name="referer" value="<?php echo $referer?>">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      <?php
		if (isset($this->getArr['msg'])) {
			$expString  = $this->getArr['msg'];
			$expString = explode ("%",$expString);
			$length = sizeof($expString);
			for ($x=0; $x < $length; $x++) {
				echo " " . $expString[$x];
			}
		}
		?>
      </font> </td>
  </tr><td width="177">
</table>
           <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                  			<tr><td>
							<table border="0">
                  			<tr>
							    <td width="100"><?php echo $lang_Commn_code; ?></td>
							    <td> <input type="hidden" name="txtSalGrdID" value=<?php echo $message[0][0]?>> <strong><?php echo $message[0][0]?></strong> </td>
							    <td>&nbsp;</td>
							  </tr>
							  <tr>
							    <td width="16%" nowrap="nowrap"><span class="error">*</span> <?php echo $lang_compstruct_Name; ?></td>
							  	  <td><input type="text" name="txtSalGrdDesc" size="40" tabindex="3" disabled value="<?php echo $message[0][1]?>">
							    </td>
							  </tr>
			<tr>
		  <td></td><td align="right">
<?php			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">
<?php			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" onClick="alert('<?php echo $lang_Common_AccessDenied;?>');">
<?php			}  ?>
					  <img src="../../themes/beyondT/pictures/btn_clear.gif" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" onClick="clearAll();" >
						</td>
						</form>
						</tr>
					  </table>
					  </td>
					  </tr>

				<form name="frmSalCurDet" id="frmSalCurDet" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&capturemode=updatemode">
					  			<input type="hidden" name="STAT">
								<input type="hidden" name="referer" value="<?php echo $referer?>">
								<input type="hidden" name="txtSalGrdID" value="<?php echo $this->getArr['id']?>">
<?php			if (!isset($this->getArr['editID'])) { ?>
					  <tr>
					  	<td height="40" valign="bottom"><h3><?php echo $lang_salarygrades_currAss; ?></h3></td>
					  </tr>
					  <tr>
					  <td>
					  		<table border="0">
			                  <tr>
									<td><span class="error">*</span> <?php echo $lang_hrEmpMain_currency; ?></td>
									<td><select <?php echo ($locRights['add']) ? '' : 'disabled'?> name="cmbUnAssCurrency">
											<option value="0">---<?php echo "$lang_Leave_Common_Select $lang_hrEmpMain_currency"; ?>---</option>
			               			<?php $unAssCurrency = $this->popArr['unAssCurrency'];
			               				for($c=0;$unAssCurrency && count($unAssCurrency)>$c;$c++)
				               				echo "<option value='" .$unAssCurrency[$c][0]. "'>" .$unAssCurrency[$c][1]. "</option>";
									?>
									</select>
 									</td>
							</tr>

							<tr>
								<td><?php echo $lang_hrEmpMain_minpoint; ?></td>
								<td><input type="text" <?php echo ($locRights['add']) ? '' : 'disabled'?> name="txtMinSal"></td>
							</tr>
							<tr>
								<td><?php echo $lang_hrEmpMain_maxpoint; ?></td>
								<td><input type="text" <?php echo ($locRights['add']) ? '' : 'disabled'?> name="txtMaxSal"></td>
							</tr>
							<tr>
								<td><?php echo $lang_salarygrades_stepSal; ?></td>
								<td><input type="text" <?php echo ($locRights['add']) ? '' : 'disabled'?> name="txtStepSal"></td>
							</tr>
			<tr>
				<td>
<?php					if($locRights['add']) { ?>
						<td align="left" valign="top"><img onClick="addEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
<?php					} else { ?>
						<td align="left" valign="top"><img onClick="alert('<?php echo $lang_Common_AccessDenied;?>');" src="../../themes/beyondT/pictures/btn_save.gif">
<?php					}

			} elseif(isset($this->getArr['editID'])) {

				$editAssCurrency = $this->popArr['editAssCurrency'];
				?>
					  <tr>
					  	<td height="40" valign="bottom"><h3><?php echo $lang_salarygrades_currAss; ?></h3></td>
					  </tr>
					  <tr>
					  <td>
					  		<table border="0">
			                  <tr>
									<td><span class="error">*</span> <?php echo $lang_hrEmpMain_currency; ?></td> <input type="hidden" name="cmbUnAssCurrency" value="<?php echo $editAssCurrency[0][1]?>">
									<td><strong>
			               			<?php $assCurrency = $this->popArr['assCurrency'];
			               				for($c=0;$assCurrency && count($assCurrency)>$c;$c++)
			               					if($assCurrency[$c][0] == $editAssCurrency[0][1])
				               					echo $assCurrency[$c][1];
									?>
									</strong></td>
							</tr>
							<tr>
								<td> <?php echo $lang_hrEmpMain_minpoint; ?></td>
								<td><input type="text" disabled name="txtMinSal" value="<?php echo $common_func->formatSciNO($editAssCurrency[0][2]);?>"></td>
							</tr>
							<tr>
								<td> <?php echo $lang_hrEmpMain_maxpoint; ?></td>
								<td><input type="text" disabled name="txtMaxSal" value="<?php echo $common_func->formatSciNO($editAssCurrency[0][3]);?>"></td>
							</tr>
							<tr>
								<td> <?php echo $lang_salarygrades_stepSal; ?></td>
								<td><input type="text" disabled name="txtStepSal" value="<?php echo $common_func->formatSciNO($editAssCurrency[0][4]);?>"></td>
							</tr>
			<tr>
		  <td></td><td align="right">
<?php			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onMouseOut="moutAss();" onMouseOver="moverAss();" name="EditAss" onClick="editAss();">
<?php			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" onClick="alert('<?php echo $lang_Common_AccessDenied;?>');">
<?php			}

		}?>
					</td>
					  </tr>

					  </table>
					  </td>
					  </tr>
					<?php
					  $assCurrency = $this->popArr['assCurrency'];

					  if ($assCurrency) {
					   ?>
					  <tr>
					  	<td>
<?php					if($locRights['delete']) { ?>
						<img onClick="delEXT();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_delete.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_delete_02.gif';" src="../../themes/beyondT/pictures/btn_delete.gif">
<?php					} else { ?>
						<img onClick="alert('<?php echo $lang_Common_AccessDenied;?>');" src="../../themes/beyondT/pictures/btn_delete.gif">
<?php					}		?>
					  </td>
					  </tr>
					  <tr>
							<td>
							<table border="0">
								<tr>
			                      	<td></td>
									 <td><strong><?php echo $lang_hrEmpMain_currency; ?></strong></td>
									 <td><strong><?php echo $lang_hrEmpMain_minpoint; ?></strong></td>
									 <td><strong><?php echo $lang_hrEmpMain_maxpoint; ?></strong></td>
									 <td><strong><?php echo $lang_salarygrades_stepSal; ?></strong></td>
								</tr>
			               		<?php
			               			for($c=0;$assCurrency && count($assCurrency)>$c;$c++) {
			               				echo '<tr>';
			               				echo "<td><input type='checkbox' name='chkdel[]' value='".$assCurrency[$c][0]."'></td>";
				            			echo "<td><a href=javascript:editCurrency('".$assCurrency[$c][0]."')>" .$assCurrency[$c][1] . "</a></td>";
				            			echo "<td>" .$common_func->formatSciNO($assCurrency[$c][2]). "</td>";
				            			echo "<td>" .$common_func->formatSciNO($assCurrency[$c][3]). "</td>";
				            			echo "<td>" .$common_func->formatSciNO($assCurrency[$c][4]). "</td>";
			               				echo '</tr>';
			               			}
								?>
							</table>
								</td>
						</tr>
						<?php } ?>
                  </table></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

                <tr>
                  <td><img name="table_r3_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
</form>
</body>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</html>
<?php } ?>
