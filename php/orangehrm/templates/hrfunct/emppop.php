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

require_once ROOT_PATH . '/language/default/lang_default_full.php';

$lan = new Language();

require_once($lan->getLangPath("full.php"));

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

	function SortOrderInWords($SortOrder) {
		if ($SortOrder == 'ASC') {
			return 'Ascending';
		} else {
			return 'Descending';
		}
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $lang_emppop_title; ?></title>
<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<script type="text/javascript" src="../../scripts/archive.js"></script>
<script>
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

	function empSel(cntrl) {

<?php
		$reqPath="?reqcode=".$_GET['reqcode'];
		if(isset($_GET['USR'])) {
			$reqPath.="&USR={$_GET['USR']}";
?>
		getElementByName("cmbUserEmpID", window.opener.document).value = cntrl.name;
		getElementByName("txtUserEmpID", window.opener.document).value = cntrl.title;
        window.close();

<?php   } else if(isset($_GET['REPORT'])) {
			$reqPath.="&REPORT={$_GET['REPORT']}";
?>
        window.opener.document.frmEmpRepTo.txtRepEmpID.value = cntrl.name;
        window.opener.document.frmEmpRepTo.cmbRepEmpID.value = cntrl.name;
        window.close();

<?php   } else if(isset($_GET['LEAVE']) && ($_GET['LEAVE'] == 'LEAVE')) {
			$reqPath.="&LEAVE={$_GET['LEAVE']}";
?>
        window.opener.document.frmLeaveApp.cmbEmployeeId.value = cntrl.name;
        window.opener.document.frmLeaveApp.txtEmployeeId.value = cntrl.title;
        window.close();

<?php  } else if(isset($_GET['LEAVE']) && ($_GET['LEAVE'] == 'SUMMARY')) {
			$reqPath.="&LEAVE={$_GET['LEAVE']}";
?>
        window.opener.document.frmSelectEmployee.id.value = cntrl.name;
        window.opener.document.frmSelectEmployee.cmbEmpID.value = cntrl.title;
        window.close();

<?php  } else if(isset($_GET['PROJECT'])) {
			$reqPath.="&PROJECT={$_GET['PROJECT']}";
?>
        window.opener.document.frmProjectAdmins.projAdminID.value = cntrl.name;
        window.opener.document.frmProjectAdmins.projAdminName.value = cntrl.title;
        window.close();

<?php  } else if(isset($_GET['reqcode'])) { ?>
        window.opener.document.frmEmp.txtRepEmpID.value = cntrl.name;
        window.opener.document.frmEmp.cmbRepEmpID.value = cntrl.title;
        window.close();

<?php  } else { ?>
		window.opener.document.standardView.action="../../lib/controllers/CentralController.php?id=" + cntrl.title + "&reqcode=<?php echo $_GET['reqcode']?>";
        window.opener.document.standardView.submit();
		window.close();
<?php } ?>
	}

	function Search() {
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
</script>
<body style="padding-left:4; padding-right:4;">
<p>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'><tr><td valign='top'>
<form name="standardView" method="post">
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3>
        <input type="hidden" name="captureState" value="<?php echo isset($_POST['captureState'])?$_POST['captureState']:''?>">
        <input type="hidden" name="pageNO" value="<?php echo isset($_POST['pageNO'])?$_POST['pageNO']:'1'?>">
        <input type="hidden" name="empID" value="">

      </h3></td>
    <td width='78%'><IMG height='1' width='1' src='../../pictures/blank.gif' alt=''></td>
  </tr>
</table>
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3><?php echo $lang_empview_search; ?></h3></td>
    <td width='78%' align="right"><img height='1' width='1' src='../../pictures/blank.gif' alt=''>
     <font color="#FF0000" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
      &nbsp;&nbsp;&nbsp;&nbsp; </font> </td>
  </tr>
</table>

<!--  newtable -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="" width="100%">
                    <tr>
                      <td width="200" class="dataLabel"><slot><?php echo $lang_empview_searchby; ?></slot>&nbsp;&nbsp;<slot>
                        <select name="loc_code">
<?php                        for($c=0;count($srchlist[0])>$c;$c++)
								if(isset($_POST['loc_code']) && $_POST['loc_code']==$srchlist[0][$c])
								   echo "<option selected value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
								else
								   echo "<option value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
?>
                        </select>
                      </slot></td>
                      <td width="200" class="dataLabel" noWrap><slot><?php echo $lang_empview_description; ?></slot>&nbsp;&nbsp;<slot>
                        <input type=text size="20" name="loc_name" class=dataField  value="<?php echo isset($_POST['loc_name'])? stripslashes($_POST['loc_name']):''?>">
                     </slot></td>

                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                    <td align="right" width="130" class="dataLabel"><input title="Search [Alt + S]" accessKey="S" class="button" type="button" name="btnSearch" value="<?php echo $lang_empview_search; ?>" onClick="Search();"/>
                          <input title="Clear [Alt+K]" accessKey="K" onClick="clear_form();" class="button" type="button" name="clear" value="<?php echo $lang_compstruct_clear; ?>"/></td>

                  </table></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
			  <table border="0" width="100%">
			  <tr>
			  <td height="40" valign="bottom" align="right">

<?php

if (isset($_POST['captureState'])&& ($_POST['captureState']=="SearchMode"))
    $temp = $empviewcontroller ->countUnAssigned($_GET['reqcode'],$strName,$choice);
else
    $temp = $empviewcontroller -> countUnAssigned($_GET['reqcode']);

$commonFunc = new CommonFunctions();
$pageStr = $commonFunc->printPageLinks($temp, $currentPage);
$pageStr = preg_replace(array('/#first/', '/#previous/', '/#next/', '/#last/'), array($lang_empview_first, $lang_empview_previous, $lang_empview_next, $lang_empview_last), $pageStr);

echo $pageStr;

?>
		</td>
		<td width="25"></td>
		</tr>
		</table>

              <table bordeir="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td ><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r1_c2.gif"></td>
                  <td background="../../themes/beyondT/pictures/table_r1_c2.gif"></td>
                  <td background="../../themes/beyondT/pictures/table_r1_c3.gif"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr  valign="top" height="25">
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif" ><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="20" border="0" alt=""></td>
						  <?php
						  	$j=0;
						  	if (!isset($_GET['sortOrder'.$j])) {
								$_GET['sortOrder'.$j]='null';
							};
						  ?>
						  <td class="listViewThS1" width="180px"><a href="<?php echo $_SERVER['PHP_SELF'].$reqPath; ?>&VIEW=MAIN&sortField=<?php echo $j?>&sortOrder<?php echo $j?>=<?php echo getNextSortOrder($_GET['sortOrder'.$j])?>" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($_GET['sortOrder'.$j]))?> order"><?php echo $lang_empview_employeeid; ?></a> <img src="../../themes/beyondT/icons/<?php echo $_GET['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt=""></td>
						  <?php
						  	$j=1;
							if (!isset($_GET['sortOrder'.$j])) {
								$_GET['sortOrder'.$j]='null';
							};
						  ?>
						  <td class="listViewThS1" width="180px"><a href="<?php echo $_SERVER['PHP_SELF'].$reqPath; ?>&VIEW=MAIN&sortField=<?php echo $j?>&sortOrder<?php echo $j?>=<?php echo getNextSortOrder($_GET['sortOrder'.$j])?>" title="Sort in <?php echo SortOrderInWords(getNextSortOrder($_GET['sortOrder'.$j]))?> order"><?php echo $lang_empview_employeename; ?></a> <img src="../../themes/beyondT/icons/<?php echo $_GET['sortOrder'.$j]?>.png" width="8" height="10" border="0" alt="" ></td>
                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

        <?php
			if ((isset($emplist)) && ($emplist !='')) {

			 for ($j=0; $j<count($emplist);$j++) {

		?>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif" height="20"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
         <?php		if(!($j%2)) { ?>
				  <td >&nbsp;&nbsp;<a title="<?php echo $emplist[$j][1]?>" name="<?php echo $emplist[$j][0]; ?>" href="" onClick="empSel(this)"><?php echo (!empty($emplist[$j][2]))?$emplist[$j][2]:$emplist[$j][0]?></a></td>
		  		  <td >&nbsp;&nbsp;<?php echo $emplist[$j][1]?></td>
		<?php		} else { ?>
				  <td bgcolor="#EEEEEE" >&nbsp;&nbsp;<a title="<?php echo $emplist[$j][1]?>" name="<?php echo $emplist[$j][0]; ?>" href="" onClick="empSel(this)"><?php echo (!empty($emplist[$j][2]))?$emplist[$j][2]:$emplist[$j][0]?></a></td>
		  		  <td bgcolor="#EEEEEE" >&nbsp;&nbsp;<?php echo $emplist[$j][1]?></td>
		<?php		}	?>

                  <td background="../../themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>

                </tr>

         <?php }
        	  } else if ((isset($message)) && ($message =='')) { ?>

			   <tr>
			   	<td></td>
				<td>
		<?php
        		 $dispMessage = $lang_empview_norecorddisplay;
        		 echo '<font color="#FF0000" size="-1" face="Verdana, Arial, Helvetica, sans-serif">';
        		 echo $dispMessage;
        		 echo '</font>';
        	}

         ?>
		 		</td>
			</tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td background="../../themes/beyondT/pictures/table_r3_c2.gif"></td>
                  <td ><img src="../../themes/beyondT/pictures/table_r3_c3.gif" border="0" alt=""></td>
                </tr>
      </table>
<!--  newtable -->

</form>

</body>
</html>
