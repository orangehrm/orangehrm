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
 *
 */

session_start();
if(!isset($_SESSION['fname'])) {

	header("Location: ./relogin.htm");
	exit();
}

define('ROOT_PATH', dirname(__FILE__));
require_once ROOT_PATH . '/lib/controllers/GenViewController.php';
require_once ROOT_PATH . '/lib/confs/sysConf.php';
require_once ROOT_PATH . '/lib/common/CommonFunctions.php';

$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( '--Select--' , 'ID' , 'Description' );

	$sysConst = new sysConf();
	$genviewcontroller = new GenViewController();

	$headingInfo = $genviewcontroller->getHeadingInfo($_GET['uniqcode']);

$currentPage = (isset($_POST['pageNO'])) ? (int)$_POST['pageNO'] : 1;

if (isset($_POST['captureState'])&& ($_POST['captureState']=="SearchMode"))
    {
    $choice=$_POST['loc_code'];
    $strName=trim($_POST['loc_name']);

    $list = $genviewcontroller ->getInfo($_GET['uniqcode'],$currentPage,$strName,$choice);
    }
else {
    $list = $genviewcontroller -> getInfo($_GET['uniqcode'],$currentPage);
}

$styleSheet = CommonFunctions::getTheme();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<link href="./themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/<?php echo $styleSheet;?>/css/style.css"); </style>
<title>Un Assigned <?php echo $headingInfo[3]?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<script>
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

	function selItm(cntrl) {
        opener.document.standardView.action="./CentralController.php?id=" + cntrl.title + "&uniqcode=<?php echo $_GET['uniqcode']?>";

		opener.document.standardView.submit();
		window.close();
	}

	function Search() {
		if (document.standardView.loc_code.value == 0) {
			alert('<?php echo $lang_empview_SelectField;?>');
			document.standardView.loc_code.Focus();
			return;
		};
		document.standardView.captureState.value = 'SearchMode';
		document.standardView.action="./genpop.php?uniqcode=<?php echo $_GET['uniqcode']?>"
		document.standardView.pageNO.value=1;
		document.standardView.submit();
	}

	function clear_form() {
		document.standardView.loc_code.options[0].selected=true;
		document.standardView.loc_name.value='';
	}

</script>
<body>
<p>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'><tr><td valign='top'>
<form name="standardView" method="post">
</td><td width="100%"><h2>Un Assigned <?php echo $headingInfo[3]?>
    </h2></td>
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3>
        <input type="hidden" name="captureState" value="<?php echo isset($_POST['captureState'])?$_POST['captureState']:''?>">
        <input type="hidden" name="pageNO" value="<?php echo isset($_POST['pageNO'])?$_POST['pageNO']:'1'?>">
        <input type="hidden" name="empID" value="">

      </h3></td>
    <td width='78%'><IMG height='1' width='1' src='./pictures/blank.gif' alt=''></td>
  </tr>
</table>
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3>Search</h3></td>
    <td width='78%' align="right"><IMG height='1' width='1' src='./pictures/blank.gif' alt=''>
     <font color="#FF0000" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
      &nbsp;&nbsp;&nbsp;&nbsp; </font> </td>
  </tr>
</table>

<!--  newtable -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200" class="dataLabel"><slot>Search By:</slot>&nbsp;&nbsp;<slot>
                        <select style="z-index: 99;" name="loc_code">
<?php                        for($c=0;count($srchlist[0])>$c;$c++)
								if(isset($_POST['loc_code']) && $_POST['loc_code']==$srchlist[0][$c])
								   echo "<option selected value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
								else
								   echo "<option value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
?>
                        </select>
                      </slot></td>
                      <td width="200" class="dataLabel" noWrap><slot>Description</slot>&nbsp;&nbsp;<slot>
                        <input type=text size="20" name="loc_name" class=dataField  value="<?php echo isset($_POST['loc_name'])? stripslashes($_POST['loc_name']):''?>">
                     </slot></td>
                    <td align="right" width="180" class="dataLabel"><img title="Search" onClick="Search();" onMouseOut="this.src='./themes/beyondT/pictures/btn_search.gif';" onMouseOver="this.src='./themes/beyondT/pictures/btn_search_02.gif';" src="./themes/beyondT/pictures/btn_search.gif">&nbsp;&nbsp;<img title="Clear" onClick="clear_form();" onMouseOut="this.src='./themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='./themes/beyondT/pictures/btn_clear_02.gif';" src="./themes/beyondT/pictures/btn_clear.gif"></td>

                  </table></td>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">

                  </table></td>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

                <tr>
                  <td><img name="table_r3_c1" src="themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
			  <table border="0" width="100%">
			  <tr>
			  <td height="40" valign="bottom" align="right">

<?php
if (isset($_POST['captureState'])&& ($_POST['captureState']=="SearchMode"))
    $temp = $genviewcontroller -> countList($_GET['uniqcode'],$strName,$choice);
else
    $temp = $genviewcontroller -> countList($_GET['uniqcode']);

if($temp)
    $recCount=$temp;
else
	$recCount=0;

	$noPages=(int)($recCount/$sysConst->itemsPerPage);

	if($recCount%$sysConst->itemsPerPage)
	   $noPages++;
	echo $noPages;
	if ($noPages > 1) {

		if($currentPage==1)
			echo "<font color='Gray'>Previous</font>";
		else
    		echo "<a href='#' onClick='prevPage()'>Previous</a>";

    	echo "  ";

		for( $c = 1 ; $noPages >= $c ; $c++) {
	    	if($c == $currentPage)
				echo "<font color='Gray'>" .$c. "</font>";
			else
	    		echo "<a href='#' onClick='chgPage(" .$c. ")'>" .$c. "</a>";

	    	echo "  ";
		}

		if($currentPage == $noPages || $noPages==0)
			echo "<font color='Gray'>Next</font>";
		else
    		echo "<a href='#' onClick='nextPage()'>Next</a>";
	};

?>
		</td>
		<td width="25"></td>
		</tr>
		</table>

              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <td width="50" NOWRAP class="listViewThS1" scope="col"></td>
						  <td scope="col" width="250" class="listViewThS1"><?php echo $headingInfo[0]?></td>
						  <td scope="col" width="400" class="listViewThS1"><?php echo $headingInfo[1]?></td>
                  </table></td>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

        <?php
			if ((isset($list)) && ($list !='')) {

			 for ($j=0; $j<count($list);$j++) {

		?>
                <tr>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
         <?php		if(!($j%2)) { ?>
				  <td width="50"></td>
				  <td width="250"><a title="<?php echo $list[$j][0]?>" href="#" onClick="selItm(this)"><?php echo $list[$j][0]?></a></td>
		  		  <td width="400" ><?php echo $list[$j][1]?></td>
		<?php		} else { ?>
				  <td bgcolor="#EEEEEE" width="50"></td>
				  <td bgcolor="#EEEEEE" width="250"><a title="<?php echo $list[$j][0]?>" href="#" onClick="selItm(this)"><?php echo $list[$j][0]?></a></td>
		  		  <td bgcolor="#EEEEEE" width="400" ><?php echo $list[$j][1]?></td>
		<?php		}	?>
		  		  </table></td>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

         <?php }
        	  } else if ((isset($message)) && ($message =='')) {

        		 $dispMessage = "No Records Found";
        		 echo '<font color="#FF0000" size="-1" face="Verdana, Arial, Helvetica, sans-serif">';
        		 echo $dispMessage;
        		 echo '</font>';
        	}

         ?>

                <tr>
                  <td><img name="table_r3_c1" src="themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>

<!--  newtable -->

</form>
</body>
</html>
