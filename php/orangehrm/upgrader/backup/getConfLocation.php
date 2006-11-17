<script language="JavaScript">
function confLocationSubmit() {
	document.frmInstall.actionResponse.value  = 'LOCCONFOK';
	document.frmInstall.submit();
}
</script>
<div id="content">
	<h2>OrangeHRM 1.2 </h2>   
	<?php if (isset($_SESSION['error'])) { ?>
    <p><font color="Red"><?php echo $_SESSION['error']; ?></font></p>
    <?php } ?>
	<p>Please enter the location of the previous installation of OrangeHRM  and Click <b>[Next]</b> to continue.</p>
<table cellpadding="0" cellspacing="0" border="0" class="table">
	<tr>
		<th colspan="3" align="left">Database Configuration</td>
	</tr>
<tr>
	<td class="tdComponent">Location of previous Installation of OrangeHRM</td>
	<td class="tdValues"><input type="text" name="locationOhrm" value="<?php echo  isset($_SESSION['dbInfo']['locationOhrm']) ? $_SESSION['dbInfo']['locationOhrm'] : 'orangehrm'?>" tabindex="1" ></td>
</tr>
</table>
<p>
	<input class="button" type="button" value="Back" onclick="back();" tabindex="4">
	<input type="button" name="next" value="Next" onclick="confLocationSubmit();" id="next" tabindex="3">
</p>
</div>