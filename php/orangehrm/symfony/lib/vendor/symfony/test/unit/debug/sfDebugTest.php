<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(1);

// ::removeObjects()
$t->diag('::removeObjects()');
$objectArray = array('foo', 42, new sfDebug(), array('bar', 23, new lime_test(null)));
$cleanedArray = array('foo', 42, 'sfDebug Object()', array('bar', 23, 'lime_test Object()'));
$t->is_deeply(sfDebug::removeObjects($objectArray), $cleanedArray, '::removeObjects() converts objects to String representations using the class name');