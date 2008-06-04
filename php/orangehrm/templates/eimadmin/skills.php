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
require_once($lan->getLangPath("full.php"));

	$sysConst = new sysConf();
	$locRights=$_SESSION['localRights'];


	if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'addmode')) {

	?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
	function goBack() {
		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
	}

	function addSave() {

		if (document.frmSkills.txtSkillName.value == '') {
		        alert("<?php echo $lang_Admin_Skill_Errors_NameCannotBeBlank; ?>");
			return false;
		}

		document.frmSkills.sqlState.value = "NewRecord";
		document.frmSkills.submit();
		return true;
	}

	function clearAll() {
		document.frmSkills.txtSkillName.value = '';
		document.frmSkills.txtSkillDesc.value = '';
	}
</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?php echo "$lang_Menu_Admin_Skills : $lang_Menu_Admin_Skills_Skills"; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmSkills" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>" onSubmit="return addSave()">

  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';"  src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'>
      <?php
		if (isset($this->getArr['msg'])) {

			$expString  = $this->getArr['msg'];
			$expString = explode ("_",$expString);
			$length = count($expString);

			$col_def=$expString[$length-1];

			$expString='lang_view_'.$this->getArr['msg'];
	?>
			<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
	<?php
				echo $$expString;
	?>
			</font>
	<?php
		}
		?>
	</td>
  </tr><td width="177">
</table>
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
						  <tr>
						    <td nowrap valign="top"><span class="error">*</span> <?php echo $lang_Commn_name; ?></td>
						    <td> <input type="text" name='txtSkillName' tabindex="3" /> </td>
						  </tr>
						  <tr>
						    <td nowrap valign="top"> <?php echo $lang_Commn_description; ?></td>
						    <td> <textarea name='txtSkillDesc' rows="3" cols="30" tabindex="3" ></textarea> </td>
						  </tr>
					  <tr><td></td><td align="right" width="100%"><img onClick="addSave();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_save.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_save_02.gif';" src="../../themes/beyondT/pictures/btn_save.gif">
        <img onClick="clearAll();" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" src="../../themes/beyondT/pictures/btn_clear.gif"></td></tr>

                  </table></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>


</form>
</form>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
<?php } else if ((isset($this->getArr['capturemode'])) && ($this->getArr['capturemode'] == 'updatemode')) {
	 $message = $this->popArr['editArr'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
function alpha(txt)
{
var flag=true;
var i,code;

if(txt.value=="")
   return false;

for(i=0;txt.value.length>i;i++)
	{
	code=txt.value.charCodeAt(i);
    if((code>=65 && code<=122) || code==32 || code==46)
	   flag=true;
	else
	   {
	   flag=false;
	   break;
	   }
	}
return flag;
}

function numeric(txt)
{
var flag=true;
var i,code;

if(txt.value=="")
   return false;

for(i=0;txt.value.length>i;i++)
	{
	code=txt.value.charCodeAt(i);
    if(code>=48 && code<=57)
	   flag=true;
	else
	   {
	   flag=false;
	   break;
	   }
	}
return flag;
}

	function goBack() {
		location.href = "./CentralController.php?uniqcode=<?php echo $this->getArr['uniqcode']?>&VIEW=MAIN";
	}

function mout() {
	if(document.Edit.title=='Save')
		document.Edit.src='../../themes/beyondT/pictures/btn_save.gif';
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit.gif';
}

function mover() {
	if(document.Edit.title=='Save')
		document.Edit.src='../../themes/beyondT/pictures/btn_save_02.gif';
	else
		document.Edit.src='../../themes/beyondT/pictures/btn_edit_02.gif';
}

function edit()
{
	if(document.Edit.title=='Save') {
		addUpdate();
		return;
	}

	var frm=document.frmSkills;

	for (var i=0; i < frm.elements.length; i++)
		frm.elements[i].disabled = false;
	document.Edit.src="../../themes/beyondT/pictures/btn_save.gif";
	document.Edit.title="Save";
}

	function addUpdate() {

		if (document.frmSkills.txtSkillName.value == '') {
		        alert("<?php echo $lang_Admin_Skill_Errors_NameCannotBeBlank; ?>");
			return false;
		}

		document.frmSkills.sqlState.value = "UpdateRecord";
		document.frmSkills.submit();
	}

	function clearAll() {
		if(document.Edit.title!='Save')
			return;

			document.frmSkills.txtSkillName.value = '';
	}
</script>
<link href="../../themes/<?php echo $styleSheet;?>/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">@import url("../../themes/<?php echo $styleSheet;?>/css/style.css"); </style>
</head>
<body>
<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'>
  <tr>
    <td valign='top'></td>
    <td width='100%'><h2><?php echo "$lang_Menu_Admin_Skills : $lang_Menu_Admin_Skills_Skills"; ?></h2></td>
    <td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'></td>
  </tr>
</table>
<p>
<p>
<table width="431" border="0" cellspacing="0" cellpadding="0" ><td width="177">
<form name="frmSkills" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?uniqcode=<?php echo $this->getArr['uniqcode']?>&id=<?php echo $this->getArr['id']?>">

  <tr>
    <td height="27" valign='top'> <p> <img title="Back" onMouseOut="this.src='../../themes/beyondT/pictures/btn_back.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_back_02.gif';" src="../../themes/beyondT/pictures/btn_back.gif" onClick="goBack();">
        <input type="hidden" name="sqlState" value="">
      </p></td>
    <td width="254" align='left' valign='bottom'> <font color="red" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;
      <?php
		if (isset($this->getArr['msg'])) {

			$expString  = $this->getArr['msg'];
			$expString = explode ("_",$expString);
			$length = count($expString);

			$col_def=$expString[$length-1];

			$expString='lang_view_'.$this->getArr['msg'];
	?>
			<font class="<?php echo $col_def?>" size="-1" face="Verdana, Arial, Helvetica, sans-serif">
	<?php
				echo $$expString;
	?>
			</font>
	<?php
		}
		?>
      </font> </td>
  </tr><td width="177">
</table>
           <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
							  <tr>
							    <td><?php echo $lang_Commn_code; ?></td>
							    <td> <input type="hidden"  name="txtSkillID" value=<?php echo $message[0][0]?>> <strong><?php echo $message[0][0]?></strong> </td>
							  </tr>
							  <tr>
							    <td nowrap valign="top"><span class="error">*</span> <?php echo $lang_Commn_name; ?></td>
<?php
								 /* Remove newlines from the skill name.
                                                                  * Earlier, skill name was a textarea and can contain new lines in it
                                                                  */
								 $skillName = preg_replace("/[\n\r]/"," ",trim($message[0][1]) );
?>
							  	  <td align="left" valign="top"> <input type="text" name='txtSkillName' disabled tabindex='3'  value="<?php echo $skillName ?>" ></td>
							  </tr>
							  <tr>
							    <td nowrap valign="top"> <?php echo $lang_Commn_description; ?></td>
							  	  <td align="left" valign="top">
							              <textarea name='txtSkillDesc' disabled rows="3" cols="30" tabindex='3' ><?php echo $message[0][2]?></textarea>
                                                            </td>
							  </tr>

					  <tr><td></td><td align="right" width="100%">
<?php			if($locRights['edit']) { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" title="Edit" onMouseOut="mout();" onMouseOver="mover();" name="Edit" onClick="edit();">
<?php			} else { ?>
			        <img src="../../themes/beyondT/pictures/btn_edit.gif" onClick="alert('<?php echo $lang_Common_AccessDenied;?>');">
<?php			}  ?>
					  <img src="../../themes/beyondT/pictures/btn_clear.gif" onMouseOut="this.src='../../themes/beyondT/pictures/btn_clear.gif';" onMouseOver="this.src='../../themes/beyondT/pictures/btn_clear_02.gif';" onClick="clearAll();" >
</td>
					  </tr>
                  </table></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r2_c3.gif"><img name="table_r2_c3" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                </tr>
                <tr>
                  <td><img name="table_r3_c1" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c1.gif" width="13" height="16" border="0" alt=""></td>
                  <td background="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c2.gif"><img name="table_r3_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><img name="table_r3_c3" src="../../themes/<?php echo $styleSheet; ?>/pictures/table_r3_c3.gif" width="13" height="16" border="0" alt=""></td>
                  <td><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="16" border="0" alt=""></td>
                </tr>
              </table>
</form>
</form>
<span id="notice"><?php echo preg_replace('/#star/', '<span class="error">*</span>', $lang_Commn_RequiredFieldMark); ?>.</span>
</body>
</html>
<?php } ?>
