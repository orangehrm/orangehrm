<?php

$root_dir = realpath(dirname(__FILE__).'/../..');
require_once($root_dir.'/lib/vendor/lime/lime.php');
require_once($root_dir.'/lib/util/sfFinder.class.php');

require_once($root_dir.'/lib/autoload/sfCoreAutoload.class.php');
$version = SYMFONY_VERSION;

printf("symfony LOC (%s)\n", $version);
printf("==============%s\n\n", str_repeat('=', strlen($version)));

// symfony core LOC
$total_loc = 0;
$files = sfFinder::type('file')->name('*.php')->prune('vendor', 'plugins')->in($root_dir.'/lib');
foreach ($files as $file)
{
  $total_loc += count(lime_coverage::get_php_lines($file));
}
$files = sfFinder::type('file')->name('*.php')->prune('vendor')->in($root_dir.'/lib/plugins/*/lib');
foreach ($files as $file)
{
  $total_loc += count(lime_coverage::get_php_lines($file));
}

// symfony tests LOC
$total_tests_loc = 0;
$files = sfFinder::type('file')->name('*Test.php')->in(array(
  $root_dir.'/lib/plugins/sfPropelPlugin/test/unit',
  $root_dir.'/lib/plugins/sfPropelPlugin/test/functional',
  $root_dir.'/lib/plugins/sfDoctrinePlugin/test/unit',
  $root_dir.'/lib/plugins/sfDoctrinePlugin/test/functional',
  $root_dir.'/test/unit',
  $root_dir.'/test/functional',
  $root_dir.'/test/other',
));
foreach ($files as $file)
{
  $total_tests_loc += count(lime_coverage::get_php_lines($file));
}

printf("core librairies:           %6d\n", $total_loc);
printf("unit and functional tests: %6d\n", $total_tests_loc);
echo "---------------------------------\n";
printf("ratio tests/librairies:    %5d%%\n", $total_tests_loc / $total_loc * 100);
