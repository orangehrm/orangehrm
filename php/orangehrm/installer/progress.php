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


$installFinishValue = 6;

?>
<div id="content">
<h2>Step 6: Installing</h2>
<?php
$_SESSION['INSTALLING'] = isset($_SESSION['INSTALLING'])? $_SESSION['INSTALLING'] : 0;
switch ($_SESSION['INSTALLING']) {
	case 1: $nextPhase = 'MAKETABLE';
			break;
	case 2: $nextPhase = 'FILLDATA';
			break;
	case 3: $nextPhase = 'WRITECONF';
			break;
	case 4: $nextPhase = 'CREATEDBUSER';
			break;
	case 5: $nextPhase = 'CREATEUSER';
			break;
	case 6: $nextPhase = 'REGISTER';
			break;
	default: $nextPhase = 'LOGIN';
			break;
}
?>
<?php if (!isset($error) && ($nextPhase == 'REGISTER')) { ?>
<p>
Installation completed successfuly.<br  />
Click <b>[Next]</b> to continue.
</p>
<?php } elseif (!isset($error)) { ?>
<p align="center">
<img src="images/progress_bar.gif" width="150" height="13" alt="Installing..." id="progressbar"><br/>
Please wait. Installation in progress.
</p>
<?php } else { ?>
<p>
Installation aborted due to an error. Click <b>[Clean Up Install]</b> to correct the error and try installing again.
</p>
<?php }

$Phases = array('Database Creation', 'Create Database Tables', 'Fill default data into the database', 'Create Database User', 'Create Default User', 'Write Configuration File');

  $controlval = 0;

  if (isset($error)) {
  	$controlval = 1;
?>
	<p class="error"><?php echo $error?></p>
<?php } ?>
<table border="0" cellpadding="5" cellspacing="0">
 <?php for ($i=0; $i < $_SESSION['INSTALLING']-$controlval; $i++) { ?>
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
		unset($_SESSION['INSTALLING']);

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
<?php if (!isset($error)){
		if ($_SESSION['INSTALLING'] < $installFinishValue) { ?>
		<noscript>
			<meta http-equiv="refresh" content="2;URL=../install.php" />
		</noscript>
		<script language="javascript">
			setTimeout('window.location= "../install.php"', 2000);
			if (document.images)
			{
				setTimeout('document.progressbar.src = document.progressbar.src', 2000);
			}
		</script>
		<?php } else {?>
		<br/>
		<script language="JavaScript">
			function next() {
				document.frmInstall.actionResponse.value  = 'REGISTER';
				document.frmInstall.submit();
			}
		</script>
	<?php }
 } ?>
 <br />
 <input class="button" type="button" value="<?php echo (isset($error))? 'Clean Up Install' : 'Back'?>" onclick="back();" tabindex="2" <?php echo (isset($error))? '' : 'disabled'?> />
 <input type="button" onClick='next();' value="Next" tabindex="1" <?php echo (isset($_SESSION['INSTALLING']) && ($_SESSION['INSTALLING'] >=$installFinishValue))? '' : 'disabled'?> />
</div>
