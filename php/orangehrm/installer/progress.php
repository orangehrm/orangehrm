<style type="text/css">
<!--
.style1 {color: #FFCC00}
.style3 {color: #009900}
-->
</style>
<div id="content">
<h2>Step 6: Installing</h2>
<?php
$_SESSION['INSTALLING'] = isset($_SESSION['INSTALLING'])? $_SESSION['INSTALLING'] : 0;
switch ($_SESSION['INSTALLING']) {
	case 1: $nextPhase = 'FILLDATA';
			break;
	case 2: $nextPhase = 'WRITECONF';
			break;
	case 3: $nextPhase = 'CREATEUSER';
			break;
	case 4: $nextPhase = 'REGISTER';
			break;
	default: $nextPhase = 'LOGIN';
			break;
}
?>
<? if (!isset($error) && ($nextPhase == 'REGISTER')) { ?>
<p>
Installation completed successfuly.<br  />
Click <b>[Next]</b> to continue.
</p>
<? } elseif (!isset($error)) { ?>
<p align="center">
<img src="../themes/beyondT/pictures/installstatus30second.gif" width="140" height="30" alt="Installing..."><br/>
Please wait. Installation in progress.
</p>
<? } else { ?>
<p>
Installation aborted due to an error. Click back to correct the error and try installing again.
</p>
<input type="button" name="back" value="Back" onclick="back();" id="next">
<? } 

$Phases = array('Database Creation', 'Fill default data into the database', 'Create Default User', 'Write Configuration File');

  if (isset($error)) { ?>
	<p class="error"><?=$error?></p>
<? } ?>
<table border="0" cellpadding="5" cellspacing="0">
 <? for ($i=0; $i < $_SESSION['INSTALLING']; $i++) { ?>
  <tr>
    <td><?=$Phases[$i]?></td>
    <td><span class="style3">Done</span></td>
  </tr>
 <? } 
 
 $j = $i;
 
 $styleStatus = 'style1'; 
 $msgNext = 'Pending';
 
 	if (isset($error)) { 		
		$j++;
		$styleStatus = 'error';
		$msgNext = 'Aborted';
		unset($_SESSION['INSTALLING']);
		
 ?>
 <tr>
    <td><?=$Phases[$j]?></td>
    <td class="error">Error</td>
  </tr>
 <? } 
 for ($i=$j; $i < 4; $i++) { ?>
  <tr>
    <td><?=$Phases[$i]?></td>
    <td class="<?=$styleStatus?>"><?=$msgNext?></td>
  </tr>
 <? } ?>
</table>
<? if (!isset($error)){
		if ($_SESSION['INSTALLING'] < 4) { ?>
			<meta http-equiv="refresh" content="2;URL=../install.php" />
		<? } else {?>
		<br/>		
		<script language="JavaScript">
			function next() {				
				document.frmInstall.actionResponse.value  = 'REGISTER';
				document.frmInstall.submit();
			}			
		</script>
		<input class="button" type="button" value="Back" onclick="back();" disabled="disabled">
		<input type="button" onClick='next();' value="Next" tabindex="1">
	<? }
 } ?>
</div>