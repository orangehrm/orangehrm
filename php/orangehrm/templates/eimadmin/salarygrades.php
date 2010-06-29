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

require_once ROOT_PATH . '/lib/controllers/ViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once($lan->getLangPath("full.php"));

/* Setting message: Begins */
$token = $this->popArr['token'];
if (isset($this->getArr['msg'])) {

	if ($this->getArr['msg'] == 'UPDATE_SUCCESS') {
	    $messageText = $lang_Common_UPDATE_SUCCESS;
	    $messageType = 'SUCCESS';
	} elseif ($this->getArr['msg'] == 'DUPLICATE_NAME_FAILURE') {
	    $messageText = $lang_Error_salarygrades_DUPLICATE_NAME_FAILURE;
	    $messageType = 'FAILURE';
	} elseif ($this->getArr['msg'] == 'UPDATE_FAILURE') {
	    $messageText = $lang_Error_salarygrades_ADD_FAILURE;
	    $messageType = 'FAILURE';
	}

} else {

    $messageText = null;

}

/* Setting message: Ends */

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[
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
    <div class="navigation">
        <input type="button" class="savebutton"
        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
        value="<?php echo $lang_Common_Back;?>" />
    </div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_salarygrades_heading;?></h2></div>

<?php if (isset($messageText)) { ?>
<div class="messagebar">
	<span class="<?php echo $messageType; ?>"><?php echo $messageText; ?></span>
</div>
<?php } ?>

<form name="frmSalGrd" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>" onSubmit="return addSave()">
     <input type="hidden" name="token" value="<?php echo $token;?>" />
      <input type="hidden" name="sqlState" value=""/>
		<input type="hidden" name="refcapturemode" value="<?php echo isset($refcapturemode)? $refcapturemode : ''?>"/>
		<input type="hidden" name="refid" value="<?php echo isset($refid) ? $refid : ''?>"/>
		<input type="hidden" name="backtype" value="<?php echo isset($backtype) ? $backtype : 0?>"/>
		<input type="hidden" name="referer" value="<?php echo $_SERVER['HTTP_REFERER']?>"/>

        <label for="txtSalGrdDesc"><?php echo $lang_compstruct_Name; ?><span class="required">*</span></label>
        <input type="text" name="txtSalGrdDesc" id="txtSalGrdDesc" size="40" class="formInputText"/>
        <br class="clear"/>

        <div class="formbuttons">
            <input type="button" class="savebutton"
                onclick="addSave();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" />
            <input type="reset" class="clearbutton"  onmouseover="moverButton(this);"
            	onmouseout="moutButton(this);" value="<?php echo $lang_Common_Reset;?>" />
            <br class="clear"/>
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
<div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="required">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>
</div>
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[
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
        var editBtn = $('editBtn');

        if(editBtn.title=='<?php echo $lang_Common_Save;?>') {
            addUpdate();
            return;
        }

        var frm=document.frmSalGrd;

        for (var i=0; i < frm.elements.length; i++) {
            frm.elements[i].disabled = false;
        }

        editBtn.className = "savebutton";
        editBtn.title = "<?php echo $lang_Common_Save;?>";
        editBtn.value = "<?php echo $lang_Common_Save;?>";
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

        var empMinSal = parseInt($('empMinSal').value);
        var txtMinSal = parseInt($('txtMinSal').value);
        if(empMinSal && (empMinSal < txtMinSal)){
        	alert('Some Basic salaries are not in the specified range. -Min');
            return;
        }
        var empMaxSal = parseInt($('empMaxSal').value);
        var txtMaxSal = parseInt($('txtMaxSal').value);
        if(empMaxSal && (empMaxSal > txtMaxSal)){
            alert('Some Basic salaries are not in the specified range. -Max ');
            return;
        }

        var editAssBtn = $('editAssBtn');

        if(editAssBtn.title=='<?php echo $lang_Common_Save;?>') {
            editEXT();
            return;
        }

        var frm=document.frmSalCurDet;

        for (var i=0; i < frm.elements.length; i++) {
            frm.elements[i].disabled = false;
        }

        editAssBtn.className = "savebutton";
        editAssBtn.title = "<?php echo $lang_Common_Save;?>";
        editAssBtn.value = "<?php echo $lang_Common_Save;?>";
	}

	function editCurrency(currID) {

		location.href = document.frmSalCurDet.action + "&editID=" + currID;
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
    <div class="navigation">
    </div>
    <div class="outerbox">
        <div class="mainHeading"><h2><?php echo $lang_salarygrades_heading;?></h2></div>

<?php if (isset($messageText)) { ?>
<div class="messagebar">
	<span class="<?php echo $messageType; ?>"><?php echo $messageText; ?></span>
</div>
<?php } ?>

<form name="frmSalGrd" id="frmSalGrd" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&uniqcode=<?php echo $this->getArr['uniqcode']?>" onsubmit="return addUpdate();">
   <input type="hidden" name="token" value="<?php echo $token;?>" />
    <input type="hidden" name="sqlState" value=""/>
    <input type="hidden" name="backtype" value="<?php echo $backtype?>"/>
    <input type="hidden" name="referer" value="<?php echo $referer?>"/>

    <input type="hidden" name="txtSalGrdID" value="<?php echo $message[0][0]?>"/>
    <span class="formLabel"><?php echo $lang_Commn_code; ?></span>
    <span class="formValue"><?php echo $message[0][0]?></span>
    <br class="clear"/>

    <label for="txtSalGrdDesc"><?php echo $lang_compstruct_Name; ?><span class="required">*</span></label>
    <input type="text" name="txtSalGrdDesc" id="txtSalGrdDesc" size="40" tabindex="3" disabled="disabled"
        value="<?php echo $message[0][1]?>" class="formInputText"/>
    <br class="clear"/>

                <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="editbutton" id="editBtn"
                        onclick="edit();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        title="<?php echo $lang_Common_Edit;?>"
                        value="<?php echo $lang_Common_Edit;?>" />
                    <input type="reset" class="clearbutton" onmouseover="moverButton(this);"
                    	onmouseout="moutButton(this);" value="<?php echo $lang_Common_Reset;?>" />
                    <input type="button" class="savebutton"
                        onclick="goBack();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        value="<?php echo $lang_Common_Back;?>" />
<?php } ?>
                    <br class="clear"/>
                </div>
<br class="clear"/>
</form>

<form name="frmSalCurDet" id="frmSalCurDet" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $this->getArr['id']?>&amp;uniqcode=<?php echo $this->getArr['uniqcode']?>&amp;capturemode=updatemode">
   <input type="hidden" name="token" value="<?php echo $token;?>" />
	  			<input type="hidden" name="STAT"/>
				<input type="hidden" name="referer" value="<?php echo $referer?>"/>
				<input type="hidden" name="txtSalGrdID" value="<?php echo $this->getArr['id']?>"/>
<?php			if (!isset($this->getArr['editID'])) { ?>

<div class="subHeading"><h3><?php echo $lang_salarygrades_AssignNewCurrency; ?></h3></div>
<label for="cmbUnAssCurrency" ><?php echo $lang_hrEmpMain_currency; ?><span class="required">*</span></label>
<select <?php echo ($locRights['add']) ? '' : 'disabled'?> name="cmbUnAssCurrency" id="cmbUnAssCurrency" class="formSelect">
    <option value="0">---<?php echo "$lang_Leave_Common_Select $lang_hrEmpMain_currency"; ?>---</option>
<?php $unAssCurrency = $this->popArr['unAssCurrency'];
	for($c=0;$unAssCurrency && count($unAssCurrency)>$c;$c++)
		echo "<option value='" .$unAssCurrency[$c][0]. "'>" .$unAssCurrency[$c][1]. "</option>";
?>
</select>
<br class="clear"/>

<label for="txtMinSal" ><?php echo $lang_hrEmpMain_minpoint; ?></label>
<input type="text" <?php echo ($locRights['add']) ? '' : 'disabled'?> name="txtMinSal" id="txtMinSal"
    class="formInputText"/>
<br class="clear"/>

<label for="txtMaxSal" ><?php echo $lang_hrEmpMain_maxpoint; ?></label>
<input type="text" <?php echo ($locRights['add']) ? '' : 'disabled'?> name="txtMaxSal" id="txtMaxSal"
    class="formInputText"/>
<br class="clear"/>

<label for="txtStepSal" ><?php echo $lang_salarygrades_stepSal; ?></label>
<input type="text" <?php echo ($locRights['add']) ? '' : 'disabled'?> name="txtStepSal" id="txtStepSal"
    class="formInputText"/>
<br class="clear"/>

<div class="formbuttons">
<?php					if($locRights['add']) { ?>

<input type="button" class="savebutton" id="saveBtn"
    onclick="addEXT();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    title="<?php echo $lang_Common_Save;?>"
    value="<?php echo $lang_Common_Save;?>" />
</div>
<br class="clear"/>

<?php					}

			} elseif(isset($this->getArr['editID'])) {

				$editAssCurrency = $this->popArr['editAssCurrency'];
				?>



<div class="subHeading"><h3><?php echo $lang_salarygrades_EditAssignedCurrency; ?></h3></div>
<input type="hidden" name="cmbUnAssCurrency" value="<?php echo $editAssCurrency[0][1]?>"/>
<label for="cmbUnAssCurrency" ><?php echo $lang_hrEmpMain_currency; ?><span class="required">*</span></label>
<span class="formValue">
    <?php $assCurrency = $this->popArr['assCurrency'];
        for($c=0;$assCurrency && count($assCurrency)>$c;$c++)
            if($assCurrency[$c][0] == $editAssCurrency[0][1])
                echo $assCurrency[$c][1];
    ?>
</span>
<br class="clear"/>

<label for="txtMinSal" ><?php echo $lang_hrEmpMain_minpoint; ?></label>
<input type="text" disabled="disabled" name="txtMinSal" id="txtMinSal"
    class="formInputText" value="<?php echo $common_func->formatSciNO($editAssCurrency[0][2]);?>"/>
<input type="hidden" name="empMinSal" id="empMinSal" value="<?php echo $editAssCurrency[0][5]; ?>">
<br class="clear"/>

<label for="txtMaxSal" ><?php echo $lang_hrEmpMain_maxpoint; ?></label>
<input type="text" disabled="disabled"  name="txtMaxSal" id="txtMaxSal"
    class="formInputText" value="<?php echo $common_func->formatSciNO($editAssCurrency[0][3]);?>"/>
<input type="hidden" name="empMaxSal" id="empMaxSal" value="<?php echo $editAssCurrency[0][6]; ?>">
<br class="clear"/>

<label for="txtStepSal" ><?php echo $lang_salarygrades_stepSal; ?></label>
<input type="text" disabled="disabled"  name="txtStepSal" id="txtStepSal"
    class="formInputText" value="<?php echo $common_func->formatSciNO($editAssCurrency[0][4]);?>"/>
<br class="clear"/>

                <div class="formbuttons">
<?php if($locRights['edit']) { ?>
                    <input type="button" class="editbutton" id="editAssBtn"
                        onclick="editAss();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                        title="<?php echo $lang_Common_Edit;?>"
                        value="<?php echo $lang_Common_Edit;?>" />
<?php } ?>
                    <br class="clear"/>
                </div>
<br class="clear"/>

<?php } ?>

<?php
  $assCurrency = $this->popArr['assCurrency'];
  if ($assCurrency) {
?>

<div class="subHeading"><h3><?php echo $lang_salarygrades_AssignedCurrencies; ?></h3></div>
    <div class="actionbar">
        <div class="actionbuttons">
<?php    if($locRights['delete']) { ?>
            <input type="button" class="delbutton"
                onclick="delEXT();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Delete;?>" />
        <?php
            }
        ?>
        </div>
        <div class="noresultsbar"></div>
        <div class="pagingbar"></div>
    </div>
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
		<?php } ?>
    </form>
</div>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</div>
</body>

</html>
<?php } ?>
