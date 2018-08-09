<?php 
$rootPath = realpath(dirname(__FILE__)."/../../../../");
require_once $rootPath.'/symfony/lib/vendor/autoload.php';

$versionData = sfYaml::load($rootPath.'/version.yml');
$version = $versionData['version'];
$prodName = 'OrangeHRM';
$copyrightYear = date('Y');

?>
<?php echo $prodName . ' ' . $version;?><br/>
&copy; 2005 - <?php echo $copyrightYear;?> <a href="http://www.orangehrm.com" target="_blank">OrangeHRM, Inc</a>. All rights reserved.
