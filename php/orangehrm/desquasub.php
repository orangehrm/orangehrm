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
require_once OpenSourceEIM . '/lib/Models/eimadmin/Designations.php';
require_once OpenSourceEIM . '/lib/Models/eimadmin/DesQuaSub.php';
require_once OpenSourceEIM . '/lib/Confs/sysConf.php';

	$parent_designation = new Designations();
	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

	$lastRecord = $parent_designation ->getLastRecord();
	$message = $parent_designation ->filterDesignations($_GET['id']);

$parent_desqsub = new DesQualSubject();

if(isset($_POST['STAT']) && $_POST['STAT']=="ADD")
    {
      $parent_desqsub->setDesId($_GET['id']);
      $parent_desqsub->setSubId($_POST['cmbSub']);
      $parent_desqsub->setQualId($_GET['QUA']);
      $parent_desqsub->setRatGrd($_POST['cmbRatGrd']);
      $parent_desqsub->addQuaSub();
    }

if(isset($_POST['STAT']) && $_POST['STAT']=="EDIT")
    {
      $parent_desqsub->setDesId($_GET['id']);
      $parent_desqsub->setSubId($_POST['cmbSub']);
      $parent_desqsub->setQualId($_GET['QUA']);
      $parent_desqsub->setRatGrd($_POST['cmbRatGrd']);
      $parent_desqsub->updateQuaSub();
    }

if(isset($_POST['STAT'])&&($_POST['STAT']=="DEL"))
    {
      $arr[1]=$_POST['chkdel'];
      $size = count($arr[1]);
      for($c=0 ; $size > $c ; $c++)
          if($arr[1][$c]!=NULL)
            {
             $arr[0][$c]=$_GET['id'];
             $arr[2][$c]=$_GET['QUA'];
             }

      $parent_desqsub -> delQuaSub($arr);
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<script language="JavaScript">

function addSUB()
{
	if(document.frmDesignations.cmbSub.value=='0') {
		alert("Field should be selected");
		document.frmDesignations.cmbSub.focus();
		return;
	}

	if(document.frmDesignations.cmbRatGrd.value=='0') {
		alert("Field should be selected");
		document.frmDesignations.cmbRatGrd.focus();
		return;
	}

	document.frmDesignations.STAT.value="ADD";
  document.frmDesignations.submit();
}

function editSUB()
{
  document.frmDesignations.STAT.value="EDIT";
  document.frmDesignations.submit();
}

	function goBack() {
		location.href = "desqua.php?id=<?=$_GET['id']?>&sqlmode=addmode&uniqcode=<?=$_GET['uniqcode']?>&capturemode=updatemode&pageID=./desqua";
		
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
		editSUB();
		return;
	}
	
	var frm=document.frmDesignations;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="./themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}

function delSUB()
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
              alert("Selct atleast one check box");
              return;
            }


    //alert(cntrl.value);
    document.frmDesignations.STAT.value="DEL";
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
    <td valign='top'> &nbsp;</td>
    <td width='100%'><h2>Designation Qualification Subjects: Designation Profile</h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmDesignations" method="post" action="./desquasub.php?pageID=<?=$_GET['pageID']?>&uniqcode=<?=$_GET['uniqcode']?>&id=<?=$_GET['id']?>&QUA=<?=$_GET['QUA']?>">
<input type="hidden" name="pageID" value="">
  <tr>
    <td height="27" valign='top'> <p><img title="Back" onmouseout="this.src='./themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_back_02.jpg';"  src="./themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
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
							    <td height="25" valign="top"><strong>Designation</strong></td>
							  	  <td align="left" valign="top"> <input type="text" readonly="true" name='txtDesignationsDesc' value="<?=$message[0][1]?>">
							    </td>
							  </tr>
							  <tr>
							    <td height="25" valign="top"><strong>Review Date</strong></td>
							    <td align="left" valign="top"> <input type="text" readonly="true" name='txtRevDat' value=<?=$message[0][4]?>>
							    </td>
							  </tr>
							  <tr>
							    <td height="25" valign="middle"><strong>Next Upgrade Level</strong></td>
							    <td valign=""><input type="text" readonly="true" value="<?=$message[0][5]?>">
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
if(isset($_GET['SUB']))
{
    $arr[0]=$_GET['id'];
    $arr[1]=$_GET['SUB'];
    $arr[2]=$_GET['QUA'];
    $edit=$parent_desqsub->filterQuaSub($arr);
?>
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
						             <td><strong>Subject</strong></td>
						             <td><input type="hidden" name="cmbSub" value="<?=$edit[0][1]?>"><?=$edit[0][1]?></td>
						    </tr>
						    <tr>
						             <td><strong>Rating Grade</strong></td>
						             <td><select disabled name="cmbRatGrd">
						             <?
						             $gradlist=$parent_desqsub->getRatGrds($_GET['QUA']);
						             for($c=0;count($gradlist)> $c; $c++)
						                 if($edit[0][3]==$gradlist[$c])
						                    echo '<option selected value=' . $gradlist[$c][0] . '>' .$gradlist[$c][1]. '</option>';
						                 else
						                    echo '<option value=' . $gradlist[$c][0] . '>' . $gradlist[$c][1]. '</option>';
						             ?>
						             </select></td>
						    </tr>
					  <tr><td></td><td align="right" width="100%">
<?				if($locRights['edit']) { ?>
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
} else { ?>
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
							         <td><strong>Subject</strong></td>
							         <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbSub">
							         		<option value="0">-Select Subject-</option>
							            <?
							            $sublist=$parent_desqsub->getSubjects($_GET['id'],$_GET['QUA']);
							            for($c=0;count($sublist)>$c;$c++)
							                echo '<option value=' . $sublist[$c][0] . '>' . $sublist[$c][1] . '</option>';
							            ?>
							         </select></td>
							</tr>
							<tr>
							         <td><strong>Rating Grade</strong></td>
							         <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbRatGrd">
							         		<option value="0">-Select Rat. Grade-</option>
							             <?
							             $gradlist=$parent_desqsub->getRatGrds($_GET['QUA']);
							             for($c=0;count($gradlist)> $c; $c++)
							                    echo '<option value=' . $gradlist[$c][0] . '>' .$gradlist[$c][1]. '</option>';
							             ?>
							         </select></td>
							</tr>
					  <tr><td></td><td align="right" width="100%">
<?				if($locRights['add']) { ?>
					  <img onClick="addSUB();" onmouseout="this.src='./themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_save_02.jpg';" src="./themes/beyondT/pictures/btn_save.jpg">
<?				} else { ?>
			        <img src="./themes/beyondT/pictures/btn_save.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
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

<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'> &nbsp;</td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3>Assigned Subjects </h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>

<?	if($locRights['delete']) { ?>
        <img title="Delete" onclick="delSUB();" onmouseout="this.src='./themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='./themes/beyondT/pictures/btn_delete_02.jpg';" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="./themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>

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
						         <td><strong>Subject</strong></td>
						         <td><strong>Rating Code</strong></td>
						</tr>
						<?
						$rset = $parent_desqsub ->getAssQuaSub($_GET['id']);
						
						    for($c=0; $rset && $c < count($rset); $c++)
						        {
						        echo '<tr>';
						            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."'></td>";
						            echo "<td><a href='./desquasub.php?pageID=" . $_GET['pageID'] . "&uniqcode=" . $_GET['uniqcode'] . "&id=" . $_GET['id']. "&QUA=" .$_GET['QUA']. "&SUB=" . $rset[$c][1] . "'>" . $rset[$c][1] . "</a></td>";
						            echo '<td>' . $rset[$c][3] .'</td>';
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
</body>
</html>
