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

	$lantype  = array ($lang_hrEmpMain_Writing=> 1 , $lang_hrEmpMain_Speaking=>2 , $lang_hrEmpMain_Reading=>3 );
	$grdcodes = array($lang_hrEmpMain_Poor=> 1 ,$lang_hrEmpMain_Basic=>2 , $lang_hrEmpMain_Good=>3 ,$lang_hrEmpMain_MotherTongue=>4);

?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--
function editLang() {
	if ($('btnEditLang').value == '<?php echo $lang_Common_Save; ?>') {
		editEXTLang();
		return;
	} else {
		$('btnEditLang').value = '<?php echo $lang_Common_Save; ?>';
		$('btnEditLang').onClick = editEXTLang;
	}

	var frm = document.frmEmp;
	for (var i=0; i < frm.elements.length; i++) {
		frm.elements[i].disabled = false;
	}
}

function addEXTLang()
{
	if(document.frmEmp.cmbLanCode.value=='0') {
		alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
		document.frmEmp.cmbLanCode.focus();
		return;
	}

	if(document.frmEmp.cmbLanType.value=='0') {
		alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
		document.frmEmp.cmbLanType.focus();
		return;
	}

	if(document.frmEmp.cmbRatGrd.value=='0') {
		alert("<?php echo $lang_Error_FieldShouldBeSelected; ?>");
		document.frmEmp.cmbRatGrd.focus();
		return;
	}

  document.frmEmp.langSTAT.value="ADD";
  qCombo(11);
}

function editEXTLang() {
  document.frmEmp.langSTAT.value="EDIT";
  qCombo(11);
}

function viewLang(lanSeq,lanFlu) {

	document.frmEmp.action=document.frmEmp.action + "&lanSEQ=" + lanSeq + "&lanFLU=" + lanFlu;
	document.frmEmp.pane.value=11;
	document.frmEmp.submit();
}

function delEXTLang() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chklangdel[]') && (elements[i].checked == true)) {
				check = true;
			}
		}
	}

	if(!check) {
		alert('<?php echo $lang_Common_SelectDelete; ?>')
		return;
	}

    document.frmEmp.langSTAT.value="DEL";
   qCombo(11);
}

//--><!]]></script>
<div id="parentPaneLanguages" >
<?php  if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
<input type="hidden" name="langSTAT" value=""/>
<?php
if(isset($this->getArr['lanSEQ'])) {
    $edit = $this->popArr['editLanArr'];
?>
<div id="editPaneLanguages" >
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
                    <tr>
                      <td width="200"><?php echo $lang_hremp_Language?></td>
    				  <td><input type="hidden" name="cmbLanCode" value="<?php echo $edit[0][1]?>"/><strong>
<?php						$lanlist = $this->popArr['lanlist'];
						for($c=0;count($lanlist)>$c;$c++)
							if($edit[0][1]==$lanlist[$c][0])
							     break;

					  			echo CommonFunctions::escapeHtml($lanlist[$c][1]);
?>
					  </strong></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $lang_hremplan_fluency?></td>
						<td align="left" valign="top"><input type="hidden" name="cmbLanType" value="<?php echo $this->getArr['lanFLU']?>"/><strong>
<?php
						$index=array_values($lantype);
						$value=array_keys($lantype);
						for($a=0;count($lantype)>$a;$a++)
							if($this->getArr['lanFLU']==$index[$a])
					  			echo $value[$a];
?>
						</td>
					  </tr>

					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_ratinggarde?></td>
						<td align="left" valign="top"><select disabled="disabled" name='cmbRatGrd'>
<?php
						$code=array_values($grdcodes);
						 $name=array_keys($grdcodes);
						for($c=0;count($grdcodes)>$c;$c++)
							if($code[$c]==$edit[0][3])
								echo "<option selected=\"selected\" value='" . $code[$c] . "'>" . $name[$c] ."</option>";
							else
								echo "<option value='" . $code[$c] . "'>" . $name[$c] ."</option>";
?>
						</select></td>
					  </tr>

					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
							<input type="button" id="btnEditLang" class="editbutton" value="<?php echo $lang_Common_Edit; ?>"
								onmouseout="moutButton(this);" onmouseover="moverButton(this);"
								onclick="editLang();" />
							<input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" disabled="disabled"
								onmouseout="moutButton(this);" onmouseover="moverButton(this);" />
						</td>
					  </tr>
                  </table>
</div>
<?php } else { ?>
<div id="addPaneLanguages" class="<?php echo ($this->popArr['rsetLang'] != null)?"addPane":""; ?>" >
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="">
                    <tr>
                      <td width="200"><?php echo $lang_hremp_Language?></td>
    				  <td><select name="cmbLanCode">
    				  		<option selected="selected" value="0">--<?php echo $lang_hremplan_SelectLanguage; ?>--</option>
<?php
						$lanlist= $this->popArr['lanlist'];
						for($c=0;$lanlist && count($lanlist)>$c;$c++)
							if(isset($this->popArr['cmbLanCode']) && $this->popArr['cmbLanCode']==$lanlist[$c][0])
							   echo "<option  value='" . $lanlist[$c][0] . "'>" . CommonFunctions::escapeHtml($lanlist[$c][1]) . "</option>";
							 else
							   echo "<option value='" . $lanlist[$c][0] . "'>" . CommonFunctions::escapeHtml($lanlist[$c][1]) . "</option>";
?>
					  </select></td>
					</tr>
                    <tr>
                      <td width="200"><?php echo $lang_hremplan_fluency?></td>
    				  <td><select name="cmbLanType">
    				  		<option value="0">---<?php echo $lang_hremplan_SelectFluency; ?>---</option>
<?php
						$index=array_values($lantype);
						$value=array_keys($lantype);
						for($c=0;$lantype && count($lantype)>$c;$c++)
							   echo "<option value='" . $index[$c] . "'>" . $value[$c] . "</option>";
?>
					  </select></td>
					</tr>
					  <tr>
						<td valign="top"><?php echo $lang_hrEmpMain_ratinggarde?></td>
						<td align="left" valign="top"><select name='cmbRatGrd'>
    				  		<option value="0">----<?php echo $lang_hremplan_SelectRating; ?>----</option>
<?php
				        $code=array_values($grdcodes);
						$name=array_keys($grdcodes);
						for($c=0;$grdcodes && count($grdcodes)>$c;$c++)
							   echo "<option value='" . $code[$c] . "'>" . $name[$c] . "</option>";
?>

					</select>
						</td>
					  </tr>
					  <tr>
						<td valign="top"></td>
						<td align="left" valign="top">
						</td>
					  </tr>
                  </table>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddLang" id="btnAddLang"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addEXTLang(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>

</div>
<?php } ?>
<?php
    $rset = $this->popArr['rsetLang'];

    if( $rset && count($rset) > 0 ){
        $assignedLanguages = true;
    } else {
        $assignedLanguages = false;
    }
?>
<?php if($assignedLanguages) { ?>
<div class="subHeading"><h3><?php echo $lang_hremplan_assignlanguage; ?></h3></div>
<div class="actionbar">
	<div class="actionbuttons">
		<input type="button" class="addbutton"
			onclick="showAddPane('Languages');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
		<input type="button" class="delbutton"
			onclick="delEXTLang();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
			value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>
	</div>
</div>
<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
	<thead>
	  <tr>
      	<td></td>
		 <td><?php echo $lang_hremp_Language?></td>
		 <td><?php echo $lang_hremplan_fluency?></td>
		 <td><?php echo $lang_hrEmpMain_ratinggarde?></td>
	</tr>
	</thead>
	<tbody>

<?php
    for($c=0; $rset && $c < count($rset); $c++) {
			$cssClass = ($c%2) ? 'even' : 'odd';
	    	echo '<tr class="' . $cssClass . '">';
            echo "<td><input type='checkbox' class='checkbox' name='chklangdel[]' value='" . $rset[$c][1] ."|". $rset[$c][2] ."'/></td>";

			for($a=0;count($lanlist)>$a;$a++)
				if($rset[$c][1] == $lanlist[$a][0])
				   $lname=$lanlist[$a][1];
            ?> <td><a href="javascript:viewLang('<?php echo $rset[$c][1]?>','<?php echo $rset[$c][2]?>')"><?php echo CommonFunctions::escapeHtml($lname)?></a></td> <?php

            for($a=0;count($lantype)>$a;$a++)
				if($rset[$c][2] == $index[$a])
				   $flu=$value[$a];
            echo '<td>' . $flu .'</td>';
            for($a=0;count($grdcodes)>$a;$a++)
				if($rset[$c][3] == $code[$a])
				   $rate=$name[$a];
            echo '<td>' . $rate.'</td>';

        echo '</tr>';
        }
?>
	</tbody>
</table>
<?php } ?>
<?php } ?>
</div>
