<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 

$cupath = realpath(dirname(__FILE__).'/../');

define('ROOT_PATH', $cupath);
$steps = array('database info', 'version info', 'database changes', 'configuration info');

$helpLink = array("#DBInfo", "#VersionInfo", "#DBChanges", '#configInfo');
$currScreen = $sf_user->getAttribute('currentScreen');

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <?php use_stylesheet('style.css') ?>
    <title>OrangeHRM Web Upgrade Wizard</title>
<link href="favicon.ico" rel="icon" type="image/gif"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript">

function goToScreen(screenNo) {
    document.frmInstall.txtScreen.value = screenNo;
}

function cancel() {
    document.frmInstall.actionResponse.value  = 'CANCEL';
    document.frmInstall.submit();
}

function back() {
    document.frmInstall.actionResponse.value  = 'BACK';
    document.frmInstall.submit();
}

</script>
  </head>
  <body>
      <div id="body">
    <?php echo image_tag('orange3.png');?>
    <form name="frmInstall" action="../install.php" method="post">
        <input type="hidden" name="txtScreen" value="<?php echo $currScreen?>">
        <input type="hidden" name="actionResponse">
        
        <table border="0" cellpadding="0" cellspacing="0">
          <tr>
        <?php
            $tocome = '';
            for ($i=0; $i < count($steps); $i++) {
                if ($currScreen == $i) {
                    $tabState = 'Active';
                } else {
                    $tabState = 'Inactive';
                }
        ?>
        
            <td nowrap="nowrap" class="left_<?php echo $tabState?>">&nbsp;</td>
            <td nowrap="nowrap" class="middle_<?php echo $tabState.$tocome?>"><?php echo $steps[$i]?></td>
            <td nowrap="nowrap" class="right_<?php echo $tabState?>">&nbsp;</td>
        
            <?php
                if ($tabState == 'Active') {
                    $tocome = '_tocome';
                }
            }
            ?>
          </tr>
        </table>
    </form>
    <div name="content" id="content">
        <?php echo $sf_content ?>
    </div>
    <div id="footer"><a href="http://www.orangehrm.com" target="_blank" tabindex="37">OrangeHRM</a> Web Upgrade Wizard ver 0.2 &copy; OrangeHRM Inc 2005 - 2012 All rights reserved. </div>
    </div>
    
</body>
</html>

