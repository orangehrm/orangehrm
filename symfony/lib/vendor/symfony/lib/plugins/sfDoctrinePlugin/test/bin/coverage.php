<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define('SYMFONY_LIB_DIR', realpath(dirname(__FILE__).'/../../../..'));

require(SYMFONY_LIB_DIR.'/vendor/lime/lime.php');
require(SYMFONY_LIB_DIR.'/util/sfFinder.class.php');

$h = new lime_harness();
$h->base_dir = realpath(dirname(__FILE__).'/..');

// unit tests
$h->register_glob($h->base_dir.'/unit/*/*Test.php');
$h->register_glob($h->base_dir.'/unit/*/*/*Test.php');

// functional tests
$h->register_glob($h->base_dir.'/functional/*Test.php');
$h->register_glob($h->base_dir.'/functional/*/*Test.php');

$c = new lime_coverage($h);
$c->extension = '.class.php';
$c->verbose = false;
$c->base_dir = realpath(dirname(__FILE__).'/../lib');

$finder = sfFinder::type('file')->name('*.php')->prune('vendor')->prune('test')->prune('data');
$c->register($finder->in($c->base_dir));
$c->run();