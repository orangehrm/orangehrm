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

session_start();
if(!isset($_SESSION['fname'])) {

	header("Location: ./relogin.htm");
	exit();
}

define('ROOT_PATH', $_SESSION['path']);
require_once ROOT_PATH . '/lib/models/hrfunct/EmpInfo.php';
require_once ROOT_PATH . '/lib/controllers/EmpViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/common/Language.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

require_once ROOT_PATH . '/language/default/lang_default_full.php';

$lan = new Language();

require_once($lan->getLangPath("full.php"));

$styleSheet = CommonFunctions::getTheme();

$srchlist[0] = array( -1 , 2 , 1 );
$srchlist[1] = array( "-{$lang_Common_Select}-" , $lang_view_ID , $lang_Commn_name);

$reqPath = "";

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

	if (!isset($_GET['sortField']) || ($_GET['sortField'] == '')) {
		$_GET['sortField']=0;
		$_GET['sortOrder0']='ASC';
	}

	$sysConst = new sysConf();
	$empviewcontroller = new EmpViewController();



$currentPage = (isset($_POST['pageNO'])) ? (int)$_POST['pageNO'] : 1;

if (isset($_POST['captureState'])&& ($_POST['captureState']=="SearchMode"))
    {
    $choice=$_POST['loc_code'];
    $strName=trim($_POST['loc_name']);

    $emplist = $empviewcontroller->getUnAssigned($_GET['reqcode'],$currentPage,$strName,$choice, $_GET['sortField'], $_GET['sortOrder'.$_GET['sortField']]);
    }
else
    $emplist = $empviewcontroller->getUnAssigned($_GET['reqcode'],$currentPage, '', -1, $_GET['sortField'], $_GET['sortOrder'.$_GET['sortField']]);

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
<title><?php echo $lang_emppop_title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script type="text/javascript">
//<![CDATA[
	function clear_form() {
		document.standardView.loc_code.options[0].selected=true;
		document.standardView.loc_name.value='';
	}

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

    function empSel(empNumber, empName) {
<?php
		$reqPath="?reqcode=".CommonFunctions::escapeHtml($_GET['reqcode']);
		if(isset($_GET['USR'])) {
			$reqPath.="&USR=" . CommonFunctions::escapeHtml($_GET['USR']);
?>
		getElementByName("cmbUserEmpID", window.opener.document).value = empNumber;
		getElementByName("txtUserEmpID", window.opener.document).value = empName;
        window.close();

<?php   } else if(isset($_GET['REPORT'])) {
			$reqPath.="&REPORT=" . CommonFunctions::escapeHtml($_GET['REPORT']);
?>
        window.opener.document.frmEmpRepTo.txtRepEmpID.value = empNumber;
        window.opener.document.frmEmpRepTo.cmbRepEmpID.value =empName;
        window.close();

<?php   } else if(isset($_GET['LEAVE']) && ($_GET['LEAVE'] == 'LEAVE')) {
			$reqPath.="&LEAVE={$_GET['LEAVE']}";
?>
        window.opener.document.frmLeaveApp.cmbEmployeeId.value = empNumber;
        window.opener.document.frmLeaveApp.txtEmployeeId.value = empName;
        if (!window.opener.closed) {
        	window.opener.resetShiftLength();
        }
        window.close();

<?php  } else if(isset($_GET['LEAVE']) && ($_GET['LEAVE'] == 'SUMMARY')) {
			$reqPath.="&LEAVE={$_GET['LEAVE']}";
?>
        window.opener.document.frmSelectEmployee.id.value = empNumber;
        window.opener.document.frmSelectEmployee.cmbEmpID.value = empName;
        window.close();

<?php  } else if(isset($_GET['PROJECT'])) {
			$reqPath.="&PROJECT=" . CommonFunctions::escapeHtml($_GET['PROJECT']);
?>
        window.opener.document.frmProjectAdmins.projAdminID.value = empNumber;
        window.opener.document.frmProjectAdmins.projAdminName.value = empName;
        window.close();

<?php  } else if(isset($_GET['reqcode'])) { ?>
        window.opener.document.frmEmp.txtRepEmpID.value = empNumber;
        window.opener.document.frmEmp.cmbRepEmpID.value = empName;
        window.close();

<?php  } else { ?>
		window.opener.document.standardView.action="../../lib/controllers/CentralController.php?id=" + empNumber + "&reqcode=<?php echo CommonFunctions::escapeHtml($_GET['reqcode'])?>";
        window.opener.document.standardView.submit();
		window.close();
<?php } ?>
	}

	function search() {
		if (document.standardView.loc_code.value == -1) {
			alert('<?php echo $lang_empview_SelectField; ?>');
			document.standardView.loc_code.Focus();
			return;
		};
		document.standardView.captureState.value = 'SearchMode';

		document.standardView.action="./emppop.php<?php echo $reqPath;?>"
		document.standardView.pageNO.value=1;
		document.standardView.submit();
	}
//]]>
</script>
</head>
<body style="padding:0 10px 0 0;">
    <div class="outerbox">
    <form name="standardView" method="post" action="" onsubmit="search();">
        <div class="mainHeading"><h2><?php echo $lang_empview_search; ?></h2></div>

        <input type="hidden" name="captureState" value="<?php echo isset($_POST['captureState'])?CommonFunctions::escapeHtml($_POST['captureState']):''?>"/>
        <input type="hidden" name="pageNO" value="<?php echo isset($_POST['pageNO'])?CommonFunctions::escapeHtml($_POST['pageNO']):'1'?>"/>
        <input type="hidden" name="empID" value=""/>

        <div class="searchbox">
            <label for="loc_code"><?php echo $lang_Common_Search;?></label>
            <select name="loc_code" id="loc_code">
                <?php
                    $optionCount = count($srchlist[0]);
                    for ($c = 0; $optionCount > $c; $c++) {
                        $selected = "";
                        if (isset($_POST['loc_code']) && ($_POST['loc_code'] == $srchlist[0][$c])) {
                            $selected = 'selected="selected"';
                        }
                       echo "<option $selected value='" . $srchlist[0][$c] ."'>" . $srchlist[1][$c] . "</option>";
                    }
                ?>
            </select>

            <input type="text" size="20" name="loc_name" id="loc_name"
                value="<?php echo isset($_POST['loc_name'])? CommonFunctions::escapeHtml($_POST['loc_name']):''?>" />
            <br class="clear"/>
            <input type="submit" class="plainbtn" name="btnSearch" style="margin:3px 3px 0 5px;"
                onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
                value="<?php echo $lang_Common_Search;?>" />
            <input type="button" class="plainbtn" onclick="clear_form();" name="clear" style="margin:3px 0 0 0;"
                onmouseover="this.className='plainbtn plainbtnhov'" onmouseout="this.className='plainbtn'"
                 value="<?php echo $lang_Common_Reset;?>" />
            <br class="clear"/>
        </div>

        <div class="actionbar">
            <div class="actionbuttons"></div>
            <div class="noresultsbar"><?php echo (!isset($emplist) || empty($emplist)) ? $lang_empview_norecorddisplay : '';?></div>
            <div class="pagingbar">
            <?php

                if (isset($_POST['captureState'])&& ($_POST['captureState']=="SearchMode")) {
                    $temp = $empviewcontroller ->countUnAssigned($_GET['reqcode'],$strName,$choice);
                } else {
                    $temp = $empviewcontroller -> countUnAssigned($_GET['reqcode']);
                }

                $commonFunc = new CommonFunctions();
                $pageStr = $commonFunc->printPageLinks($temp, $currentPage);
                $pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

                echo $pageStr;
            ?>
            </div>
            <br class="clear" />
        </div>
        <br class="clear" />

<!--  data table start -->
        <table cellpadding="0" cellspacing="0" class="data-table">
            <thead>
            <tr>
                <?php
                    $headings = array($lang_empview_employeeid, $lang_empview_employeename);
                    $sortUrlFormat = $_SERVER['PHP_SELF'] . str_replace('&', '&amp;', $reqPath) . "&amp;VIEW=MAIN&amp;sortField=%d&amp;sortOrder%d=%s";

                    for ($j = 0; $j < count($headings); $j++) {
                        if (!isset($_GET['sortOrder'.$j])) {
                            $_GET['sortOrder'.$j] = 'null';
                        }
                        $sortOrder = $_GET['sortOrder'.$j];
                        $nextSortOrder = getNextSortOrder($sortOrder);
                        $sortUrl = sprintf($sortUrlFormat, $j, $j, $nextSortOrder);
                ?>
                    <td scope="col">
                        <a href="<?php echo $sortUrl;?>" title="<?php echo nextSortOrderInWords($sortOrder);?>"
                            class="<?php echo $sortOrder;?>"><?php echo $headings[$j]?>
                        </a>
                    </td>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
<?php
            if ((isset($emplist)) && ($emplist !='')) {
                for ($j = 0; $j < count($emplist); $j++) {
                    $cssClass = ($j%2) ? 'even' : 'odd';
                    $empNum = $emplist[$j][0];
                    $empId = empty($emplist[$j][2]) ? $empNum : $emplist[$j][2];
                    $empName = $emplist[$j][1];
                    $onclick = "empSel(" . ltrim($empNum, '0') . ", '" . addslashes($empName) . "');";
?>
                <tr>
                    <td class="<?php echo $cssClass;?>">
                        <a href="#" onclick="<?php echo $onclick;?>"><?php echo CommonFunctions::escapeHtml($empId);?></a>
                    </td>
                    <td>
                        <a href="#" onclick="<?php echo $onclick;?>"><?php echo CommonFunctions::escapeHtml($empName);?></a>
                    </td>
                </tr>

<?php           }
            }
?>
            </tbody>
        </table>
<!-- data table -->
    </form>
    </div>
    <div class="pagingbar"><?php echo $pageStr;?></div>
    <br class="clear"/>

<script type="text/javascript">
    <!--
        if (document.getElementById && document.createElement) {
            roundBorder('outerbox');
        }
    -->
</script>
</body>
</html>
