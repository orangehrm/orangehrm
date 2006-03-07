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
require_once OpenSourceEIM . '/lib/Controllers/EmpViewController.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

$srchlist[0] = array( 0 , 1 , 2 );
$srchlist[1] = array( ' ' , 'ID' , 'Description' );

	$empviewcontroller = new EmpViewController();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

if (isset($_GET['reqcode'])) {
	
	$pageInfo = $empviewcontroller -> getPageID(trim($_GET['reqcode']));
 	$headingInfo = $empviewcontroller -> getHeadingInfo(trim($_GET['reqcode']));
    }
	
if (isset($_POST['delState'])&& ($_POST['delState']=="DeleteMode"))
    {
    $arrList[0]=$_POST['chkLocID'];

    $empviewcontroller->delParser(trim($_GET['reqcode']),$arrList);
    }

$currentPage = (isset($_POST['pageNO'])) ? (int)$_POST['pageNO'] : 1;
    
if (isset($_POST['captureState'])&& ($_POST['captureState']=="SearchMode"))
    {
    $choice=$_POST['loc_code'];
    $strName=trim($_POST['loc_name']);
    
    $emplist = $empviewcontroller -> getInfo(trim($_GET['reqcode']),$currentPage,$strName,$choice);
    }
else 
	$emplist = $empviewcontroller -> getInfo(trim($_GET['reqcode']),$currentPage);


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


<? if($_GET['reqcode']=='EMP') { ?>

	function returnAdd() {
	
		location.href = "<?=$pageInfo?>" + ".php?pageID=<?=$pageInfo?>&reqcode=<?=$_GET['reqcode']?>&capturemode=addmode";
		
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
			alert("Select At Least One Record To Delete");
		}		
	}
	
<? } else { ?>
	function returnAdd() {

        var popup=window.open('emppop.php?reqcode=<?=$_GET['reqcode']?>','Employees','height=450,width=400');
        if(!popup.opener) popup.opener=self;
	}

<? } ?>

	
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

	function clear_form() {
		document.standardView.loc_code.options[0].selected=true;
		document.standardView.loc_name.value='';
	}
		
</script>
<body>
<p> 
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'><tr><td valign='top'>
<form name="standardView" method="post" action="./empview.php?reqcode=<?=$_GET['reqcode']?>">
</td>
  <td width='100%'><h2> 
      <?=$headingInfo[0]?>
    </h2></td>
  <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td></tr>
</table></p>
</p> 
<p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="22%" nowrap><h3> 
        <input type="hidden" name="captureState" value="<?=isset($_POST['captureState'])?$_POST['captureState']:''?>">
        <input type="hidden" name="delState" value="">
        
        <input type="hidden" name="pageNO" value="<?=isset($_POST['pageNO'])?$_POST['pageNO']:'1'?>">
        <input type="hidden" name="empID" value="">

<?	if($locRights['add']) { ?>
        <img border="0" title="Add" onClick="returnAdd();" onmouseout="this.src='./themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_add_02.jpg';" src="./themes/beyondT/pictures/btn_add.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_add.jpg">
<?	}

 if($_GET['reqcode']=='EMP') { 

		if($locRights['delete']) { ?>
	        <img title="Delete" onclick="returnDelete();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
	<? 	} else { ?>
	        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
	<? 	}
 } 
 ?>
 
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
      <?
    
      
		if (isset($_GET['emplist'])) {
			$expString  = $_GET['emplist'];
			$expString = explode ("%",$expString);
			$length = sizeof($expString);
			for ($x=0; $x < $length; $x++) {		
				echo " " . $expString[$x];		
			}
		}		
		?>
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
                    <td align="right" width="180" class="dataLabel"><img title="Search" onClick="returnSearch();" onmouseout="this.src='./themes/beyondT/pictures/btn_search.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_search_02.jpg';" src="./themes/beyondT/pictures/btn_search.jpg">&nbsp;&nbsp;<img title="Clear" onclick="clear_form();" onmouseout="this.src='./themes/beyondT/pictures/btn_clear.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_clear_02.jpg';" src="./themes/beyondT/pictures/btn_clear.jpg"></td>

                  </table></td>
                  <td background="themes/beyondT/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table  border="0" cellpadding="5" cellspacing="0" class="">

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
    $temp = $empviewcontroller -> countList(trim($_GET['reqcode']),$strName,$choice);
else 
    $temp = $empviewcontroller -> countList(trim($_GET['reqcode']));
    
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
                  		<tr>
                  		  <td width="50">&nbsp;&nbsp;&nbsp;</td>
						  <td scope="col" width="250" class="listViewThS1">Employee Id</td>
						  <td scope="col" width="400" class="listViewThS1">Employee Name</td>
						 </tr>
        <?
			if ((isset($emplist)) && ($emplist !='')) {
	 
			 for ($j=0; $j<count($emplist);$j++) {

			 	$descField=$emplist[$j][1];
			 	
			 	if($sysConst->viewDescLen <= strlen($descField)) {
			 	   $descField = substr($descField,0,$sysConst->viewDescLen);
			 	   $descField .= "....";
			 	}
			 	
		?>
					<tr>             
<?		if(!($j%2)) { ?>
<? 			if($_GET['reqcode']=='EMP') { ?>
                  <td width="50"><input type="checkbox" class="checkbox" name="chkLocID[]" value="<?=$emplist[$j][0]?>"></td>
                  <? } else { ?>
                  <td width="50"></td>
<? } ?>                  
				  <td width="250"><a href="<?=$pageInfo?>.php?id=<?=$emplist[$j][0]?>&capturemode=updatemode&reqcode=<?=$_GET['reqcode']?>&pageID=<?=$pageInfo?>" class="listViewTdLinkS1"><?=$emplist[$j][0]?></a></td>

		  		  <td width="400" ><?=$descField?></td>
<?			} else { ?>
<? 			if($_GET['reqcode']=='EMP') { ?>
                  <td bgcolor="#EEEEEE" width="50"><input type="checkbox" class="checkbox" name="chkLocID[]" value="<?=$emplist[$j][0]?>"></td>
                  <? } else { ?>
                  <td bgcolor="#EEEEEE" width="50"></td>
<? } ?>                  
				  <td bgcolor="#EEEEEE" width="250"><a href="<?=$pageInfo?>.php?id=<?=$emplist[$j][0]?>&capturemode=updatemode&reqcode=<?=$_GET['reqcode']?>&pageID=<?=$pageInfo?>" class="listViewTdLinkS1"><?=$emplist[$j][0]?></a></td>

		  		  <td bgcolor="#EEEEEE" width="400" ><?=$descField?></td>
<?			}	?>
			</tr>
         <? } 
        	  } else if ((isset($emplist)) && ($emplist =='')) {
        		
        		 $dispMessage = "No Records to Display !";
        		 echo "<tr><td>";
        		 echo '<font color="#FF0000" size="-1" face="Verdana, Arial, Helvetica, sans-serif">';
        		 echo $dispMessage;
        		 echo '</font>';
        		 echo "</tr></td>";
        	}
         
         ?> 
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


      
<!--  newtable -->

</form>
</body>
</html>
