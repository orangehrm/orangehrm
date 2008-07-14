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
$maxDispLen = $sysConst->viewDescLen;

$locRights = $_SESSION['localRights'];

$currentPage = $this->popArr['currentPage'];

$list = $this->popArr['list'];
$baseURL = './CentralController.php?recruitcode='. $this->getArr['recruitcode'];

$allowAdd = $locRights['add'];
$allowDelete = $locRights['delete'];

if (!isset($this->getArr['sortField']) || ($this->getArr['sortField'] == '')) {
	$this->getArr['sortField']=0;
	$this->getArr['sortOrder0']='ASC';
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

function getSortOrderInWords($SortOrder) {
	if ($SortOrder == 'ASC') {
		return 'Ascending';
	} else {
		return 'Descending';
	}
}

function getDisplayValue($value, $map, $maxLen) {


	if (!empty($map) && isset($map[$value])) {
	    $value = $map[$value];
	}

	if ($maxLen <= strlen($value)) {
		$value = substr($value, 0, $maxLen) . '....';
	}
	return $value;
}

$searchStr = isset($this->postArr['loc_name'])? stripslashes($this->postArr['loc_name']) : '';
if (isset($this->postArr['loc_code'])) {
	$code = $this->postArr['loc_code'];
	if (isset($valueMap[$code])){
		$searchStr = getDisplayValue($searchStr, $valueMap[$code], $maxDispLen);
	}
}

$themeDir = '../../themes/' . $styleSheet;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="<?php echo $themeDir;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("<?php echo $themeDir;?>/css/style.css"); </style>
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
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript" src="../../scripts/octopus.js"></script>
</head>
<script type="text/javascript">

	// Maps to allow searching of mapped values
    var maps = new Array();
<?php
for ($i = 0; $i < count($valueMap); $i++) {
	echo "maps[{$i}] = new Array();\n";
	if (!empty($valueMap[$i]) && is_array($valueMap[$i])) {
	    foreach($valueMap[$i] as $key=>$value) {
			echo "maps[{$i}]['{$value}'] = '$key';\n";
	    }
	}
}
?>
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

	function sortAndSearch(fieldNum, sortOrder) {
		var url = '<?php echo $baseURL;?>&action=<?php echo $this->getArr['action'];?>';
		action = url + '&sortField=' + fieldNum + '&sortOrder' + fieldNum + '=' + sortOrder;

		document.standardView.action = action;
		document.standardView.submit();
	}

	function returnAdd() {
		location.href = '<?php echo $baseURL;?>&action=ViewAdd';
	}

	function returnDelete() {
		var check = false;
		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'chkID[]')){
					check = true;
					break;
				}
			}
		}

		if (check){

			var res = confirm("<?php echo $deletePrompt . ' ' . $lang_Common_ConfirmDelete; ?>");
			if (!res) {
			    return false;
			}

			document.standardView.action="<?php echo $baseURL;?>&action=Delete";
			document.standardView.pageNO.value=1;
			document.standardView.submit();
		} else {
			alert("<?php echo $lang_Common_SelectDelete; ?>");
		}
	}

	function returnSearch() {

		if ($('loc_code').value == -1) {
			alert("<?php echo $lang_Common_SelectField; ?>");
			$('loc_code').Focus();
			return;
		};
		var searchNdx = $('loc_code').value;
		var searchVal = $('loc_name').value;

		if (searchNdx in maps) {
		    map = maps[searchNdx];
		    if (searchVal in map) {
		        $('loc_name').value = map[searchVal];
		    } else {
		        var len = map.length;
		        var allowed = '';
		        for ( var i in map) {
		        	if (allowed == ''){
		            	allowed = i;
		        	} else {
		            	allowed = allowed + ', ' + i;
		            }
		        }
		        alert("<?php echo $lang_Recruit_AllowedValuesAre;?> " + allowed);
		        return;
		    }
		}

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
<form name="standardView" id="standardView" method="post" action="<?php echo $baseURL;?>&action=<?php echo $this->getArr['action'];?>&sortField=<?php echo $this->getArr['sortField']?>&sortOrder<?php echo $this->getArr['sortField']?>=<?php echo $this->getArr['sortOrder'.$this->getArr['sortField']];?>">
	<div class="moduleTitle" style="padding: 6px;"><h2><?php echo$title; ?></h2></div>
	<div>
		<input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>" />
		<input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>" />
		<div style="padding: 6px;">
		<?php	if($allowAdd) { ?>
		<img 
			style="border: none" 
			title="Add" 
			alt="Add" 
			src="<?php echo $themeDir;?>/pictures/btn_add.gif" 
			onclick="returnAdd();" 
			onmouseout="this.src='<?php echo $themeDir;?>/pictures/btn_add.gif';" 
			onmouseover="this.src='<?php echo $themeDir;?>/pictures/btn_add_02.gif';" />
		<?php	} ?>

		<?php if($allowDelete) { ?>
		<img 
			style="border: none" 
	   		title="Delete" 
	   		alt="Delete" 
			src="<?php echo $themeDir;?>/pictures/btn_delete.gif"
			onclick="returnDelete();" 
			onmouseout="this.src='<?php echo $themeDir;?>/pictures/btn_delete.gif';" 
			onmouseover="this.src='<?php echo $themeDir;?>/pictures/btn_delete_02.gif';" />
		<?php } ?>
		</div>
	</div>

	<div style="width: 98%">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="22%" style="white-space: nowrap"><h3><?php echo $search?></h3></td>
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
					<label for="loc_code" style="float: left; padding-right: 10px;"><?php echo $SearchBy?></label>
					<select style="z-index: 99;" name="loc_code" id="loc_code">
					<?php 
						for($c=-1;count($srchlist)-1>$c;$c++) {
							if(isset($this->postArr['loc_code']) && $this->postArr['loc_code']==$c) {
								echo "<option selected value='" . $c ."'>".$srchlist[$c+1] ."</option>";
							} else {
								echo "<option value='" . $c ."'>".$srchlist[$c+1] ."</option>";
							}
						}
					?>
					</select>
				</td>
				<td width="300" class="dataLabel" style="white-space: nowrap">
					<label for="loc_name" style="float: left; padding-right: 10px;"><?php echo $description?></label>
					<input type="text" size="20" name="loc_name" id="loc_name" class="dataField"  value="<?php echo $searchStr;?>" />
				</td>
				<td align="right" width="180" class="dataLabel">
					<img 
						title="Search" 
						alt="Search" 
						src="<?php echo $themeDir;?>/pictures/btn_search.gif" 
						onclick="returnSearch();" 
						onmouseover="this.src='<?php echo $themeDir;?>/pictures/btn_search_02.gif';" 
						onmouseout="this.src='<?php echo $themeDir;?>/pictures/btn_search.gif';" />
					<img 
						title="Clear" 
						alt="Clear" 
						src="<?php echo $themeDir;?>/pictures/btn_clear.gif" 
						onclick="clear_form();" 
						onmouseover="this.src='<?php echo $themeDir;?>/pictures/btn_clear_02.gif';" 
						onmouseout="this.src='<?php echo $themeDir;?>/pictures/btn_clear.gif';" />
				</td>
			</tr>
		</table>
	</div>
	
	<div style="padding-top: 4px; width: 98%">
		<span id="messageDisplay">
			<?php
			if (empty($list)) { 
					echo $dispMessage; 
			} 
			?>&nbsp;
		</span>
	</div>
	
	<div style="text-align: right; padding-top: 4px; width: 98%">
		<?php
		$temp = $this->popArr['count'];
		$commonFunc = new CommonFunctions();
		$pageStr = $commonFunc->printPageLinks($temp, $currentPage);
		$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);
		
		echo $pageStr;
		?>&nbsp;
	</div>

	<div class="roundbox">
		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
		  <tr style="white-space: nowrap">
			<td width="50" nowrap="nowrap" class="listViewThS1" scope="col">
				<?php	if($allowDelete) { ?>
					<input type='checkbox' class='checkbox' name='allCheck' value='' onclick="doHandleAll();" />
				<?php	} else {	?>
					&nbsp;
				<?php } ?>
			</td>
			<?php
				for ($j=0; $j < count($headings); $j++) {
					if (!isset($this->getArr['sortOrder'.$j])) {
						$this->getArr['sortOrder'.$j] = 'null';
					}
					$nextSortOrder = getNextSortOrder($this->getArr['sortOrder'.$j]);
					$nextSortInWords = getSortOrderInWords($nextSortOrder);
			?>
					<td scope="col" width="250" class="listViewThS1">
						<a 
							href="#" 
							onclick="sortAndSearch(<?php echo $j?>, '<?php echo $nextSortOrder;?>');" 
							title="Sort in <?php echo $nextSortInWords; ?> order">
								<?php echo $headings[$j]?>
						</a> 
						<img 
							src="<?php echo $themeDir;?>/icons/<?php echo $this->getArr['sortOrder'.$j]?>.png" 
							style="width: 8px; height:10px; border: none; vertical-align: middle" />
					</td>
			<?php 
				} 
			?>
			</tr>
			<?php
				if ((isset($list)) && ($list !='')) {
					for ($j=0; $j < count($list);$j++) {
						$cssClass = ($j%2) ? 'even' : 'odd';
			?>
						<tr>
							<td class="<?php echo $cssClass?>" width="50">
							<?php 
								if($allowDelete) { 
									if (CommonFunctions::extractNumericId($list[$j][0]) > 0) { 
							?>
										<input type='checkbox' class='checkbox' name='chkID[]' value='<?php echo $list[$j][0]?>' />
							<?php 	} 
								} else {  ?>
									&nbsp;
							<?php 	}  ?>
							</td>
						<td class="<?php echo $cssClass?>" width="250">
							<a href="<?php echo $baseURL . '&id='. $list[$j][0];?>&amp;action=View" class="listViewTdLinkS1"><?php echo $list[$j][0]?></a>
						</td>
						<?php
							$k =	1;
							if ($k < count($headings)) {
								$descField = getDisplayValue($list[$j][$k], $valueMap[$k], $maxDispLen);
							}
						?>
						<td class="<?php echo $cssClass?>" width="400" >
							<a href="<?php echo $baseURL . '&id='. $list[$j][0];?>&amp;action=View" class="listViewTdLinkS1"><?php echo $descField?></a>
						</td>
						<?php
							for ($k=2; $k < count($headings); $k++) {
								$descField = getDisplayValue($list[$j][$k], $valueMap[$k], $maxDispLen);
						?>
							<td class="<?php echo $cssClass?>" width="400" ><?php echo $descField?></td>
						<?php } ?>
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
