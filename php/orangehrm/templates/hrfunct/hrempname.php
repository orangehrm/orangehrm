
/////////////// addmode

<table width="550" align="center" border="0" cellpadding="0" cellspacing="0">
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
				<td><?=$code?></td>
				<td><input type="hidden" name="txtEmpID" value=<?=$this->popArr['newID']?>><strong><?=$this->popArr['newID']?></strong></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font><?=$lastname?></td>
				<td> <input type="text" name="txtEmpLastName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:''?>"></td>
				<td>&nbsp;</td>
				<td><font color=#ff0000>*</font><?=$firstname?></td>
				<td> <input type="text" name="txtEmpFirstName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:''?>"></td>
			  </tr>
			  <tr> 
				<td><font color=#ff0000>*</font><?=$middlename?></td>
				<td> <input type="text" name="txtEmpMiddleName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:''?>"></td>
				<td>&nbsp;</td>
			  <td><?=$nickname?></td>
				<td> <input type="text" name="txtEmpNickName" <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:''?>"></td>
			  </tr>
			 <tr>
				<td><?=$photo?></td>
				<td> <input type="file" name='photofile' <?=$locRights['add'] ? '':'disabled'?> value="<?=(isset($this->postArr['photofile']))?$this->postArr['photofile']:''?>"></td>
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


///////////////updatemode

			<table width="550" align="center" border="0" cellpadding="0" cellspacing="0"><tr><td><br>&nbsp;</td></tr>
                <tr>
                  <td width="13"><img name="table_r1_c1" src="../../themes/beyondT/pictures/table_r1_c1.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="339" background="../../themes/beyondT/pictures/table_r1_c2.gif"><img name="table_r1_c2" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td width="13"><img name="table_r1_c3" src="../../themes/beyondT/pictures/table_r1_c3.gif" width="13" height="12" border="0" alt=""></td>
                  <td width="11"><img src="../../themes/beyondT/pictures/spacer.gif" width="1" height="12" border="0" alt=""></td>
                </tr>
                <tr>
                  <td background="../../themes/beyondT/pictures/table_r2_c1.gif"><img name="table_r2_c1" src="../../themes/beyondT/pictures/spacer.gif" width="1" height="1" border="0" alt=""></td>
                  <td><table onclick="setUpdate(0)" onkeypress="setUpdate(0)" width="100%" border="0" cellpadding="5" cellspacing="0" class="">
			  <tr> 
				<td><?=$code?></td>
				<td><strong><input type="hidden" name="txtEmpID" value="<?=$this->getArr['id']?>"><?=$this->getArr['id']?></strong></td>
			  </tr>
			  <tr> 
				<td><?=$lastname?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpLastName" value="<?=(isset($this->postArr['txtEmpLastName']))?$this->postArr['txtEmpLastName']:$edit[0][1]?>"></td>
				<td>&nbsp;</td>
				<td><?=$firstname?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpFirstName" value="<?=(isset($this->postArr['txtEmpFirstName']))?$this->postArr['txtEmpFirstName']:$edit[0][2]?>"></td>
			  </tr>
			  <tr> 
				<td><?=$middlename?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpMiddleName" value="<?=(isset($this->postArr['txtEmpMiddleName']))?$this->postArr['txtEmpMiddleName']:$edit[0][4]?>"></td>
				<td>&nbsp;</td>
			  <td><?=$nickname?></td>
				<td> <input type="text" <?=(isset($this->postArr['EditMode']) && $this->postArr['EditMode']=='1') ? '' : 'disabled'?> name="txtEmpNickName" value="<?=(isset($this->postArr['txtEmpNickName']))?$this->postArr['txtEmpNickName']:$edit[0][3]?>"></td>
			  </tr><tr><td><br>&nbsp;</td></tr>
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
