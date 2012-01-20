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

$currentPage   = $this->popArr['currentPage'];
$token         = $this->popArr['token'];

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

$GLOBALS['lang_Common_SortAscending'] = $lang_Common_SortAscending;
$GLOBALS['lang_Common_SortDescending'] = $lang_Common_SortDescending;

function nextSortOrderInWords($sortOrder) {
    return $sortOrder == 'ASC' ? $GLOBALS['lang_Common_SortDescending'] : $GLOBALS['lang_Common_SortAscending'];
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
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[
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

		searchBy = $('loc_code');

		if (searchBy.options[searchBy.selectedIndex].value == -1) {
			alert("<?php echo $lang_Common_SelectField; ?>");
			searchBy.focus();
			return;
		};
		searchNdx = searchBy.options[searchBy.selectedIndex].value;
		var searchVal = $('loc_name').value;

		if (searchNdx == 3) {
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
//]]>
</script>
</head>
<body>
<div class="outerbox">
<form name="standardView" id="standardView" method="post" action="<?php echo $baseURL;?>&amp;action=<?php echo $this->getArr['action'];?>&amp;sortField=<?php echo $this->getArr['sortField']?>&amp;sortOrder<?php echo $this->getArr['sortField']?>=<?php echo $this->getArr['sortOrder'.$this->getArr['sortField']];?>">
   <input type="hidden" value="<?php echo $token;?>" name="token" />
	<div class="mainHeading" style="padding: 6px;"><h2><?php echo $title; ?></h2></div>
    <input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>" />
    <input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>" />
    <?php
    if (isset($this->getArr['message'])) {
        $expString  = $this->getArr['message'];
        $messageType = CommonFunctions::getCssClassForMessage($expString, 'failure');
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
            $optionCount = count($srchlist);
            for ($c = -1; $optionCount - 1 > $c; $c++) {
                $selected = "";
                if(isset($this->postArr['loc_code']) && $this->postArr['loc_code'] == $c) {
                    $selected = 'selected="selected"';
                }
                echo "<option $selected value='" . $c ."'>".$srchlist[$c+1] ."</option>";
            }
            ?>
        </select>

        <label for="loc_name"><?php echo $description?></label>
        <input type="text" size="20" name="loc_name" id="loc_name" value="<?php echo $searchStr;?>" />
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
        <?php if ($allowAdd) { ?>
            <input type="button" class="plainbtn" onclick="returnAdd();"
                onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
                value="<?php echo $lang_Common_Add;?>" />
        <?php   } ?>
            <?php if ($allowDelete) { ?>
                <input type="button" class="plainbtn" onclick="returnDelete();"
                    onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
                    value="<?php echo $lang_Common_Delete;?>" />
        <?php   } ?>
        </div>
        <div class="noresultsbar"><?php echo (empty($list)) ? $norecorddisplay : '';?></div>
        <div class="pagingbar">
        <?php
            $temp = $this->popArr['count'];
            $commonFunc = new CommonFunctions();
            $pageStr = $commonFunc->printPageLinks($temp, $currentPage);
            $pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

            echo $pageStr;

            /*for ($j = 0; $j < 11; $j++) {
                if (!isset($this->getArr['sortOrder'.$j])) {
                    $this->getArr['sortOrder'.$j] = 'null';
                }
            } */
        ?>
        </div>
    <br class="clear" />
    </div>

    <br class="clear" />
    <table cellpadding="0" cellspacing="0" class="data-table">
        <thead>
        <tr>
			<td width="50">
				<?php if ($allowDelete) { ?>
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
                    $sortOrder = $this->getArr['sortOrder'.$j];
					$nextSortOrder = getNextSortOrder($sortOrder);
					$nextSortInWords = nextSortOrderInWords($sortOrder);
			?>
					<td scope="col">
						<a	href="#" class="<?php echo $sortOrder;?>"
							onclick="sortAndSearch(<?php echo $j?>, '<?php echo $nextSortOrder;?>');"
							title="<?php echo $nextSortInWords; ?>">
								<?php echo $headings[$j]?>
						</a>
					</td>
			<?php
				}
			?>
			</tr>
        </thead>
        <tbody>
			<?php
				if ((isset($list)) && ($list !='')) {
					for ($j=0; $j < count($list);$j++) {
						$cssClass = ($j%2) ? 'even' : 'odd';
                        $detailsUrl = $baseURL . '&amp;id='. $list[$j][0] . '&amp;action=View';
			?>
						<tr>
							<td class="<?php echo $cssClass?>">
							<?php
								if ($allowDelete) {
									if (CommonFunctions::extractNumericId($list[$j][0]) > 0) {
							?>
										<input type='checkbox' class='checkbox' name='chkID[]' value='<?php echo $list[$j][0]?>' />
							<?php 	}
								} else {  ?>
									&nbsp;
							<?php 	}  ?>
							</td>
						<td class="<?php echo $cssClass?>">
							<a href="<?php echo $detailsUrl;?>"><?php echo $list[$j][0]?></a>
						</td>
						<?php
							for ($k = 1; $k < count($headings); $k++) {
								$descField = getDisplayValue($list[$j][$k], $valueMap[$k], $maxDispLen);
						?>
							<td class="<?php echo $cssClass?>">
                            <?php if ($k == 1) {
                                      echo "<a href='{$detailsUrl}'>{$descField}</a>";
                                  } else {
                                      echo $descField;
                                  } ?>
                            </td>
						<?php } ?>
						</tr>
					<?php
					}
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
