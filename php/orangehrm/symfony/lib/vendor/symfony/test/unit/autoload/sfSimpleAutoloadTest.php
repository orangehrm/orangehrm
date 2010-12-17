<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please component the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(1);

$autoload = sfSimpleAutoload::getInstance();
$autoload->addFile(dirname(__FILE__).'/../sfEventDispatcherTest.class.php');
$autoload->register();

$t->is(class_exists('myeventdispatchertest'), true, '"sfSimpleAutoload" is case insensitive');
