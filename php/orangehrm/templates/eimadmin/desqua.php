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

require_once ROOT_PATH . '/lib/confs/sysConf.php';

	$sysConst = new sysConf(); 
	$locRights=$_SESSION['localRights'];

$statlist = array('First Class'=>'FIRSTCLS','Second Class Upr.'=>'SECONDCLSUPR','Second Class Lwr.'=>'SECONDCLSLWR');

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<script language="JavaScript">

function addQUA()
{
	if(document.frmDesignations.cmbQual.value=='0') {
		alert("Field should be selected");
		document.frmDesignations.cmbQual.focus();
		return;
	}

	if(document.frmDesignations.cmbStat.value=='0') {
		alert("Field should be selected");
		document.frmDesignations.cmbStat.focus();
		return;
	}
	
  document.frmDesignations.STAT.value="ADD";
  document.frmDesignations.submit();
}

function editQUA()
{
  document.frmDesignations.STAT.value="EDIT";
  document.frmDesignations.submit();
}

	function goBack() {
		location.href =  "./CentralController.php?uniqcode=<?=$this->getArr['uniqcode']?>&VIEW=MAIN";
	}

function mout() {
	if(document.Edit.title=='Save') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit.jpg'; 
}

function mover() {
	if(document.Edit.title=='Save') 
		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.jpg'; 
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.jpg'; 
}
	
function edit()
{
	if(document.Edit.title=='Save') {
		editQUA();
		return;
	}
	
	var frm=document.frmDesignations;
//  alert(frm.elements.length);
	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.jpg";
	document.Edit.title="Save";
}
	
function delQUA()
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
              alert("Select atleast one check box");
              return;
            }


    //alert(cntrl.value);
    document.frmDesignations.STAT.value="DEL";
    document.frmDesignations.submit();
}

</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../../themes/beyondT/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/beyondT/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'> &nbsp;</td>
    <td width='100%'><h2><?=$heading?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmDesignations" method="post" action="<?=$_SERVER['PHP_SELF']?>?uniqcode=<?=$this->getArr['uniqcode']?>&id=<?=$this->getArr['id']?>">
  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onmouseout="this.src='../../themes/beyondT/pictures/btn_back.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_back_02.jpg';"  src="../../themes/beyondT/pictures/btn_back.jpg" onclick="goBack();">
        <input type="hidden" name="STAT" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      </font> </td>
  </tr><td width="177">
</table>
<?
$desDet = $this->popArr['desDet'];
?>
<input type="hidden" name="txtDesID" value="<?=$this->getArr['id']?>">

              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
<?						for($c=0;$desDet && count($desDet)>$c;$c++)
							if($desDet[$c][0]==$this->getArr['id'])
								break;
?>                  
						  <tr> 
						    <td><?=$designation?></td>
						  	  <td> <strong><?=$desDet[$c][1]?></strong></td>
						  </tr>
						  <tr>
						    <td><?=$reviewdate?></td>
						    <?
						    $field = explode(" ",$desDet[$c][4]);
						    ?>
						    <td><strong><?=$field[0]?></strong></td>
						  </tr>
						  <tr>
						  	<td><?=$nextupgradelevel?></td>
<?						$nxtupg = $desDet[$c][5];
						for($c=0;$desDet && count($desDet)>$c;$c++)
							if($desDet[$c][0]==$nxtupg)
						  		echo '<td><strong>' .$desDet[$c][1]. '</strong></td>';
?>                  
						  </tr>
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

<?
if(isset($this->popArr['editArr']))
{
    $edit = $this->popArr['editArr'];
?>
<br>
<br>
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						    <tr>
						             <td><?=$qualification?></td>
						             <td><input type="hidden" name="cmbQual" value="<?=$edit[0][1]?>"><strong>
<?
											$quallist = $this->popArr['qualListAll'];
											for($c=0;count($quallist)>$c;$c++)
												if($edit[0][1]==$quallist[$c][0])
												   echo $quallist[$c][1];
?>
						             </strong></td>
						    </tr>
						    <tr>
						             <td><?=$institute?></td>
						             <td><input type="text" disabled name="txtInst" value="<?=$edit[0][2]?>">
						    </tr>
						    <tr>
						             <td><?=$status?></td>
						             <td><select disabled name="cmbStat">
						             <?
						             $key = array_keys($statlist);
						             $value = array_values($statlist);
						             for($c=0;count($statlist)> $c; $c++)
						                 if($edit[0][3]==$value[$c])
						                    echo '<option selected value=' .$value[$c]. '>' .$key[$c]. '</option>';
						                 else
						                    echo '<option value=' . $value[$c] . '>' .$key[$c]. '</option>';
						             ?>
						    </tr>
					  <tr><td></td><td align="right" width="100%">
<?				if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" title="Edit" onmouseout="mout();" onmouseover="mover();" name="Edit" onClick="edit();">
<?				} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.jpg" onClick="alert('<?=$sysConst->accessDenied?>');">
<?				} ?>
						</td></tr>

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
<?
} else {
?>
<br>
<br>
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
							<tr>
							
							         <td><?=$qualification?></td>
							         <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbQual">
							         		<option value="0">-Select Qualification-</option>
							            <?
							            $quallist = $this->popArr['qualListUnAss'];
							            for($c=0;$quallist && count($quallist)>$c;$c++)
							                echo '<option value=' . $quallist[$c][0] . '>' . $quallist[$c][1] . '</option>';
							            ?>
							         </td>
							</tr>
							<tr>
							         <td><?=$institute?></td>
							         <td><input <?=$locRights['add'] ? '':'disabled'?> type="text" name="txtInst">
							</tr>
							<tr>
							         <td><?=$status?></td>
							         <td><select <?=$locRights['add'] ? '':'disabled'?> name="cmbStat">
							         		<option value="0">-Select-</option>
							             <?
							             $key = array_keys($statlist);
							             $value = array_values($statlist);
							             for($c=0;count($statlist)> $c; $c++)
							                    echo '<option value='.$value[$c].'>' .$key[$c]. '</option>';
							             ?>
							</tr>
					  <tr><td></td><td align="right" width="100%">
<?				if($locRights['add']) { ?>
					  <img onClick="addQUA();" onmouseout="this.src='../../themes/beyondT/pictures/btn_save.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_save_02.jpg';" src="../../themes/beyondT/pictures/btn_save.jpg">
<?				} else { ?>
					  <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_save.jpg">
<?				} ?>
						</td></tr>
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
<? } ?>

<table width='100%' cellpadding='0' cellspacing='0' border='0'>
  <tr>
    <td valign='top'> &nbsp;</td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>

  <tr>

    <td width='100%'><h3><?=assignedqualifications?> </h3></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'><A href='index.php?module=Contacts&action=index&return_module=Contacts&return_action=DetailView&&print=true' class='utilsLink'></td>
  </tr>
  <tr>
  <td>
<?	if($locRights['delete']) { ?>
		<img onClick="delQUA();" onmouseout="this.src='../../themes/beyondT/pictures/btn_delete.jpg';" onmouseover="this.src='../../themes/beyondT/pictures/btn_delete_02.jpg';" src="../../themes/beyondT/pictures/btn_delete.jpg">
  <? 	} else { ?>
        <img onClick="alert('<?=$sysConst->accessDenied?>');" src="../../themes/beyondT/pictures/btn_delete.jpg">
<? 	} ?>
  </td>
  </tr>
<tr><td>&nbsp;</td></tr>
</table>
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
								<tr>
								         <td></td>
								         <td><strong><?=$qualification?></strong></td>
								         <td><strong><?=$institute?></strong></td>
								         <td><strong><?=$status?></strong></td>
								</tr>
								<?
								$rset = $this->popArr['qualListAss'];
								$quallist = $this->popArr['qualListAll'];
				            	$key = array_keys($statlist);
				            	$value = array_values($statlist);
								
								    for($c=0;$rset && $c < count($rset); $c++)
								        {
								        echo '<tr>';
								            echo "<td><input type='checkbox' class='checkbox' name='chkdel[]' value='" . $rset[$c][1] ."'></td>";
								            for ($a=0;count($quallist)>$a;$a++)
								            	if($quallist[$a][0]==$rset[$c][1])
								            		$fname = $quallist[$a][1];
						            		echo "<td><a href='". $_SERVER['PHP_SELF'] ."?uniqcode=" . $this->getArr['uniqcode'] . "&id=" . $this->getArr['id']. "&editID=" . $rset[$c][1] . "'>" . $fname . "</a></td>";
								            echo '<td>' . $rset[$c][2] .'</td>';
								            for($a=0;count($statlist)>$a;$a++)
								            	if($rset[$c][3]==$value[$a])
										            echo '<td>' . $key[$a] .'</td>';
								            echo "<td><a href='". $_SERVER['PHP_SELF'] ."?uniqcode=DQS&id=" . $this->getArr['id']."&QUA=". $rset[$c][1] ."'>Subjects</a></td>";
								        echo '</tr>';
								        }
								
								?>
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
</form>
</body>
</html>
