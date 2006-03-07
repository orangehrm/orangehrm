<?
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software International Pvt. Ltd, http://www.hsenid.com

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

session_start();
if(!isset($_SESSION['fname'])) { 

	header("Location: ./relogin.htm");
	exit();
}

define('OpenSourceEIM', dirname(__FILE__));
require_once OpenSourceEIM . '/lib/Controllers/GenViewController.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( ' ' , 'ID' , 'Description' );

	$sysConst = new sysConf(); 
	$genviewcontroller = new GenViewController();
	
	$pageInfo = $genviewcontroller -> getPageID(trim($_GET['uniqcode']));
	$headingInfo = $genviewcontroller->getHeadingInfo($_GET['uniqcode']);
	
$currentPage = (isset($_POST['pageNO'])) ? (int)$_POST['pageNO'] : 1;

if (isset($_POST['captureState'])&& ($_POST['captureState']=="SearchMode"))
    {
    $choice=$_POST['loc_code'];
    $strName=trim($_POST['loc_name']);
    
    $list = $genviewcontroller ->getInfo($_GET['uniqcode'],$currentPage,$strName,$choice);
    }
else 
    $list = $genviewcontroller -> getInfo($_GET['uniqcode'],$currentPage);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
        opener.document.standardView.action="<?=$pageInfo?>.php?id=" + cntrl.title + "&sqlmode=addmode&uniqcode=<?=$_GET['uniqcode']?>&capturemode=updatemode&pageID=<?=$pageInfo?>";
        
		opener.document.standardView.submit();
		window.close();
	}
	
	function Search() {
		document.standardView.captureState.value = 'SearchMode';		
		document.standardView.action="./genpop.php?uniqcode=<?=$_GET['uniqcode']?>"
		document.standardView.pageNO.value=1;
		document.standardView.submit();
	}
	
</script>
<body>
<p> 
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'><tr><td valign='top'>
<form name="standardView" method="post">
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3> 
        <input type="hidden" name="captureState" value="<?=isset($_POST['captureState'])?$_POST['captureState']:''?>">
        <input type="hidden" name="pageNO" value="<?=isset($_POST['pageNO'])?$_POST['pageNO']:'1'?>">
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
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200" class="dataLabel"><slot>Search By:</slot>&nbsp;&nbsp;<slot>
                        <select name="loc_code">
<?                        for($c=0;count($srchlist[0])>$c;$c++)
								if(isset($_POST['loc_code']) && $_POST['loc_code']==$srchlist[0][$c])
								   echo "<option selected value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
								else
								   echo "<option value='" . $srchlist[0][$c] ."'>".$srchlist[1][$c] ."</option>";
?>								   
                        </select>
                      </slot></td>
                      <td width="200" class="dataLabel" noWrap><slot>Description</slot>&nbsp;&nbsp;<slot>
                        <input type=text size="20" name="loc_name" class=dataField  value="<?=isset($_POST['loc_name'])?$_POST['loc_name']:''?>">
                     </slot></td>

                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                    <td align="right" width="130" class="dataLabel"><input title="Search [Alt + S]" accessKey="S" class="button" type="button" name="btnSearch" value="Search" onClick="Search();"/>
                          <input title="Clear [Alt+K]" accessKey="K" onclick="clear_form();" class="button" type="button" name="clear" value=" Clear "/></td>

                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
			  <table border="0" width="100%">
			  <tr>
			  <td height="40" valign="bottom" align="right">
			  
<?
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
			
?> 
		</td>
		<td width="25"></td>
		</tr>
		</table>

              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <td width="50" NOWRAP class="listViewThS1" scope="col"></td>
						  <td scope="col" width="250" class="listViewThS1"><?=$headingInfo[0]?></td>
						  <td scope="col" width="400" class="listViewThS1"><?=$headingInfo[1]?></td>
                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

        <?
			if ((isset($list)) && ($list !='')) {
	 
			 for ($j=0; $j<count($list);$j++) {
			
		?>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
         <?		if(!($j%2)) { ?>
				  <td width="50"></td>
				  <td width="250"><a title="<?=$list[$j][0]?>" href="#" onClick="selItm(this)"><?=$list[$j][0]?></a></td>
		  		  <td width="400" ><?=$list[$j][1]?></td>
		<?		} else { ?>
				  <td bgcolor="#EEEEEE" width="50"></td>
				  <td bgcolor="#EEEEEE" width="250"><a title="<?=$list[$j][0]?>" href="#" onClick="selItm(this)"><?=$list[$j][0]?></a></td>
		  		  <td bgcolor="#EEEEEE" width="400" ><?=$list[$j][1]?></td>
		<?		}	?>
		  		  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>

         <? } 
        	  } else if ((isset($message)) && ($message =='')) {
        		
        		 $dispMessage = "No Records to Display !";
        		 echo '<font color="#FF0000" size="-1" face="Verdana, Arial, Helvetica, sans-serif">';
        		 echo $dispMessage;
        		 echo '</font>';
        	}
         
         ?> 

                <tr>
                  <td><img name="table_r3_c1" src="themes/beyondT/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="themes/beyondT/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="themes/beyondT/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
      
<!--  newtable -->

</form>
</body>
</html>
