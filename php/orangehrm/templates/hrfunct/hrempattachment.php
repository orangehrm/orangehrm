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
?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--

function dwPopup() {
        var popup=window.open('../../templates/hrfunct/download.php?id=<?php echo isset($this->getArr['id']) ? CommonFunctions::escapeHtml($this->getArr['id']) : ''?>&ATTACH=<?php echo isset($this->getArr['ATTACH']) ? CommonFunctions::escapeHtml($this->getArr['ATTACH']) : ''?>','Downloads');
        if(!popup.opener) popup.opener=self;
}

function delAttach() {

	var check = false;
	with (document.frmEmp) {
		for (var i=0; i < elements.length; i++) {
			if ((elements[i].name == 'chkattdel[]') && (elements[i].checked == true)){
				check = true;
			}
		}
	}

	if(!check){
		alert('<?php echo $lang_hremp_SelectAtLEastOneAttachment; ?>')
		return;
	}

	document.frmEmp.attSTAT.value="DEL";
	qCombo(6);
}

function addAttach() {
	var fileName = document.frmEmp.ufile.value;
	fileName = trim(fileName);
	if (fileName == "") {
		alert("<?php echo $lang_hremp_PleaseSelectFile; ?>");
		return;
	}

    if (document.frmEmp.txtAttDesc.value.length > 200 ) {
        alert('<?php echo $lang_hremp_CommentsShouldBeLimitedTo200Chars; ?>');
        document.frmEmp.txtAttDesc.focus();
        return;
    }

	document.frmEmp.attSTAT.value="ADD";
	qCombo(6);
}

function viewAttach(att) {
	document.frmEmp.action=document.frmEmp.action + "&ATTACH=" + att;
	document.frmEmp.pane.value=6;
	document.frmEmp.submit();
}

function editAttach() {
	if ($('btnEditAttach').value == '<?php echo $lang_Common_Save; ?>') {

        if (document.frmEmp.txtAttDesc.value.length > 200 ) {
            alert('<?php echo $lang_hremp_CommentsShouldBeLimitedTo200Chars; ?>');
            document.frmEmp.txtAttDesc.focus();
            return;
        }
        
		document.frmEmp.attSTAT.value="EDIT";
		qCombo(6);
	} else {
		$('btnEditAttach').value = '<?php echo $lang_Common_Save; ?>'
		$('txtAttDesc').disabled = false;
		$('btnReset').disabled = false;
	}
}

<?php
	if(isset($_GET['ATT_UPLOAD']) && $_GET['ATT_UPLOAD'] == 'FAILED')
		echo "alert('" .$lang_lang_uploadfailed."');";
?>
//--><!]]></script>
<div id="parentPaneAttachments" >
<?php if(isset($this->getArr['capturemode']) && $this->getArr['capturemode'] == 'updatemode') { ?>
<?php		if(isset($this->getArr['ATTACH'])) {
				$edit = $this->popArr['editAttForm'];
		 		$disabled = ($locRights['edit']) ? "" : 'disabled="disabled"';
?>
	<div id="editPaneAttachments" >
       <input type="hidden" name="seqNO" value="<?php echo CommonFunctions::escapeHtml($edit[0][1])?>">
       <table width="352" style="height:120px" border="0" cellpadding="0" cellspacing="0">
              <tr>
              	<td><?php echo $lang_hremp_filename?></td>
              	<td><?php echo CommonFunctions::escapeHtml($edit[0][3]);?></td>
              </tr>
              <tr>
              	<td><?php echo $lang_Commn_description?></td>
              	<td>
              		<textarea name="txtAttDesc" id="txtAttDesc" rows="3" cols="25" disabled="disabled"><?php echo CommonFunctions::escapeHtml($edit[0][2])?></textarea>
              	</td>
              </tr>
              <tr>
              	<td colspan="2">
              		<input type="button" class="plainbtn" value="<?php echo $lang_hremp_ShowFile; ?>"
              		class="button" onclick="dwPopup()">
					<?php	if ($locRights['edit']) { ?>
        				<input type="button" class="editbutton" id="btnEditAttach" value="<?php echo $lang_Common_Edit; ?>"
        					onmouseout="moutButton(this)" onmouseover="moverButton(this)" onclick="editAttach();" />
        				<input type="reset" class="resetbutton" id="btnReset" disabled="disabled" value="<?php echo $lang_Common_Reset; ?>"
        					onmouseout="moutButton(this)" onmouseover="moverButton(this)" />
					<?php	} ?>
              	</td>
              </tr>
		</table>
	</div>
<?php } else if ($locRights['add']) { ?>
<div id="addPaneAttachments" class="<?php echo ($this->popArr['empAttAss'] != null)?"addPane":""; ?>" >
	  <table width="352" style="height:120px;padding:5px 5px 0 5px;" border="0" cellpadding="0" cellspacing="0" >
          <tr>
				<td valign="top"><?php echo $lang_hremp_path?></td>
				<td><input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
					<input type="file" name="ufile"/> <br />[<?php echo $lang_hremp_largefileignore?>]</td>
              </tr>
              <tr>
              	<td><?php echo $lang_Commn_description?></td>
              	<td><textarea name="txtAttDesc" rows="3" cols="25" ></textarea></td>
              </tr>
			  <tr>
				<td>&nbsp;</td>
				<td>
				</td>
			  </tr>
	   </table>
<div class="formbuttons">
    <input type="button" class="savebutton" name="btnAddAttachment" id="btnAddAttachment"
    	value="<?php echo $lang_Common_Save;?>"
    	title="<?php echo $lang_Common_Save;?>"
    	onmouseover="moverButton(this);" onmouseout="moutButton(this);"
    	onclick="addAttach(); return false;"/>
    <input type="reset" class="resetbutton" value="<?php echo $lang_Common_Reset; ?>" />
</div>
	 </div>
<?php } ?>
<?php
	$rset = $this->popArr['empAttAss'] ;
	if ($rset != null){ ?>
		<div class="subHeading"><h3><?php echo $lang_hrEmpMain_assignattach?></h3></div>
	<div class="actionbar">
		<div class="actionbuttons">
<?php if ($locRights['add']) { ?>
					<input type="button" class="addbutton"
						onclick="showAddPane('Attachments');" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Add;?>" title="<?php echo $lang_Common_Add;?>"/>
<?php } ?>
<?php	if ($locRights['delete']) { ?>
					<input type="button" class="delbutton"
						onclick="delAttach();" onmouseover="moverButton(this);" onmouseout="moutButton(this);"
						value="<?php echo $lang_Common_Delete;?>" title="<?php echo $lang_Common_Delete;?>"/>

<?php 	} ?>
			</div>
		</div>

		<table width="100%" cellspacing="0" cellpadding="0" class="data-table">
		<thead>
			<tr>
                <td></td>
				<td><?php echo $lang_hremp_filename?></td>
				<td><?php echo $lang_Commn_description?></td>
				<td><?php echo $lang_hremp_size?></td>
				<td><?php echo $lang_hremp_type?></td>
			</tr>
		</thead>
		<tbody>
<?php

	$disabled = ($locRights['delete']) ? "" : 'disabled="disabled"';
    for($c=0;$rset && $c < count($rset); $c++) {
		$cssClass = ($c%2) ? 'even' : 'odd';
?>
		<tr class="<?php echo $cssClass;?>">
            <td><input type='checkbox' <?php echo $disabled;?> class='checkbox' name='chkattdel[]' value="<?php echo $rset[$c][1]; ?>"/></td>
            <td><a href="#" title="<?php echo CommonFunctions::escapeHtml($rset[$c][2]); ?>"
                   onmousedown="viewAttach('<?php echo $rset[$c][1]; ?>')" ><?php echo CommonFunctions::escapeHtml($rset[$c][3]); ?></a></td>
            <td><?php echo CommonFunctions::escapeHtml($rset[$c][2]); ?></td>
            <td><?php echo CommonFunctions::formatSiUnitPrefix($rset[$c][4]); ?>B</td>
            <td><?php echo CommonFunctions::escapeHtml($rset[$c][6]); ?></td>
        </tr>
<?php
        }
?>
			</tbody>
          </table>
<?php } else if (!$locRights['add']) { ?>
	<p><?php echo $lang_empview_norecorddisplay; ?></p>
<?php }?>
<?php } ?>
</div>