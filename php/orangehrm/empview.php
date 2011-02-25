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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
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

	$GLOBALS['lang_Common_SortAscending'] = $lang_Common_SortAscending;
	$GLOBALS['lang_Common_SortDescending'] = $lang_Common_SortDescending;

	function nextSortOrderInWords($sortOrder) {
		return $sortOrder == 'ASC' ? $GLOBALS['lang_Common_SortDescending'] : $GLOBALS['lang_Common_SortAscending'];
	}
?>
<head>
<link href="../../themes/<?php echo $styleSheet; ?>/css/style.css" rel="stylesheet" type="text/css"/>
<!--[if lte IE 6]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE6_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<!--[if IE]>
<link href="../../themes/<?php echo $styleSheet; ?>/css/IE_style.css" rel="stylesheet" type="text/css"/>
<![endif]-->
<script type="text/javascript" src="../../themes/<?php echo $styleSheet;?>/scripts/style.js"></script>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
<script type="text/javascript">
//<![CDATA[
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

	function sortAndSearch(sortField, sortOrder) {
		var uri = "<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&VIEW=MAIN&sortField=" + sortField + "&sortOrder" + sortField + "=" + sortOrder;
		document.standardView.action = uri;
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
        var popup=window.open('../../templates/hrfunct/emppop.php?reqcode=<?php echo $this->getArr['reqcode']?>','Employees','height=450,width=400,scrollbars=1');
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

	function doCheckAll() {
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
	
	/**
	 * If at least one day is unchecked, main check box would be unchecked
	 */

	function unCheckMain() {
		noOfCheckboxes = 0;
		noOfCheckedCheckboxes = 0;

		with (document.getElementById('standardView')) {
			for (i = 0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox' && elements[i].name != 'allCheck') {
					noOfCheckboxes++;
					if (elements[i].checked == true) {
						noOfCheckedCheckboxes++;
					}

				}
			}
		}

		document.getElementById('allCheck').checked = (noOfCheckboxes == noOfCheckedCheckboxes);
	}

	function clear_form() {
		document.standardView.loc_code.options[0].selected=true;
		document.standardView.loc_name.value='';
	}

	parent.scrollTo(0, 0);
//]]>
</script>
</head>
<body>
<div class="outerbox">

	<form name="standardView" id="standardView" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?reqcode=<?php echo $this->getArr['reqcode']?>&amp;VIEW=MAIN&amp;sortField=<?php echo $this->getArr['sortField']; ?>&amp;sortOrder<?php echo $this->getArr['sortField']; ?>=<?php echo $this->getArr['sortOrder'.$this->getArr['sortField']]?>">

		<div class="mainHeading"><h2><?php echo $headingInfo[0]?></h2></div>
		<input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>" />
		<input type="hidden" name="delState" value="" />
		<input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>" />
		<input type="hidden" name="empID" value="" />


		<?php
		if (isset($this->getArr['message'])) {
			$expString  = $this->getArr['message'];
			$messageType = CommonFunctions::getCssClassForMessage($expString);
			$messageType = 'failure';
		?>
		<div class="messagebar">
			<span class="<?php echo $messageType; ?>"><?php echo $$expString; ?></span>
		</div>
		<?php
		}
		?>

		<div class="searchbox">
			<label for="loc_code"><?php echo $searchby?></label>
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
			<label for="loc_name"><?php echo $description?></label>
			<input type="text" size="20" name="loc_name" id="loc_name" value="<?php echo isset($this->postArr['loc_name'])? stripslashes($this->postArr['loc_name']):''?>" />
			<input type="button" class="plainbtn" onclick="returnSearch();"
				onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
				value="<?php echo $lang_Common_Search;?>" />
			<input type="button" class="plainbtn" onclick="clear_form();"
				onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
				 value="<?php echo $lang_Common_Reset;?>" />
			<br class="clear"/>
		</div>

			<div class="actionbar">
				<div class="actionbuttons">
					<input type="button" class="plainbtn"
					<?php echo ($locRights['add']) ? 'onclick="returnAdd();"' : 'style=visibility:hidden;'; ?>
						onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
						value="<?php echo $lang_Common_Add;?>" />

					<?php if($this->getArr['reqcode']=='EMP') { ?>
						<input type="button" class="plainbtn"
						<?php echo ($locRights['delete']) ? 'onclick="returnDelete();"' : 'style=visibility:hidden;'; ?>
							onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
							value="<?php echo $lang_Common_Delete;?>" />

					<?php } ?>
				</div>
				<div class="noresultsbar"><?php echo (empty($emplist)) ? $norecorddisplay : '';?></div>
				<div class="pagingbar">
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
			<br class="clear" />
			</div>
			<br class="clear" />
				<table cellspacing="0" cellpadding="0" class="data-table">
					<thead>
					<tr>
						<td width="50">
							<?php if (empty($emplist)) { ?>
							&nbsp;
							<?php } else { ?>
							<input type="checkbox" name="allCheck" id="allCheck" class="checkbox" style="margin-left:1px"
								onclick="doHandleAll()" />
							<?php } ?>
						</td>
						<?php $j = 0;
							  $sortOrder = $this->getArr['sortOrder' . $j];
						?>
						<td scope="col">
							<a href="#" onclick="sortAndSearch(<?php echo $j; ?>, '<?php echo getNextSortOrder($sortOrder);?>');"
								title="<?php echo nextSortOrderInWords($sortOrder);?>" class="<?php echo $sortOrder;?>"><?php echo $employeeid; ?>
							</a>
						</td>
						<?php $j = 7;
							  $sortOrder = $this->getArr['sortOrder' . $j];
						?>
						<td scope="col">
							<a href="#" onclick="sortAndSearch(<?php echo $j; ?>, '<?php echo getNextSortOrder($sortOrder);?>');"
							title="<?php echo nextSortOrderInWords($sortOrder);?>" class="<?php echo $sortOrder;?>"><?php echo $employeename; ?> </a>
						</td>
						<?php $j = 6;
							  $sortOrder = $this->getArr['sortOrder' . $j];
						?>
						<td scope="col">
							<a href="#" onclick="sortAndSearch(<?php echo $j; ?>, '<?php echo getNextSortOrder($sortOrder);?>');"
							title="<?php echo nextSortOrderInWords($sortOrder);?>" class="<?php echo $sortOrder;?>"><?php echo $lang_empview_JobTitle; ?></a>
						</td>
						<?php $j = 9;
							  $sortOrder = $this->getArr['sortOrder' . $j];
						?>
						<td scope="col">
							<a href="#" onclick="sortAndSearch(<?php echo $j; ?>, '<?php echo getNextSortOrder($sortOrder);?>');"
							title="<?php echo nextSortOrderInWords($sortOrder);?>" class="<?php echo $sortOrder;?>"><?php echo $lang_empview_EmploymentStatus; ?></a>
						</td>
						<?php $j = 8;
							  $sortOrder = $this->getArr['sortOrder' . $j];
						?>
						<td scope="col">
							<a href="#" onclick="sortAndSearch(<?php echo $j; ?>, '<?php echo getNextSortOrder($sortOrder);?>');"
							title="<?php echo nextSortOrderInWords($sortOrder);?>" class="<?php echo $sortOrder;?>"><?php echo $lang_empview_SubDivision; ?></a>
						</td>
						<?php
							/* Show supervisor only for admin users, not for supervisors */
							if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
								$j = 10;
								$sortOrder = $this->getArr['sortOrder' . $j];
						?>
							<td scope="col">
							<a href="#" onclick="sortAndSearch(<?php echo $j; ?>, '<?php echo getNextSortOrder($sortOrder);?>');"
								title="<?php echo nextSortOrderInWords($sortOrder);?>" class="<?php echo $sortOrder;?>"><?php echo $lang_empview_Supervisor; ?></a>
							</td>
						<?php
							}
						?>
					</tr>
					</thead>

					<tbody>
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

								$cssClass = ($j%2) ? 'even' : 'odd';
					?>
								<tr class="<?php echo $cssClass;?>">
								<?php
								if($_GET['reqcode']=='EMP') {
					?>
									<td ><input type="checkbox" class="checkbox" name="chkLocID[]" value="<?php echo $emplist[$j][2]?>" onclick="unCheckMain();" /></td>
								<?php } else { ?>
									<td ></td>
								<?php } ?>
									<td >
                                <!-- if employee id removed we dont show it -->
                                <?php //echo (!empty($emplist[$j][0]))?$emplist[$j][0]:$emplist[$j][2]
                                    echo (!empty($emplist[$j][0]))?$emplist[$j][0]:"";
                                ?></td>

									<td >
										<!--<a target="_self" href="./CentralController.php?menu_no_top=hr&amp;id=<?php echo $emplist[$j][2]?>&amp;capturemode=updatemode&amp;reqcode=<?php echo $this->getArr['reqcode']?>&amp;currentPage=<?php echo $currentPage; ?>">-->
<a target="_self" href="../../symfony/web/index.php/pim/viewPersonalDetails?empNumber=<?php echo $emplist[$j][2]?>">
										<?php echo $descField?></a> </td>
									<td ><?php echo (!empty($emplist[$j][4]))?$emplist[$j][4]:"-"; ?></td>
									<td ><?php echo (!empty($emplist[$j][6]))?$emplist[$j][6]:"-"; ?></td>
									<td ><?php echo $subDivision; ?></td>
									<?php
									/* Show supervisor only for admin users, not for supervisors */
									if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
									?>
										<td ><?php echo (!empty($emplist[$j][5]))?$emplist[$j][5]:"-";?></td>
									<?php
									}
									?>
							</tr>
						<?php }
					  }
					?>
					</tbody>
				</table>
	</form>
</div>
<script type="text/javascript">
    <!--
    	if (document.getElementById && document.createElement) {
 			roundBorder('outerbox');
		}
    -->
</script>
</body>
</html>
