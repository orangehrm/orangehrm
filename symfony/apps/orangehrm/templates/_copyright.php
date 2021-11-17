<?php 
$rootPath = realpath(dirname(__FILE__)."/../../../../");

if (@include_once $rootPath."/lib/confs/sysConf.php") {
    $conf = new sysConf();
    $version = $conf->getVersion();
}
$prodName = 'OrangeHRM';
$copyrightYear = date('Y');

?>
<?php echo $prodName . ' ' . $version;?><br/>
&copy; 2005 - <?php echo $copyrightYear;?> <a href="http://www.orangehrm.com" target="_blank">OrangeHRM, Inc</a>. All rights reserved.
