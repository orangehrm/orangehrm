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



require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/models/eimadmin/CompStruct.php';

	$sysConst = new sysConf();
	$locRights = $_SESSION['localRights'];

	//$headingInfo =$this->popArr['headinginfo'];

    $currentPage = $this->popArr['currentPage'];
	$emplist = $this->popArr['emplist'];

	if (!isset($this->getArr['sortField']) || ($this->getArr['sortField'] == '')) {
		$this->getArr['sortField']=4;
		$this->getArr['sortOrder4']='ASC';
	}

	function getNextSortOrder($curSortOrder) {
		switch ($curSortOrder) {
			case 'null' :
				return 'ASC';
				break;
			case 'ASC' :
				return 'DESC';
				break;
			case 'DESC'	:
				return 'ASC';
				break;
		}

	}

	function SortOrderInWords($SortOrder) {
		if ($SortOrder == 'ASC') {
			return 'Ascending';
		} else {
			return 'Descending';
		}
	}
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
</head>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script type="text/javascript">
	function nextPage() {
		i=document.standardView.pageNO.value;
		i++;
		document.standardView.pageNO.value=i;
		document.standardView.submit();
	}
	
	function prevPage() {
		var i=document.standardView.pageNO.value;
		i--;
		document.standardView.pageNO.value=i;
		document.standardView.submit();
	}
	
	function chgPage(pNO) {
		document.standardView.pageNO.value=pNO;
		document.standardView.submit();
	}
	
	function sortAndSearch(action) {
		document.standardView.action = action;
		document.standardView.submit();
	}
	
<?php if($this->getArr['reqcode']=='EMP') { ?>
	function returnAdd() {

		location.href = "./CentralController.php?reqcode=<?php echo $this->getArr['reqcode']?>&capturemode=addmode";

	}
	
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
		}else{
			alert("<?php echo $lang_Error_SelectAtLeastOneRecordToDelete; ?>");
		}
	}

<?php } else { ?>
	function returnAdd() {
        var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=<?php echo $this->getArr['reqcode']?>','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
	}
<?php } ?>

	function returnSearch() {
		if (document.standardView.loc_code.value == -1) {
			alert("<?php echo $lang_Common_SelectField; ?>");
			document.standardView.loc_code.Focus();
			return;
		};
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

	parent.scrollTo(0, 0);
</script>
<body style="padding-left:14px; padding-right: 14px;" onfocus="document.getElementById('content').focus()">
<div id="content">
	<form name="standardView" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN&sortField=<?php echo $this->getArr['sortField']; ?>&sortOrder<?php echo $this->getArr['sortField']; ?>=<?php echo $this->getArr['sortOrder'.$this->getArr['sortField']]?>">
		<div class="moduleTitle" style="padding: 6px;"><h2><?php echo $headingInfo[0]?></h2></div>
		<div>
			<input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>" />
			<input type="hidden" name="delState" value="" />
			<input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>" />
			<input type="hidden" name="empID" value="" />
			<div style="padding: 6px;">
				<img 
					title="Add" 
					alt="Add"
					src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_add.gif" 
					style="border: none;"
					onclick="<?php 
						if($locRights['add']) { 
							?>returnAdd();<?php 
						} else { 
							?>alert('<?php echo $lang_Common_AccessDenied;?>');<?php 
						} ?>"
					onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_add.gif';" 
					onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_add_02.gif';" />	
				<?php if($this->getArr['reqcode']=='EMP') { ?>
					<img 
						title="Delete" 
						alt="Delete" 
						src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_delete.gif" 
						onclick="<?php if($locRights['delete']) { ?>returnDelete();<?php } else { ?>alert('<?php echo $lang_Common_AccessDenied;?>');<?php } ?>" 
						onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_delete.gif';"
						onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_delete_02.gif';" />
				<?php } ?>
			</div>
		</div>
		<div>
			<div>
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
				  <tr>
					<td width="22%" nowrap="nowrap"><h3><?php echo $search?></h3></td>
					<td width="78%" align="right">
					<?php
						if (isset($this->getArr['message'])) {
				
							$expString  = $this->getArr['message'];
							$col_def = CommonFunctions::getCssClassForMessage($expString);
					?>
							<span class="<?php echo $col_def?>" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: smaller"><?php echo $$expString; ?></span>
					<?php
						}
					?>
					  &nbsp;&nbsp;&nbsp;&nbsp;</td>
				  </tr>
				</table>
			</div>
			<div class="roundbox">
				<table  border="0" cellpadding="5" cellspacing="0" class="">
					<tr style="white-space: nowrap">
						<td width="250" class="dataLabel" nowrap="nowrap">
							<label for="loc_code" style="float: left"><?php echo $searchby?></label>&nbsp;&nbsp;
							<select name="loc_code" id="loc_code">
							<?php
								 $optionCount = count($srchlist[0]);
	
								 /* Don't show the last option (search by supervisor) if user is a supervisor */
								 if ($_SESSION['isSupervisor']) {
									$optionCount--;
								 }
	
								 for ($c = 0; $optionCount > $c; $c++) {
									if (isset($this->postArr['loc_code']) && $this->postArr['loc_code']==$srchlist[0][$c]) {
									   echo "<option selected value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
									} else {
									   echo "<option value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
									}
								}
							?>
							</select>
						</td>
						<td width="230" class="dataLabel" nowrap="nowrap">
							<label for="loc_name" style="float: left"><?php echo $description?></label>&nbsp;&nbsp;
							<input type=text size="20" name="loc_name" class="dataField" value="<?php echo isset($this->postArr['loc_name'])? stripslashes($this->postArr['loc_name']):''?>" />
						</td>
						<td align="right" width="180" class="dataLabel">
							<img 
								title="Search" 
								alt="Search"
								src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_search.gif"
								onclick="returnSearch();" 
								onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_search.gif';" 
								onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_search_02.gif';" />
							&nbsp;&nbsp;
							<img 
								title="Clear" 
								alt="Clear" 
								src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_clear.gif"
								onclick="clear_form();" 
								onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_clear.gif';" 
								onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_clear_02.gif';" />
						</td>
					</table>
				</div>
				<div style="padding-top: 4px; width: 98%">
					 <span id="messageDisplay">
					<?php 
						if ((isset($emplist)) && ($emplist =='')) { 
							echo $norecorddisplay; 
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
		
					for ($j = 0; $j < 11; $j++) {
						if (!isset($this->getArr['sortOrder'.$j])) {
							$this->getArr['sortOrder'.$j] = 'null';
						}
					}					
				?>
			</div>
			<div class="roundbox">
				<table cellpadding="5" cellspacing="0" class="" style="width: 100%; border: none;">
					<tr>
						<td width="50">&nbsp;&nbsp;&nbsp;</td>
						<?php $j = 0; ?>
						<td scope="col" width="200" class="listViewThS1">
							<a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&amp;VIEW=MAIN&amp;sortField=<?php echo $j; ?>&amp;sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $employeeid; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="img" style="vertical-align: middle" />
						</td>
						<?php $j = 7; ?>
						<td scope="col" width="400" class="listViewThS1">
							<a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&amp;VIEW=MAIN&amp;sortField=<?php echo $j; ?>&amp;sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $employeename; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="img" style="vertical-align: middle" />
						</td>
						<?php $j = 6; ?>
						<td scope="col" width="140" class="listViewThS1">
							<a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&amp;VIEW=MAIN&amp;sortField=<?php echo $j; ?>&amp;sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $lang_empview_JobTitle; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="img" style="vertical-align: middle" />
						</td>
						<?php $j = 9; ?>
						<td scope="col" width="250" class="listViewThS1">
							<a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&amp;VIEW=MAIN&amp;sortField=<?php echo $j; ?>&amp;sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $lang_empview_EmploymentStatus; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="img" style="vertical-align: middle" />
						</td>
						<?php $j = 8; ?>
						<td scope="col" width="250" class="listViewThS1">
							<a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&amp;VIEW=MAIN&amp;sortField=<?php echo $j; ?>&amp;sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $lang_empview_SubDivision; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="img" style="vertical-align: middle" />
						</td>
						<?php
							/* Show supervisor only for admin users, not for supervisors */
							if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
								$j = 10;
						?>
							<td scope="col" width="250" class="listViewThS1">
								<a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&amp;VIEW=MAIN&amp;sortField=<?php echo $j; ?>&amp;sortOrder<?php echo $j; ?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j])?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $lang_empview_Supervisor; ?></a> <img src="../../themes/beyondT/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="img" style="vertical-align: middle" />
							</td>
						<?php
							}
						?>
					</tr>
					<?php
						if ((isset($emplist)) && ($emplist !='')) {
							$compStructObj = new CompStruct();
							$compStructObj->buildAllWorkStations();
							
							for ($j=0; $j<count($emplist);$j++) {
								$descField=$emplist[$j][1];
								$subDivision = "-";
								
								if (isset($emplist[$j][3]) && !empty($emplist[$j][3])) {
									$subDivision = $compStructObj->fetchHierarchString($emplist[$j][3]);
								}
								
								if ($sysConst->viewDescLen <= strlen($descField)) {
									$descField = substr($descField,0,$sysConst->viewDescLen);
									$descField .= "....";
								}
					?>
								<tr valign="top">
								<?php
								if (!($j%2)) {
									$cssClass = 'odd';
								} else {
									$cssClass = 'even';
								}
								
								if($_GET['reqcode']=='EMP') {
					?>
									<td width="50" class="<?php echo $cssClass?>"><input type="checkbox" class="checkbox" name="chkLocID[]" value="<?php echo $emplist[$j][2]?>" /></td>
								<?php } else { ?>
									<td width="50" class="<?php echo $cssClass?>"></td>
								<?php } ?>
									<td width="200" class="<?php echo $cssClass?>"><?php echo (!empty($emplist[$j][0]))?$emplist[$j][0]:$emplist[$j][2]?></td>
									<td width="400" class="<?php echo $cssClass?>" ><a href="./CentralController.php?id=<?php echo $emplist[$j][2]?>&amp;capturemode=updatemode&amp;reqcode=<?php echo $this->getArr['reqcode']?>"
									 class="listViewTdLinkS1"><?php echo $descField?></a> </td>
									<td width="120" class="<?php echo $cssClass?>"><?php echo (!empty($emplist[$j][4]))?$emplist[$j][4]:"-"; ?></td>
									<td width="250" class="<?php echo $cssClass?>"><?php echo (!empty($emplist[$j][6]))?$emplist[$j][6]:"-"; ?></td>
									<td width="250" class="<?php echo $cssClass?>"><?php echo $subDivision; ?></td>
									<?php
									/* Show supervisor only for admin users, not for supervisors */
									if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
									?>
										<td width="250" class="<?php echo $cssClass?>"><?php echo (!empty($emplist[$j][5]))?$emplist[$j][5]:"-";?></td>
									<?php
									}
									?>
							</tr>
						<?php }
					  }
					?>
				</table>
			</div>
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

</body>
</html>
