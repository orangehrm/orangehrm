<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
 * all the essential functionalities required for any enterprise. 
 * Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com
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
	
	$types = array(array($lang_compstruct_Division, $lang_compstruct_Division), array($lang_compstruct_Department, $lang_compstruct_Department), array($lang_compstruct_Team, $lang_compstruct_Team), array($lang_compstruct_Other, $lang_compstruct_Other));


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

		$objResponse = $xajaxFiller->cmbFiller($objResponse,$getLoc,0,'frmAddNode','cmbLocation',3);

		$objResponse->addScript("document.getElementById('layerFormLoc').style.visibility='hidden';");
		
		$objResponse->addScript("document.getElementById('cmbLocation').options[document.getElementById('cmbLocation').options.length] = new Option('Other', 'Other');");

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
		if ($provlist) {
			$objResponse->addAssign('lrState','innerHTML','<select name="txtState" id="txtState"><option value="0">--- Select ---</option></select>');
			$objResponse = $xajaxFiller->cmbFillerById($objResponse,$provlist,1,'lrState','txtState');
		
		} else {
			$objResponse->addAssign('lrState','innerHTML','<input type="text" name="txtState" id="txtState" value="">');
		}
		$objResponse->addScript('document.getElementById("txtState").focus();');
	
		$objResponse->addScript("document.frmLocation.txtDistrict.options.length = 1;");
		$objResponse->addAssign('status','innerHTML','');
	
	
	
	return $objResponse->getXML();
	}

	

?>

<html>

<head>
<title>Company Structure</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../../themes/beyondT/pictures/styles.css" rel="stylesheet" type="text/css">
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<link href="../../themes/beyondT/css/compstruct.css" rel="stylesheet" type="text/css">
</head>

<?php 
	require(ROOT_PATH.'/scripts/archive.js'); 
	$objAjax->printJavascript(); 
?>  

<script language="JavaScript" type="text/javascript">

<?php require_once(ROOT_PATH.'/scripts/SCRIPT_compstruct.js'); ?>

</script>
<body style="padding-left:5px;">

 	<div id="layerComStruct">
	<h2><?php echo $lang_compstruct_heading; ?></h2>
	<br>
	<?php if ($_GET['root'] === '') { ?>
	<div class="err"><?php echo 'Please define Company General Information first!'?></div>
	<?php } else { ?>
	<table id="tblCompStruct" border="0" cellspacing="0" cellpadding="0" style="BORDER-COLLAPSE: collapse" bordercolor="#111111">	

	<?php

		$treeHierarchy = $treeCompStruct->displayTree($_GET['root']);
		$depth=(($treeHierarchy[0][0]['rgt']-$treeHierarchy[0][0]['lft']+1)/2);
		unset($indentor);		

	if ($treeHierarchy) {			

		foreach ($treeHierarchy as $child) {
	?>
		<tr>
			<td valign="middle">
			<?php 
				if ( $child['depth'] > 0 ) {

					if ($child['isLast']) {

						$indentor[$child['depth']]="<image src='../../themes/beyondT/icons/space.gif'>";

					} else {

						$indentor[$child['depth']]="<image src='../../themes/beyondT/icons/space.gif' id='line'>";

					}
					for ($i=1; $i<$child['depth']; $i++) {

						echo $indentor[$i];

					}

					//echo str_repeat("|<image src='space.png'>",($child['depth']-1));

					echo "<image src='../../themes/beyondT/icons/arrow.gif'>";
			?>				

			<a class="title" href="#layerForm" onClick="edit(<?php echo $child[0]['id']?>, '<?php echo escapeshellcmd($child[0]['title'])?>', '<?php echo escapeshellcmd($child[0]['description'])?>', '<?php echo $child[0]['loc_code']?>');"><?php echo $child[0]['title']?></a>

			<?php

				} else {

					echo $child[0]['title'];

				} 
			?>
			</td>
			<?php if (!(isset($_GET['esp']) && ($_GET['esp'] == 1))) { ?>
			<td id="ControlButton" valign="bottom">
				<a href='#layerForm' class="add" onClick="addChild(<?php echo $child[0]['rgt']; ?>, '<?php echo escapeshellcmd($child[0]['title'])?>', <?php echo $child[0]['id']; ?>, '<?php echo $child[0]['loc_code']?>')"><?php echo $lang_compstruct_add; ?></a>
			</td>

			<td valign="bottom">
			<?php if ( $child['depth'] > 0 ) {?>

			| </td>
			
			<td id="ControlButton" valign="bottom">
					<a class="delete" href="#" onClick="deleteChild(<?php echo $child[0]['lft']; ?>, <?php echo $child[0]['rgt']; ?>, '<?php echo escapeshellcmd($child[0]['title'])?>');"><?php echo $lang_compstruct_delete; ?></a>

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
    <form name="frmDeleteNode" id="frmDeleteNode" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>" onSubmit="validate(); return false;">
		<input type="hidden" value="" id="rgt" name="rgt">
		<input type="hidden" value="" id="lft" name="lft">
		<input type="hidden" value="" id="sqlState" name="sqlState">		
	</form>
	<!-- End Delete Subdivision -->
	

    <!-- Add Subdivision  -->

	<div id="layerForm"  class="frame">
		<h3><LABEL id="parnt"></LABEL></h3>

		<form name="frmAddNode" id="frmAddNode" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&id=1" onSubmit="validate(); return false;">

		<input type="hidden" value="" id="rgt" name="rgt">
		<input type="hidden" value="" id="sqlState" name="sqlState">

		<input type="hidden" value="" id="txtParnt" name="txtParnt">

		<table>
			<tr>
				<td valign="top">
					<LABEL id="lblSubDivision" for="txtTitle"><span class="error">*</span> <?php echo $lang_compstruct_Name; ?></LABEL>
				</td>
				<td>
					<input type="text" value="" id="txtTitle" name="txtTitle" >
				</td>
			</tr>
			<tr>
				<td valign="top">
					<LABEL id="lblType" for="cmbType"><span class="error">*</span> <?php echo $lang_compstruct_Type; ?></LABEL>
				</td>
				<td>
  					<select name="cmbType" id="cmbType">
    					<option value="null"><?php echo $lang_Leave_Common_Select; ?></option>
    					<?php foreach ($types as $typex) { ?>
    						<?php vprintf('<option value="%s">%s</option>', $typex);?>
    					<?php } ?>
  					</select>
  				</td>
  			</tr>
  			<tr>
				<td valign="top">
					<LABEL id="lblLocation" for="cmbLocation"><span class="error">*</span> <?php echo $lang_compstruct_Location?></LABEL>
				</td>
				<td>
  					<select name="cmbLocation" id="cmbLocation" onChange="locChange(this);">
  						<option value=""><?php echo $lang_Leave_Common_Select; ?></option>
  						<?php
						  if ($locations) {
							foreach ($locations as $location) { ?>
  						<option value="<?php echo $location[0]; ?>"><?php echo $location[1]; ?></option>
  						<?php	}
						  } ?>
    					<option value="Other"><?php echo $lang_compstruct_Other; ?></option>
  					</select>
  				</td>
  			</tr>
  			<tr>
				<td valign="top">
					<LABEL id="lblDesc" for="txtDesc"><?php echo $lang_compstruct_Description; ?></LABEL>
				</td>
				<td>	  					
  					<textarea name="txtDesc" id="txtDesc"></textarea>  
  				</td>
  			</tr>
			<tr>
				<td></td>
				<td align="right">
					<input type="Submit" value="<?php echo $lang_compstruct_save; ?>" id="Add" name="Add" class="btnAdd">
					<input type="Reset" value="<?php echo $lang_compstruct_clear; ?>" id="Clear" name="Clear">
					<input type="Reset" value="<?php echo $lang_compstruct_hide; ?>" id="Hide" name="Hide" onClick="frmAddHide()">
				</td>
			</tr>
		</table>
	</form>	

	<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>

	<!-- Add Location  -->	

	<div id="layerFormLoc"  name="layerFormLoc" class="frame">
		<h3><?php echo $lang_compstruct_frmNewLocation; ?></h3>&nbsp;<span id="status"><image src='../../themes/beyondT/icons/loading.gif' width='20' height='20' style="vertical-align: bottom;"></span>
		<form id="frmAddLoc" name="frmAddLoc" method="post" onSubmit="return false;">
		<table>
			<tr>
				<td><span class="error">*</span> <?php echo $lang_compstruct_Name; ?></td>
				<td> <input name="txtLocDescription" id="txtLocDescription"></td>
			<tr>
  				<td><span class="error">*</span> <?php echo $lang_compstruct_country; ?></td>
				<td><select name="cmbCountry" onChange="swStatus(); xajax_populateStates(this.value);"> 						<option value="0"><?php echo $lang_Leave_Common_Select; ?></option>
						<?php
							$cntlist = $this->popArr['countries'];
								for($c=0; $cntlist && count($cntlist)>$c ;$c++) 
							    	echo "<option value='" . $cntlist[$c][0] . "'>" . $cntlist[$c][1] . "</option>";
						?>
					</select>
				</td>
			</tr>						
			<tr>
				<td><span class="error">*</span> <?php echo $lang_compstruct_Address?></td>
				<td><textarea name="txtAddress"></textarea></td>
			</tr>
			<tr>
				<td><?php echo $lang_compstruct_city?></td>
				<td><input type="text" name="cmbDistrict"></td>
			</tr>
			<tr>
				<td><?php echo $lang_compstruct_state?></td>
				<td><div id="lrState" name="lrState" ><input type="text" name="txtState" id="txtState"></div>
					 <input type="hidden" name="cmbProvince" id="cmbProvince">
				</td>
			</tr>
			<tr>
				<td><span class="error">*</span> <?php echo $lang_compstruct_ZIP_Code?></td>
				<td><input type="text" name="txtZIP"></td>
			</tr>
			<tr>
				<td><?php echo $lang_compstruct_Phone; ?></td>
				<td><input type="text" name="txtPhone"></td>
			</tr>
			<tr>
				<td><?php echo $lang_comphire_fax; ?></td>
				<td><input type="text" name="txtFax"></td>
			</tr>
			<tr valign="top">
				<td><?php echo $lang_Leave_Common_Comments; ?></td>
				<td><textarea name="txtComments"></textarea></td>
			</tr>
			<tr>
				<td align="right">
					<input type="button" value="<?php echo  $lang_compstruct_save; ?>" id="Add" name="Add" class="btnAdd" onClick="addNewLocation ();">
					<input type="Reset" value="<?php echo  $lang_compstruct_clear; ?>" id="Clear" name="Clear" onClick="resetx()">
				</td>
			</tr>
		</table>
	</form>
	</div>
	</div>
	<?php } ?>
</body>
</html>