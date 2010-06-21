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
<script  language="javascript" type="text/javascript">
//<![CDATA[
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
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true) && (elements[i].name == 'chkLocID[]')){
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

	function toggleSelectAll() {
		noOfRecords = 0;
		noOfCheckedRecords = 0;

		with (document.standardView) {
			for (var i=0; i < elements.length; i++) {
				if (elements[i].type == 'checkbox' && elements[i].name != 'allCheck') {
					noOfRecords++;
					if (elements[i].checked) {
						noOfCheckedRecords++;
					}
				}
			}

			if (noOfCheckedRecords == noOfRecords) {
				elements['allCheck'].checked = true;
			} else {
				elements['allCheck'].checked = false;
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
//]]>
</script>
</head>
<body>
<div class="outerbox">
<form name="standardView" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?repcode=<?php echo $this->getArr['repcode']?>&amp;VIEW=MAIN">
   <input type="hidden" value="<?php echo $this->popArr['token'];?>" name="token" />
	<div class="mainHeading"><h2><?php echo $heading[3]; ?></h2></div>
    <input type="hidden" name="captureState" value="<?php echo isset($this->postArr['captureState'])?$this->postArr['captureState']:''?>"/>
    <input type="hidden" name="delState" value=""/>
    <input type="hidden" name="pageNO" value="<?php echo isset($this->postArr['pageNO'])?$this->postArr['pageNO']:'1'?>"/>

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
            $optionCount = count($srchlist[0]);
            for ($c = 0; $optionCount > $c; $c++) {
                $selected = "";
                if (isset($this->postArr['loc_code']) && $this->postArr['loc_code'] == $srchlist[0][$c]) {
                    $selected = 'selected="selected"';
                }
                echo "<option $selected value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
            }
            ?>
        </select>

        <label for="loc_name"><?php echo $searchfor; ?></label>
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
        <?php if($locRights['add'] && $headingInfo[2] == 1) { ?>
            <input type="button" class="plainbtn" onclick="returnAdd();"
                onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
                value="<?php echo $lang_Common_Add;?>" />
            <?php
              }
              if ($locRights['delete'] && $headingInfo[2] == 1 && isset($message) && $message != '') {
            ?>
                <input type="button" class="plainbtn" onclick="returnDelete();"
                    onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
                    value="<?php echo $lang_Common_Delete;?>" />
        <?php  } ?>
        </div>
        <div class="noresultsbar"><?php echo (empty($message)) ? $norecorddisplay : '';?></div>
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

    <table cellpadding="0" cellspacing="0" class="data-table">
        <thead>
			<tr>
				<td width="50">
				<?php if($headingInfo[2]==1) { ?>
					<input type='checkbox' class='checkbox' name='allCheck' value='' onclick="doHandleAll();"/>
				<?php }?>
				</td>
				<td scope="col"><?php echo $heading[0]?></td>
				<td scope="col"><?php echo $heading[1]?></td>
                <td></td>
			</tr>
        </thead>
        <tbody>
		<?php
		if ((isset($message)) && ($message !='')) {

			for ($j = 0; $j < count($message); $j++) {

				$descField=$message[$j][1];

				if($sysConst->viewDescLen <= strlen($descField)) {
					$descField = substr($descField,0,$sysConst->viewDescLen);
					$descField .= "....";
				}

				$cssClass = ($j%2) ? 'even' : 'odd';
		?>
				<tr>
					<td class="<?php echo $cssClass; ?>" width="50">
					<?php if($headingInfo[2]==1) { ?>
						<input type="checkbox" class="checkbox" name="chkLocID[]" value="<?php echo $message[$j][0]?>"
							onclick="toggleSelectAll()" />
					<?php } else { ?>
						&nbsp;
					<?php } ?>
					</td>
					<td class="<?php echo $cssClass; ?>">
						<a href="./CentralController.php?id=<?php echo $message[$j][0]?>&amp;repcode=<?php echo $this->getArr['repcode']?>&amp;capturemode=updatemode"
							class="listViewTdLinkS1"><?php echo $message[$j][0]?></a>
                    </td>
					<td class="<?php echo $cssClass; ?>"><?php echo $descField?></td>
				    <td class="<?php echo $cssClass; ?>">
                    <?php if(trim($_GET['repcode'])  == 'EMPVIEW' && isset($csvExportRepotsPluginAvailable))  { ?>
                        <input type="button" class="button" id="btnExport" value="<?php echo $lang_DataExport_Export;?>"
	        	              title="<?php echo $lang_DataExport_Export?>" name="btnExport"
                              onclick="exportData('<?php echo $message[$j][0]?>')" />
				    <?php } ?>
				    </td>
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
