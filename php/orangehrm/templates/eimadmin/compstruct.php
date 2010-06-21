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
 * @ author : Mohanjith <mohanjith@beyondm.net>, <moha@mohanjith.net>
 *
 */

	require_once ROOT_PATH . '/lib/dao/DMLFunctions.php';
	require_once ROOT_PATH . '/lib/controllers/ViewController.php';
	require_once ROOT_PATH . '/lib/confs/sysConf.php';
	require_once($lan->getLangPath("full.php"));

	$GLOBALS['lang_Common_Select'] = $lang_Common_Select;
	$GLOBALS['lang_compstruct_Other'] = $lang_compstruct_Other;

	$types = array(array($lang_compstruct_Division, $lang_compstruct_Division), array($lang_compstruct_Department, $lang_compstruct_Department), array($lang_compstruct_Team, $lang_compstruct_Team), array('Other', $lang_compstruct_Other));

	$dbConnection = new DMLFunctions();

	$message2 = $dbConnection -> executeQuery("SELECT geninfo_values FROM `hs_hr_geninfo` WHERE code = 001");

 	$arrCompInfo = mysql_fetch_array($message2, MYSQL_NUM);

 	$txtCompInfo=explode("|", $arrCompInfo[0]);

	if (!isset($_GET['root']) ) {

		$_GET['root']=$txtCompInfo[0];

	};

	$locations = $this->popArr['locations'];

	$treeCompStruct = new CompStruct();

	$objAjax = new xajax();

	$objAjax->registerFunction('addLocation');
	$objAjax->registerFunction('populateStates');

	$objAjax->processRequests();

	function addLocation($arrElements) {

		$view_controller = new ViewController();
		$ext_locAdd = new EXTRACTOR_Location();

		$objAddLoc = $ext_locAdd->parseAddData($arrElements);

		$view_controller -> addData('LOC',$objAddLoc, true);

		$getLoc = $view_controller->xajaxObjCall('', 'LOC','getLocCodes');

		$objResponse = new xajaxResponse();

		$xajaxFiller = new xajaxElementFiller();
		$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
		$objResponse = $xajaxFiller->cmbFiller($objResponse,$getLoc,0,'frmAddNode','cmbLocation',3);

		$objResponse->addScript("document.getElementById('layerFormLoc').style.visibility='hidden';");

		$objResponse->addScript("document.getElementById('cmbLocation').options[document.getElementById('cmbLocation').options.length] = new Option('".$GLOBALS['lang_compstruct_Other']."', 'Other');");

		$objResponse->addScript("document.getElementById('cmbLocation').selectedIndex = document.getElementById('cmbLocation').options.length-2;");

		$objResponse->addScript("document.getElementById('frmAddNode').focus();");

		$objResponse->addAssign('status','innerHTML','');

	return $objResponse->getXML();
	}

	function populateStates($value) {

		$view_controller = new ViewController();
		$provlist = $view_controller->xajaxObjCall($value,'LOC','province');

		$objResponse = new xajaxResponse();
		$xajaxFiller = new xajaxElementFiller();
		$xajaxFiller->setDefaultOptionName($GLOBALS['lang_Common_Select']);
		if ($provlist) {
			$objResponse->addAssign('lrState','innerHTML','<select name="txtState" id="txtState" class="formSelect"><option value="0">--- '.$GLOBALS['lang_Common_Select'].' ---</option></select>');
			$objResponse = $xajaxFiller->cmbFillerById($objResponse,$provlist,1,'lrState','txtState');

		} else {
			$objResponse->addAssign('lrState','innerHTML','<input type="text" name="txtState" id="txtState" value="" class="formInputText"/>');
		}
		$objResponse->addScript('document.getElementById("txtState").focus();');

		$objResponse->addScript("document.frmLocation.txtDistrict.options.length = 1;");
		$objResponse->addAssign('status','innerHTML','');



	return $objResponse->getXML();
	}

    $treeHierarchy = $treeCompStruct->displayTree($_GET['root']);
    $depth=(($treeHierarchy[0][0]['rgt']-$treeHierarchy[0][0]['lft']+1)/2);
    unset($indentor);

    $token = $this->popArr['token'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="../../themes/<?php echo $styleSheet;?>/css/compstruct.css" rel="stylesheet" type="text/css"/>

    <script type="text/javascript" src="../../scripts/archive.js"></script>
    <?php $objAjax->printJavascript();?>

    <script type="text/javascript">
    //<![CDATA[
        <?php require_once(ROOT_PATH.'/scripts/SCRIPT_compstruct.js'); ?>
        var allChildDepIds= new Array();
        var allChildIds = new Array();
		var currentEditNodeValues = ['', '', '', '', ''];

<?php if ($treeHierarchy) { ?>
<?php
    $cnt=0;
    foreach ($treeHierarchy as $child) {
        $dept_id=$child[0]['dept_id']==''?"'';\n":'\''.$child[0]['dept_id']."';\n";
        $id = $child[0]['id'];
        echo("allChildDepIds[$cnt]=".$dept_id);
        echo("allChildIds[$cnt] = " . $id . "\n");
        $cnt++;
    }
?>
<?php } ?>

	function resetAddNodeForm() {
		$('frmAddNode').reset();
		if (currentEditNodeValues[1] != '') {
			edit(currentEditNodeValues[0], currentEditNodeValues[1], currentEditNodeValues[2], currentEditNodeValues[3], currentEditNodeValues[4]);
		}
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

<body style="padding-left:5px;">

 	<div id="layerComStruct">

	<h2><?php echo $lang_compstruct_heading; ?></h2>
	<br />
	<?php if ($_GET['root'] === '') { ?>
	<div class="err"><?php echo $lang_Error_Company_General_Undefined; ?></div>
	<?php } else { ?>
	<table id="tblCompStruct" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse">

    <?php
	if ($treeHierarchy) {
		foreach ($treeHierarchy as $child) {
	?>
		<tr>
			<td valign="middle">
			<?php
				if ( $child['depth'] > 0 ) {

					if ($child['isLast']) {

						$indentor[$child['depth']]="<img src='../../themes/beyondT/icons/space.gif' alt=\"\"/>";

					} else {

						$indentor[$child['depth']]="<img src='../../themes/beyondT/icons/space.gif' id='line' alt=\"\"/>";

					}
					for ($i=1; $i<$child['depth']; $i++) {

						echo $indentor[$i];

					}

					//echo str_repeat("|<img src='space.png'>",($child['depth']-1));

					echo "<img src='../../themes/beyondT/icons/arrow.gif' alt=\"\"/>";
			?>
			<a class="title" href="#layerForm"
				onclick="edit(<?php echo $child[0]['dept_id']==''?'\'\'':'\''.$child[0]['dept_id'].'\''?>,<?php echo $child[0]['id']?>, '<?php echo escapeshellcmd($child[0]['title'])?>', '<?php echo CommonFunctions::escapeForJavascript(escapeshellcmd($child[0]['description']))?>', '<?php echo $child[0]['loc_code']?>');"><?php echo $child[0]['dept_id']." ".preg_replace('/'.$lang_compstruct_Other.'$|Other$/', '',$child[0]['title']); ?></a>
			<?php

				} else {
					// If in the popup window, allow top level (company) to be selected as well.
					if (isset($_GET['esp']) && $_GET['esp'] == 1) {
                                        ?>
						<a class="title" href="#layerForm"
							onclick="edit(<?php echo $child[0]['dept_id']==''?'\'\'':'\''.$child[0]['dept_id'].'\''?>,<?php echo $child[0]['id']?>, '<?php echo escapeshellcmd($child[0]['title'])?>', '<?php echo CommonFunctions::escapeForJavascript(escapeshellcmd($child[0]['description']))?>', '<?php echo $child[0]['loc_code']?>');"><?php echo preg_replace('/'.$lang_compstruct_Other.'$|Other$/', '', $child[0]['title']); ?></a>

                                        <?php
					} else {
						echo  $child[0]['dept_id']." ".$child[0]['title'];
					}

				}
			?>
			</td>
			<?php if (!(isset($_GET['esp']) && ($_GET['esp'] == 1))) { ?>
			<td valign="bottom">
				<a href='#layerForm' class="add" onclick="addChild(<?php echo $child[0]['rgt']; ?>, '<?php echo escapeshellcmd($child[0]['title'])?>', <?php echo $child[0]['id']; ?>, '<?php echo $child[0]['loc_code']?>')"><?php echo $lang_compstruct_add; ?></a>
			</td>

			<td valign="bottom">
			<?php if ( $child['depth'] > 0 ) {?>

			| </td>
			<td valign="bottom">
					<a class="delete" href="#" onclick="deleteChild(<?php echo $child[0]['lft']; ?>, <?php echo $child[0]['rgt']; ?>, '<?php echo escapeshellcmd($child[0]['title'])?>');"><?php echo $lang_compstruct_delete; ?></a>

			<?php } ?>
			</td>
			<?php } ?>
		</tr>
		<?php
			}
		} else {
		?>
	<p class='ERR'><?php echo $lang_compstruct_no_root; ?></p>
	<?php } ?>
	</table>
    </div>

    <!-- Delete Subdivision -->
    <form name="frmDeleteNode" id="frmDeleteNode" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>" onsubmit="validate(); return false;">
      
		<input type="hidden" value="" id="del_rgt" name="rgt"/>
		<input type="hidden" value="" id="lft" name="lft"/>
		<input type="hidden" value="" name="sqlState"/>
	</form>
	<!-- End Delete Subdivision -->

    <!-- Add Subdivision  -->
	<div id="layerForm"  class="frame" style="margin-right:10px;width:420px;">
        <div class="outerbox">
        <div class="subHeading"><h3 id="parnt">&nbsp;</h3></div>
		<form name="frmAddNode" id="frmAddNode" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&amp;id=1" onsubmit="validate(); return false;">
         <input type="hidden" value="<?php echo $token;?>" name="token" />
		<input type="hidden" value="" id="add_rgt" name="rgt"/>
		<input type="hidden" value=""  name="sqlState"/>
		<input type="hidden" value="" id="txtParnt" name="txtParnt"/>

    	<label id="lblDeptId" for="txtDeptId"><?php echo $lang_compstruct_Dept_Id; ?></label>
        <input type="text" value="" id="txtDeptId" name="txtDeptId" class="formInputText"/>
        <br class="clear" />

        <label id="lblSubDivision" for="txtTitle"><?php echo $lang_compstruct_Name; ?><span class="required">*</span></label>
        <input type="text" value="" id="txtTitle" name="txtTitle" class="formInputText"/>
        <br class="clear" />

        <label id="lblType" for="cmbType"><?php echo $lang_compstruct_Type; ?><span class="required">*</span></label>
		<select name="cmbType" id="cmbType" class="formSelect">
            <option value="null"><?php echo $lang_Leave_Common_Select; ?></option>
			<?php foreach ($types as $typex) { ?>
				<?php vprintf('<option value="%s">%s</option>', $typex);?>
			<?php } ?>
		</select>
        <br class="clear" />

  		<label id="lblLocation" for="cmbLocation"> <?php echo $lang_compstruct_Location?></label>
        <select name="cmbLocation" id="cmbLocation" onchange="locChange(this);" class="formSelect">
            <option value=""><?php echo $lang_Leave_Common_Select; ?></option>
			<?php
			  if ($locations) {
				foreach ($locations as $location) { ?>
			<option value="<?php echo $location[0]; ?>"><?php echo $location[1]; ?></option>
			<?php	}
			  } ?>
			<option value="Other"><?php echo $lang_compstruct_Other; ?></option>
		</select>
        <br class="clear" />

        <label id="lblDesc" for="txtDesc"><?php echo $lang_compstruct_Description; ?></label>
        <textarea name="txtDesc" id="txtDesc" rows="3" cols="25" class="formTextArea"></textarea>
        <br class="clear" />

        <div class="formbuttons">
            <input type="button" class="savebutton"
                onclick="validate();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                value="<?php echo $lang_Common_Save;?>" />
            <input type="button" class="clearbutton" onclick="resetAddNodeForm();"
                onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                 value="<?php echo $lang_Common_Reset;?>" />
			<input type="button" class="savebutton" onclick="frmAddHide();" value="<?php echo $lang_compstruct_hide;?>" />
		</div>
	</form>
    <br class="clear" />
    </div>
    <div class="requirednotice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</div>

	<!-- Add Location  -->

	<div id="layerFormLoc" class="frame">
        <div class="outerbox">
		<div class="subHeading"><h3><?php echo $lang_compstruct_frmNewLocation; ?></h3></div>

        <span id="status"><img src='../../themes/beyondT/icons/loading.gif' width='20' height='20' style="vertical-align: bottom;" alt=""/></span>


		<form id="frmAddLoc" name="frmAddLoc" method="post" onsubmit="return false;" action="">

            <label for="txtLocDescription"><?php echo $lang_compstruct_Name; ?><span class="required">*</span></label>
            <input name="txtLocDescription" id="txtLocDescription" class="formInputText"/>
            <br class="clear" />

            <label for="cmbCountry"><?php echo $lang_compstruct_country; ?><span class="required">*</span></label>
            <select name="cmbCountry" id="cmbCountry" onchange="swStatus(); xajax_populateStates(this.value);"
                    class="formSelect">
                <option value="0"><?php echo $lang_Leave_Common_Select; ?></option>
				<?php
					$cntlist = $this->popArr['countries'];
					for($c=0; $cntlist && count($cntlist)>$c ;$c++) {
                        echo "<option value='" . $cntlist[$c][0] . "'>" . $cntlist[$c][1] . "</option>";
                    }
				?>
			</select>
            <br class="clear" />

            <label for="txtState"><?php echo $lang_compstruct_state; ?></label>
            <div id="lrState" ><input type="text" name="txtState" id="txtState" class="formInputText"/></div>
            <input type="hidden" name="cmbProvince" id="cmbProvince"/>
            <br class="clear" />

            <label for="txtAddress"><?php echo $lang_compstruct_Address; ?><span class="required">*</span></label>
            <textarea name="txtAddress" id="txtAddress" rows="3" cols="25" class="formTextArea"></textarea>
            <br class="clear" />

            <label for="cmbDistrict"><?php echo $lang_compstruct_city; ?></label>
            <input type="text" name="cmbDistrict" id="cmbDistrict" class="formInputText"/>
            <br class="clear" />

            <label for="txtZIP"><?php echo $lang_compstruct_ZIP_Code; ?><span class="required">*</span></label>
            <input type="text" name="txtZIP" id="txtZIP" class="formInputText"/>
            <br class="clear" />

            <label for="txtPhone"><?php echo $lang_compstruct_Phone; ?></label>
            <input type="text" name="txtPhone" id="txtPhone" class="formInputText"/>
            <br class="clear" />

            <label for="txtPhone"><?php echo $lang_comphire_fax; ?></label>
			<input type="text" name="txtFax" id="txtFax" class="formInputText"/>
            <br class="clear" />

            <label for="txtPhone"><?php echo $lang_Leave_Common_Comments; ?></label>
            <textarea name="txtComments" rows="3" cols="25" class="formTextArea"></textarea>
            <br class="clear" />

             <div class="formbuttons">
                <input type="button" class="savebutton"
                    onclick="addNewLocation();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                    value="<?php echo $lang_Common_Save;?>" />
                <input type="button" class="clearbutton" onclick="resetx(); document.getElementById('frmAddLoc').reset();"
                    onmouseover="moverButton(this);" onmouseout="moutButton(this);"
                     value="<?php echo $lang_Common_Reset;?>" />
             </div>
             <br class="clear" />
	</form>
    </div>
	</div>
	</div>
	<?php } ?>
<script type="text/javascript">
//<![CDATA[
    if (document.getElementById && document.createElement) {
        roundBorder('outerbox');
    }
//]]>
</script>
</body>
</html>
