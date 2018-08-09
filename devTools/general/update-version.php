<?php
$rootPath = dirname(__FILE__) . "/../../";

$files = array(
    'build/build.xml', 
    'devTools/installer/SPEC/installer.nsi',
    'devTools/installer/SPEC/main.nsi',
    'lib/confs/Conf.php-distribution',
    'installer/guide/index.js',
    'version.yml',
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

