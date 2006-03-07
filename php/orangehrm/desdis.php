<?
/*
OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures 
all the essential functionalities required for any enterprise. 
Copyright (C) 2006 hSenid Software, http://www.hsenid.com

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


define('OpenSourceEIM', dirname(__FILE__) );
require_once OpenSourceEIM . '/lib/Models/eimadmin/Designations.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/DesDis.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$parent_designation = new Designations();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$lastRecord = $parent_designation ->getLastRecord();
	$message = $parent_designation ->filterDesignations($_GET['id']);

if(isset($_POST['KRA'])&&($_POST['KRA']=="SEL"))
    {
      $parent_desdis = new  DesDescription();
      
      $addlist = $_POST['chkadd'];
      
      for($c=0; $c < count($addlist) ; $c++)
        if($addlist[$c]!=NULL)
          {
          $parent_desdis -> setDesId($_GET['id']);
          $parent_desdis -> setJDKraId($addlist[$c]);
          $parent_desdis -> setJDKPI("");
          $parent_desdis -> addJDKPI();
          }
    }

if(isset($_POST['KRA'])&&($_POST['KRA']=="SAV"))
    {
      $parent_desdis = new  DesDescription();
      $parent_desdis -> setDesId($_GET['id']);
      $parent_desdis -> setJDKraId($_POST['CODE']);
      $parent_desdis -> setJDKPI($_POST['txtKPI']);
      $parent_desdis -> updateJDKPI();

    }


if(isset($_POST['KRA'])&&($_POST['KRA']=="DEL"))
    {
      $parent_desdis = new DesDescription();
      $arr[0]=$_POST['chkdel'];
      $size = count($arr[0]);
      
      for($c=0 ; $size > $c ; $c++)
          if($arr[0][$c]!=NULL)
             $arr[1][$c]=$_GET['id'];

      $parent_desdis -> delJDKPI($arr);
    }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<script language="JavaScript">
function addKRA()
{
    document.frmDesignations.KRA.value="ADD";
    document.frmDesignations.submit();
}

function parseKRA()
{
      var check = 0;
		with (document.frmKRA) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
            {
              alert("Select atleast one KRA check box to assign");
              return;
            }

    //alert(cntrl.value);
    document.frmKRA.KRA.value="SEL";
    document.frmKRA.submit();
}

function saveKPI()
{
    //alert(cntrl.value);
    document.frmKRA.KRA.value="SAV";
    document.frmKRA.submit();
}

	function goBack() {
		location.href = "view.php?uniqcode=<?=$_GET['uniqcode']?>";
	}

function mout() {
	if(document.Edit.title=='Save') 
		document.Edit.src='./themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.Edit.title=='Save') 
		document.Edit.src='./themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='./themes/beyondT/pictures/btn_edit_02.jpg'; 
}
	
function edit()
{
	if(document.Edit.title=='Save') {
		saveKPI();
		return;
	}
	
	var frm=document.frmKRA;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}
	
function delKRA()
{
      var check = 0;
		with (document.frmDesignations) {
			for (var i=0; i < elements.length; i++) {
				if ((elements[i].type == 'checkbox') && (elements[i].checked == true)){
					check = 1;
				}
			}
        }

        if(check==0)
            {
              alert("Select atleast one assigned KRA check box");
              return;
            }


    //alert(cntrl.value);
    document.frmDesignations.KRA.value="DEL";
    document.frmDesignations.submit();
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="./themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("./themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td width='100%'><h2>Designation Description: Designation Profile</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p> 
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmDesignations" method="post" action="./desdis.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>&id=<?=$_GET['id']?>">
<input type="hidden" name="pageID" value="">
  <tr> 
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="KRA" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
      </font> </td>
  </tr><td width="177">
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
						    <td>Designation</td>
						  	  <td> <strong><?=$message[0][1]?></strong></td>
						  </tr>
						  <tr>
						    <td>Review Date</td>
						    <?
						    $field = explode(" ",$message[0][4]);
						    ?>
						    <td><strong><?=$field[0]?></strong></td>
						  </tr>
						  <tr>
						    <td>Next Upgrade Level</td>
						    <td><strong><?=$message[0][5]?></strong></td>
						  </tr>
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


<?
$parent_desdis = new DesDescription();

$rset = $parent_desdis ->getAssigned($_GET['id']);
?>
<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>
    
    <td width='100%'><h3>Assigned Key Result Areas</h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr><td>
<? if($locRights['add']) { ?>
  <img onClick="addKRA();" onmouseout="this.src='./themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_add_02.jpg';" src="./themes/beyondT/pictures/btn_add.jpg">
<? } else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_add.jpg">
<? } 

	if($locRights['delete']) { ?>
		<img onClick="delKRA();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
  <? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
		</td>
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
<?						if($rset) {	?>
						<tr>
						         <td></td>
						         <td><strong>JD Category</strong></td>
						         <td><strong>JD Type</strong></td>
						         <td><strong>JD KRA</strong></td>
						</tr>
						<?
						
						    for($c=0;$c < count($rset); $c++)
						        {
						        echo '<tr>';
						            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][2] ."'></td>";
						            echo '<td title="' . $rset[$c][3] .'">' . $rset[$c][0] .'</td>';
						            echo '<td title="' . $rset[$c][4] .'">' . $rset[$c][1] .'</td>';
						            echo '<td title="' . $rset[$c][5] .'">' . $rset[$c][2] .'</td>';
						            if($rset[$c][6]!="")
						                echo '<td><a href="./desdis.php?pageID=' . $_GET['pageID'] . '&uniqcode=' . $_GET['uniqcode'] . '&id=' . $_GET['id'].'&KPI='. $rset[$c][2] .'">View</a></td>';
						            else
						                echo '<td><a href="./desdis.php?pageID=' . $_GET['pageID'] . '&uniqcode=' . $_GET['uniqcode'] . '&id=' . $_GET['id'].'&KPI='. $rset[$c][2] .'">Add</a></td>';
						        echo '</tr>';
						      }
						     } else {
						        echo '<tr>';
						            echo '<td> No KRAs Assigned </td>';
						        echo '</tr>';
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

</form>
<form name="frmKRA" method="post" action="./desdis.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>&id=<?=$_GET['id']?>" >			
 <input type="hidden" name="KRA" value="">

<?
if(isset($_GET['KPI']))
    {
      $parent_desdis = new  DesDescription();

          $arr[1]=$_GET['id'];
          $arr[0]=$_GET['KPI'];
          $message=$parent_desdis -> filterJDKPI($arr);
?>
    <input type="hidden" name="CODE" value="<?=$_GET['KPI']?>">
	  <br>
	  	  <br>
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
					         <td><strong>KPI</strong></td>
					  	  <td align="left" valign="top"> <textarea name='txtKPI' disabled rows="3" tabindex='3' cols="30"><?=$message[0][2]?></textarea>
					    </td>
					        </tr>
					  <tr><td></td><td align="right" width="100%">
<?				if($locRights['add']) { ?>
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?				} else { ?>
			        <img src="./themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?				} ?>
						</td></tr>

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

<?
    }



if(isset($_POST['KRA'])&&($_POST['KRA']=="ADD"))
    {
      ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'>&nbsp; </td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>
    <td><h3>Key Result Areas</h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td><img onClick="parseKRA();" onmouseout="this.src='./themes/beyondT/pictures/btn_add.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_add_02.jpg';" src="./themes/beyondT/pictures/btn_add.jpg"></td>
  </tr>
</table>				
			   <input type="hidden" name="dummy">
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
								         <td></td>
								         <td><strong>JD Category</strong></td>
								         <td><strong>JD Type</strong></td>
								         <td><strong>JD KRA</strong></td>
								</tr>
								<?
								$rset=$parent_desdis ->getGrouping($_GET['id']);
								
								    for($c=0;$rset && $c < count($rset); $c++)
								        {
								        echo '<tr>';
								            echo "<td><input type='checkbox' class='checkbox' name='chkadd[]' value='" . $rset[$c][2] ."'></td>";
								            echo '<td title="' . $rset[$c][3] .'">' . $rset[$c][0] .'</td>';
								            echo '<td title="' . $rset[$c][4] .'">' . $rset[$c][1] .'</td>';
								            echo '<td title="' . $rset[$c][5] .'">' . $rset[$c][2] .'</td>';
								            //echo '<td><input type="button" class="button" value="Add KRA" onclick="parseKRA(' .$rset[$c][2] .');"></td>';
								        echo '</tr>';
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

<?    }
?>
</form>
</body>
</html>

