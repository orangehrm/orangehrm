<?php $installFinishValue = 4; ?>
<div id="content">
		<h2>Restoring the database </h2>
<?php
$_SESSION['RESTORING'] = isset($_SESSION['RESTORING'])? $_SESSION['RESTORING'] : 0;
switch ($_SESSION['RESTORING']) {
	case 1: $nextPhase = 'Phase 1';
			break;
	case 2: $nextPhase = 'Phase 2';
			break;
	case 3: $nextPhase = 'Phase 2';
			break;
	case 4: $nextPhase = 'REGISTER';
			break;
	default: $nextPhase = 'LOGIN';
			break;
}
?>

<?php if (!isset($error) && ($nextPhase == 'REGISTER')) { ?>
<p>
Upgrading is completed successfuly.<br  />
Click <b>[Next]</b> to continue.
</p>
<?php } elseif (!isset($error)) { ?>
<p align="center">
<img src="../installer/images/progress_bar.gif" width="150" height="13" alt="Upgrading..." id="progressbar"><br/>
Please wait. Upgrading in progress.
</p>
<?php } else { ?>
<p>
Upgrading is  aborted due to an error. Click <b>[Clean Up Upgrade]</b> to correct the error and try Upgrading again.
</p>
<?php } 

$Phases = array('Phase 1', 'Phase 2', 'Phase 3', 'Phase 4');

  $controlval = 0;
  	
  if (isset($error)) {
  	$controlval = 1;  
?>
	<p class="error"><?php echo $error?></p>
<?php } ?>

<table border="0" cellpadding="5" cellspacing="0">
 <?php for ($i=0; $i < $_SESSION['RESTORING']-$controlval; $i++) { ?>
  <tr>
    <td><?php echo $Phases[$i]?></td>
    <td><span class="done">Done</span></td>
  </tr>
 <?php } 
 
 $j = $i--;
 
 $styleStatus = 'pending'; 
 $msgNext = 'Pending';
 
 	if (isset($error)) { 			
		$styleStatus = 'error';
		$msgNext = 'Aborted';
		unset($_SESSION['RESTORING']);
		
 ?>
 <tr>
    <td><?php echo $Phases[$j]?></td>
    <td class="error">Error</td>
  </tr>
 <?php $j++;
 	} 
 for ($i=$j; $i < $installFinishValue; $i++) { ?>
  <tr>
    <td><?php echo $Phases[$i]?></td>
    <td class="<?php echo $styleStatus?>"><?php echo $msgNext?></td>
  </tr>
 <?php } ?>
</table>
		<p>
	<?php if (!isset($error)){
		if ($_SESSION['RESTORING'] < $installFinishValue) { ?>
          <noscript>
          <meta http-equiv="Refresh" content="2;URL=../upgrade.php" />
          </noscript>
          <script language="JavaScript" type="text/javascript">
			setTimeout('window.location= "../upgrade.php"', 2000);
			if (document.images)
			{ 
				setTimeout('document.progressbar.src = document.progressbar.src', 2000);
			}
		  </script>
          <?php }
		  } ?>
</p>
		<input class="button" type="button" value="<?php echo (isset($error))? 'Clean Up Upgrade' : 'Back'?>" onclick="back();" tabindex="2" <?php echo (isset($error))? '' : 'disabled'?> />
 <input type="button" onClick='next();' value="Next" tabindex="1" <?php echo (isset($_SESSION['RESTORING']) && ($_SESSION['RESTORING'] >=$installFinishValue))? '' : 'disabled'?> />
</div>
