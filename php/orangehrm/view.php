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

	$sysConst = new sysConf();
	$locRights = $_SESSION['localRights'];

	$currentPage = $this->popArr['currentPage'];

	$message = $this->popArr['message'];

	if (!isset($this->getArr['sortField']) || ($this->getArr['sortField'] == '')) {
		$this->getArr['sortField'] = 0;
		$this->getArr['sortOrder0'] = 'ASC';
	}

	$readOnlyView = (isset($this->popArr['readOnlyView'])) && ($this->popArr['readOnlyView'] === true);

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
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
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

	function sortAndSearch(action) {
		document.standardView.action = action;
		document.standardView.submit();
	}

	function returnAdd() {
	<?php
		$esp = isset($_GET['isAdmin'])? ('&isAdmin='.$_GET['isAdmin']) : '';

		switch($headingInfo[2]) {
			case 1 : 
				echo "location.href = './CentralController.php?uniqcode=".$this->getArr['uniqcode']."&capturemode=addmode".$esp."'";
				break;
			case 2 : 
				echo "var popup=window.open('../../genpop.php?uniqcode=".$this->getArr['uniqcode']."','Employees','modal=yes,height=450,width=600');";
				echo "if(!popup.opener) popup.opener=self;";
				 break;
			}
	?>
	}

	function returnDelete() {
		$check = 0;
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'chkLocID[]')){
					$check = 1;
				}
			}
		}

		if ($check == 1){

			var res = confirm("<?php echo "{$headingInfo[4]}. {$lang_Common_ConfirmDelete}"; ?>");

			if(!res) return;

			document.standardView.delState.value = 'DeleteMode';
			document.standardView.pageNO.value=1;
			document.standardView.submit();
		}else{
			alert("<?php echo $lang_Common_SelectDelete; ?>");
		}
	}

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

	function doHandleAll() {
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

	function doUnCheckAll() {
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
	
</script>

<body>
<form name="standardView" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN&sortField=<?php echo $this->getArr['sortField']?>&sortOrder<?php echo $this->getArr['sortField']?>=<?php echo $this->getArr['sortOrder'.$this->getArr['sortField']].$esp?>">
	<div class="moduleTitle" style="padding: 6px;"><h2><?php echo $headingInfo[3]; ?></h2></div>
		<div>
			<input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>" />
			<input type="hidden" name="delState" value="" />
			<input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>" />
			<div style="padding: 6px;">
				<?php if (!$readOnlyView) { ?>
					<img 
						title="Add" 
						alt="Add" 
						src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_add.gif" 
						style="border: none"
						onclick="<?php	
							if($locRights['add']) { 
								?>returnAdd();<?php 
							} else { 
								?>alert('<?php echo $lang_Common_AccessDenied;?>');<?php 
							} ?>" 
						onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_add.gif';" 
						onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_add_02.gif';" />
					<?php	if($headingInfo[2]==1) { ?>
						<img 
							title="Delete" 
							alt="Delete" 
							style="border: none;" 
							src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_delete.gif"
							onclick="<?php 
								if($locRights['delete']) { 
									?>returnDelete();<?php } 
								else { 
									?>alert('<?php echo $lang_Common_AccessDenied;?>');<?php 
								} ?>" 
							onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_delete.gif';" 
							onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_delete_02.gif';" />
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<div style="width: 98%;">
		<?php 
		/*
		 *  TODO: This need to be remove when search functionality is implemented for Import/Export and Custom fields
		 */
		
		if ($this->getArr['uniqcode'] != 'CIM' && $this->getArr['uniqcode'] != 'CEX' && $this->getArr['uniqcode'] != 'CTM') {
		?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="22%" style="white-space: nowrap;"><h3><?php echo $search?></h3></td>
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
				&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
			</table>
		</div>
		<div class="roundbox">
			<table  border="0" cellpadding="5" cellspacing="0" class="">
				<tr>
					<td width="200" class="dataLabel">
						<label for="loc_code" style="float: left; padding-right: 10px;"><?php echo $SearchBy?></label>
						<select style="z-index: 99;" name="loc_code">
							<?php for($c=-1;count($srchlist)-1>$c;$c++)
								if(isset($this->postArr['loc_code']) && $this->postArr['loc_code'] == $c) {
									echo "<option selected value='" . $c ."'>".$srchlist[$c+1] ."</option>";
								} else {
									echo "<option value='" . $c ."'>".$srchlist[$c+1] ."</option>";
								}
							?>
						</select>
					</td>
					<td width="300" class="dataLabel" style="white-space: nowrap;">
						<label for="loc_name" style="float: left; padding-right: 10px;"><?php echo $description?></label>
						<input type=text size="20" name="loc_name" class="dataField" value="<?php echo isset($this->postArr['loc_name']) ? stripslashes($this->postArr['loc_name']):''?>" />
					</td>
					<td align="right" width="180" class="dataLabel">
						<img 
							title="Search" 
							alt="Search" 
							src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_search.gif" 
							onclick="returnSearch();" 
							onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_search.gif';" 
							onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_search_02.gif';" />&nbsp;&nbsp;
						<img 
							title="Clear" 
							alt="Clear"
							src="../../themes/<?php echo $styleSheet; ?>/pictures/btn_clear.gif"
							onclick="clear_form();" 
							onmouseout="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_clear.gif';" 
							onmouseover="this.src='../../themes/<?php echo $styleSheet; ?>/pictures/btn_clear_02.gif';" />
					</td>
				</tr>
			</table>
	</div>
	<?php
	/*
	 *  TODO: This need to be remove when search functionality is implemented for Import/Export and Custom fields
	 */
	 }
	?>
	<div style="padding-top: 4px; width: 98%">
		<span id="messageDisplay">
			<?php
			if ($message == '') { 
					echo $dispMessage; 
			} 
			?>
		</span>
	</div>
	<div style="text-align: right; padding-top: 4px; width: 98%">
		<?php
		$temp = $this->popArr['temp'];
		$commonFunc = new CommonFunctions();
		$pageStr = $commonFunc->printPageLinks($temp, $currentPage);
		$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);
		
		echo $pageStr;
		?>
	</div>
	<div class="roundbox">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
			<tr style="white-space: nowrap">
				<td width="50" class="listViewThS1" scope="col" style="white-space: nowrap;">
				<?php	if (($headingInfo[2]==1) && (!$readOnlyView)) { ?>
					<input type="checkbox" class="checkbox" name="allCheck" value="" onclick="doHandleAll();" />
				<?php	}	?>
				</td>
				<?php
					for ($j=0; $j < count($headings); $j++) {
						if (!isset($this->getArr['sortOrder'.$j])) {
							$this->getArr['sortOrder'.$j] = 'null';
						}
				?>
					<td scope="col" width="250" class="listViewThS1">
						<a href="#" onclick="sortAndSearch('<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN&sortField=<?php echo $j?>&sortOrder<?php echo $j?>=<?php echo getNextSortOrder($this->getArr['sortOrder'.$j]).$esp?>');" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($this->getArr['sortOrder'.$j]))?> order"><?php echo $headings[$j]?></a> <img src="../../themes/<?php echo $styleSheet; ?>/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="" style="vertical-align: middle">
					</td>
				<?php } ?>
      			<td class="listViewThS1">&nbsp;</td>
    		</tr>
    		<?php
				if ((isset($message)) && ($message !='')) {
					for ($j = 0; $j < count($message); $j++) {
					
						if(!($j%2)) {
							$cssClass = 'odd';
						} else {
							$cssClass = 'even';
						}
	 		?>
				<tr>
       				<td class="<?php echo $cssClass?>" width="50">
					<?php if($headingInfo[2] == 1) { ?>
						<?php if ((!$readOnlyView) && (CommonFunctions::extractNumericId($message[$j][0]) > 0)) { ?>
							<input type='checkbox' class='checkbox' name='chkLocID[]' value='<?php echo $message[$j][0]?>' />
						<?php } ?>
					<?php 	} else { ?>
						&nbsp;
					<?php 	}  ?>
					</td>
		 			<td class="<?php echo $cssClass?>" width="250">
		 				<a href="./CentralController.php?id=<?php echo $message[$j][0]?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&capturemode=updatemode<?php echo $esp?>" class="listViewTdLinkS1"><?php echo $message[$j][0]?></a>
		 			</td>
					<?php
						$k=1;
						if ($k < count($headings)) {
							$descField=$message[$j][$k];
	
							if($sysConst->viewDescLen <= strlen($descField)) {
	
								$descField = substr($descField,0,$sysConst->viewDescLen);
								$descField .= "....";
							}
						}
					?>
					<td class="<?php echo $cssClass?>" width="400" >
						<a href="./CentralController.php?id=<?php echo $message[$j][0]?>&uniqcode=<?php echo $this->getArr['uniqcode']?>&capturemode=updatemode<?php echo $esp?>" class="listViewTdLinkS1"><?php echo $descField?></a>
					</td>
					<?php
		 				for ($k=2; $k < count($headings); $k++) {

							$descField=$message[$j][$k];

		  	 				if($sysConst->viewDescLen <= strlen($descField)) {
			 	   				$descField = substr($descField,0,$sysConst->viewDescLen);
			 	   				$descField .= "....";
			 				}
		 			?>
		 			<td class="<?php echo $cssClass?>" width="400" ><?php echo $descField?></td>
				<?php } ?>
				<td class="<?php echo $cssClass?>" width="400" >&nbsp;</td>
		 	</tr>
		 	<?php 
				}
		 	}
			?>
 		</table>
	</div>
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
