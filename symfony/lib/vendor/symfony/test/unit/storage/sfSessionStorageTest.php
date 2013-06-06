<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';

require_once(dirname(__FILE__).'/../../bootstrap/functional.php');

ob_start();

$_test_dir = realpath(dirname(__FILE__).'/../../');
require_once($_test_dir.'/../lib/vendor/lime/lime.php');

sfConfig::set('sf_symfony_lib_dir', realpath($_test_dir.'/../lib'));

$t = new lime_test(8);

// initialize the storage
try
{
  $storage = new sfSessionStorage();
  $t->pass('->__construct() does not throw an exception when not provided with options');
}
catch (InvalidArgumentException $e)
{
  $t->fail('->__construct() Startup failure');
}


$storage = new sfSessionStorage();
$t->ok($storage instanceof sfStorage, '->__construct() is an instance of sfStorage');

$storage->write('test', 123);

$t->is($storage->read('test'), 123, '->read() can read data that has been written to storage');

// regenerate()
$oldSessionData = 'foo:bar';
$key = md5($oldSessionData);

$storage->write($key, $oldSessionData);
$session_id = session_id();
$storage->regenerate(false);
$t->is($storage->read($key), $oldSessionData, '->regenerate(false) regenerated the session with a different session id - this class by default doesn\'t regen the id');
$t->isnt(session_id(), $session_id, '->regenerate(false) regenerated the session with a different session id');

$storage->regenerate(true);
$t->is($storage->read($key), $oldSessionData, '->regenerate(true) regenerated the session with a different session id and destroyed data');
$t->isnt(session_id(), $session_id, '->regenerate(true) regenerated the session with a different session id');

$storage->remove($key);
$t->is($storage->read($key), null, '->remove() removes data from the storage');

// shutdown the storage
$storage->shutdown();
