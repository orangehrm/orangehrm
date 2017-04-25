<?php
$rootPath = dirname(__FILE__) . "/../../";
$confPath = $rootPath . "lib/confs/Conf.php-distribution";
require_once $confPath;

$conf = new Conf();


$files = array(
    'build/build.xml', 
    'devTools/installer/SPEC/installer.nsi', 
    'devTools/installer/SPEC/main.nsi', 
    'installer/ApplicationSetupUtility.php', 
    'installer/welcome.php', 
    'installer/guide/index.html', 
    'lib/confs/Conf.php-distribution', 
    'orangehrm-quick-start-guide.html', 
    'symfony/apps/orangehrm/templates/_copyright.php', 
    'sysinfo.php',
    'faq.html',
    'installer/registrationMessage.php',
    'symfony/plugins/orangehrmBeaconPlugin/modules/communication/actions/sendBeaconMessageAjaxAction.class.php',
    'symfony/plugins/orangehrmBeaconPlugin/modules/communication/actions/beaconAboutComponent.class.php',
    'symfony/plugins/orangehrmBeaconPlugin/lib/service/BeaconCommunicationsService.php'
);

if ($argc != 3) {
    echo "Usage: php update-version.php [old-version] [new-version]\n\n";
    echo "Example:\n\n";
    echo "php update-version.php 2.6-alpha.3 2.6-alpha.4\n";
    exit();
}

$oldVersion = $argv[1];
$newVersion = $argv[2];

foreach ($files as $file) {
    $fileName = $rootPath . $file;
    $contents = file_get_contents($fileName);
    $contents = str_replace($oldVersion, $newVersion, $contents);
    file_put_contents($fileName, $contents);
}

