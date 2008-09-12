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


$_SESSION['moduleType'] = 'rep';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/plugins/PlugInFactoryException.php';
require_once ROOT_PATH . '/plugins/PlugInFactory.php';
$sysConst = new sysConf();
$locRights=$_SESSION['localRights'];
$headingInfo=$this->popArr['headinginfo'];
$currentPage = $this->popArr['currentPage'];
$message= $this->popArr['message'];
$themeDir = '../../themes/' . $styleSheet;
// Check csv plugin available 
$PlugInObj = PlugInFactory::factory("CSVREPORT");
 
if(is_object($PlugInObj) && $PlugInObj->checkAuthorizeLoginUser(authorize::AUTHORIZE_ROLE_ADMIN) && $PlugInObj->checkAuthorizeModule( $_SESSION['moduleType'])){
	$csvExportRepotsPluginAvailable = true;
}


	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];

	$headingInfo=$this->popArr['headinginfo'];

	$currentPage = $this->popArr['currentPage'];

	$message= $this->popArr['message'];
	

	$themeDir = '../../themes/' . $styleSheet;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="../../themes/<?php echo $styleSheet; ?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet; ?>/css/style.css"); </style>
<style type="text/css">

    .roundbox {
        margin-top: 10px;
        margin-left: 0px;
        width: 98%;
    }

    .roundbox_content {
        padding:15px;
    }

</style>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../scripts/octopus.js"></script>
</head>
<script  language="javascript" type="text/javascript">

	function nextPage() {
		var i=eval(document.standardView.pageNO.value);
		document.standardView.pageNO.value=i+1;
		document.standardView.submit();
	}

	function prevPage() {
		var i=eval(document.standardView.pageNO.value);
		document.standardView.pageNO.value=i-1;
		document.standardView.submit();
	}

	function chgPage(pNO) {
		document.standardView.pageNO.value=pNO;
		document.standardView.submit();
	}

<?php	if($headingInfo[2] == '1') { ?>

	function returnAdd() {

		location.href = "./CentralController.php?repcode=<?php echo $this->getArr['repcode']?>&capturemode=addmode";
	}

<?php  }	?>

	function returnDelete() {
		$check = 0;
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					$check = 1;
				}
			}
		}

		if ( $check == 1 ){

			document.standardView.delState.value = 'DeleteMode';
			document.standardView.pageNO.value=1;
			document.standardView.submit();
		} else {
			alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
		}
	}

	function returnSearch() {
		document.standardView.captureState.value = 'SearchMode';
		document.standardView.pageNO.value=1;
		document.standardView.submit();
	}

	function doHandleAll()
	{
		with (document.standardView) {
			if(elements['allCheck'].checked == false){
				doUnCheckAll();
			}
			else if(elements['allCheck'].checked == true){
				doCheckAll();
			}
		}
	}

	function doCheckAll()
	{
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = true;
				}
			}
		}
	}

	function doUnCheckAll()
	{
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox') {
					elements[i].checked = false;
				}
			}
		}
	}

	function clear_form() {
		document.standardView.loc_code.options[0].selected=true;
		document.standardView.loc_name.value='';
	}
	
	function exportData(repcode) {
		var url = "../../plugins/csv/CSVController.php?uniqcode=CSE&download=1&path=<?php echo addslashes(ROOT_PATH) ?>&moduleType=<?php echo  $_SESSION['moduleType'] ?>&repcode=" +  repcode + "&obj=<?php  echo   base64_encode(serialize($PlugInObj))?>";
	  window.location = url;
	}
</script>
<body>
<form name="standardView" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?repcode=<?php echo $this->getArr['repcode']?>&VIEW=MAIN">
	<div class="moduleTitle" style="padding: 6px;"><h2><?php echo $heading[3]; ?></h2></div>
	<div>
		<input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>">
		<input type="hidden" name="delState" value="">
		<input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>">

		<div style="padding: 6px;">
			<?php	
				if($locRights['add'] && $headingInfo[2] == 1) { ?>
		        <img 
					title="Add" 
					alt="Add" 
					src="<?php echo $themeDir; ?>/pictures/btn_add.gif"
					style="border: none;"
					onclick="returnAdd();" 
					onmouseout="this.src='<?php echo $themeDir; ?>/pictures/btn_add.gif';" 
					onmouseover="this.src='<?php echo $themeDir; ?>/pictures/btn_add_02.gif';" />
			<?php	
				}
				if($locRights['delete'] && $headingInfo[2] == 1 && isset($message) && $message != '') { 
			?>
       			<img 
					title="Delete"
					alt="Delete" 
					src="<?php echo $themeDir; ?>/pictures/btn_delete.gif"
					style="border: none" 
					onclick="returnDelete();" 
					onmouseout="this.src='<?php echo $themeDir; ?>/pictures/btn_delete.gif';" 
					onmouseover="this.src='<?php echo $themeDir; ?>/pictures/btn_delete_02.gif';" />
			<?php } ?>
		</div>
	</div>

	<?php 
	/* Show tables only if records are available: Begins */
	if (isset($message) && $message != '') {
	?>
		<div style="width: 98%">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="22%" nowrap><h3><?php echo $search; ?></h3></td>
					<td width='78%' align="right">
						<?php
							if (isset($this->getArr['message'])) {
							
								$expString  = $this->getArr['message'];
								$col_def = CommonFunctions::getCssClassForMessage($expString);
						?>
								<span class="<?php echo $col_def?>" style="font-family: Verdana, Arial, Helvetica, sans-serif;"><?php echo $$expString; ?></span>
						<?php
							}
						?>
						&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
		</tr>
	</table>
	</div>
	
	<div class="roundbox">
		<table  border="0" cellpadding="5" cellspacing="0" class="">
			<tr>
				<td width="200" class="dataLabel">
					<label for="loc_code" style="float: left; padding-right: 10px;"><?php echo $searchby?></label>
					<select style="z-index: 99;" name="loc_code">
					<?php 
						for($c=0;count($srchlist[0])>$c;$c++) {
							if(isset($this->postArr['loc_code']) && $this->postArr['loc_code']==$srchlist[0][$c]) {
								echo "<option selected value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
							} else {
								echo "<option value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
							}
						}
					?>
					</select>
				</td>
				<td width="200" class="dataLabel" nowrap="nowrap">
					<label for="loc_name" ><?php echo $searchfor; ?></label>
					<input type="text" size="20" name="loc_name" class="dataField"  value="<?php echo isset($this->postArr['loc_name'])? stripslashes($this->postArr['loc_name']):''?>" />
				</td>
				<td align="right" width="180" class="dataLabel">
					<img 
						title="Search" 
						alt="Search" 
						src="<?php echo $themeDir; ?>/pictures/btn_search.gif" 
						onclick="returnSearch();" 
						onmouseover="this.src='<?php echo $themeDir; ?>/pictures/btn_search_02.gif';" 
						onmouseout="this.src='<?php echo $themeDir; ?>/pictures/btn_search.gif';" />
						
					<img 
						title="Clear" 
						alt="Clear" 
						src="<?php echo $themeDir; ?>/pictures/btn_clear.gif" 
						onclick="clear_form();" 
						onmouseover="this.src='<?php echo $themeDir; ?>/pictures/btn_clear_02.gif';" 
						onmouseout="this.src='<?php echo $themeDir; ?>/pictures/btn_clear.gif';" />
				</td>
			</tr>
		</table>
	</div>

	<div style="padding-top: 4px; width: 98%">
		<span id="messageDisplay">
			<?php
			if (empty($message)) { 
					echo $dispMessage; 
			} 
			?>&nbsp;
		</span>
	</div>
	
	<div style="text-align: right; padding-top: 4px; width: 98%">
		<?php
		$temp = $this->popArr['temp'];
		$commonFunc = new CommonFunctions();
		$pageStr = $commonFunc->printPageLinks($temp, $currentPage);
		$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);
		
		echo $pageStr;
		?>&nbsp;
	</div>
	
	<div class="roundbox">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
			<tr>
				<td width="50" NOWRAP class="listViewThS1" scope="col">
				<?php if($headingInfo[2]==1) { ?>
					<input type='checkbox' class='checkbox' name='allCheck' value='' onclick="doHandleAll();">
				<?php	} else {	?>
					&nbsp;
				<?php }?>
				</td>
				<td scope="col" width="250" class="listViewThS1"><?php echo $heading[0]?></td>
				<td scope="col" width="400" class="listViewThS1"><?php echo $heading[1]?></td>
			    <td scope="col" width="400" class="listViewThS1">&nbsp;</td>
			</tr>
		<?php
		if ((isset($message)) && ($message !='')) {
		
			for ($j=0; $j<count($message);$j++) {
			
				$descField=$message[$j][1];
			
				if($sysConst->viewDescLen <= strlen($descField)) {
					$descField = substr($descField,0,$sysConst->viewDescLen);
					$descField .= "....";
				}
				
				$cssClass = ($j%2) ? 'odd' : 'even';
		?>
				<tr>
					<td class="<?php echo $cssClass; ?>" width="50">
					<?php if($headingInfo[2]==1) { ?>
						<input type='checkbox' class='checkbox' name='chkLocID[]' value='<?php echo $message[$j][0]?>' />
					<?php } else { ?>
						&nbsp;
					<?php } ?>
					</td>
					<td class="<?php echo $cssClass; ?>" width="250">
						<a 
							href="./CentralController.php?id=<?php echo $message[$j][0]?>&repcode=<?php echo $this->getArr['repcode']?>&capturemode=updatemode" 
							class="listViewTdLinkS1">
								<?php echo $message[$j][0]?>						</a>					</td>
					<td class="<?php echo $cssClass; ?>" width="400" >
						<?php echo $descField?>					</td>
				    <td class="<?php echo $cssClass; ?>" width="400" ><?php if(trim($_GET['repcode'])  == 'EMPVIEW'   && isset($csvExportRepotsPluginAvailable))  {?><input type="button" class="button" id="btnExport" value="<?php echo $lang_DataExport_Export?>"
	        	title="<?php echo $lang_DataExport_Export?>" name="btnExport" onclick="exportData('<?php echo $message[$j][0]?>')" />
				<?php } ?>
				</td>
				</tr>
		<?php 
			}
		} 
		?>
		</table>
	</div>
	
	<?php /* Show tables only if records are available: Ends */
	} else {
		echo "<h5>$lang_empview_norecorddisplay</h5>";
	}
	?>
</form>

<script type="text/javascript">
<!--
   	if (document.getElementById && document.createElement) {
		initOctopus();
	}
-->
</script>

</body>
</html>
