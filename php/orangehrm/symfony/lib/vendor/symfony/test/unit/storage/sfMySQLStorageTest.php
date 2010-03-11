<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2008 Dejan Spasic <spasic.dejan@yahoo.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

ob_start();
$plan = 16;
$t = new lime_test($plan, new lime_output_color());

if (!extension_loaded('mysql'))
{
  $t->skip('Mysql extension must be loaded', $plan);
  exit(0);
}

// Configure your database with the settings below in order to run the test
$mysql_config = array(
  'host'     => 'localhost',
  'username' => 'root', 
  'password' => '', 
);

if (!isset($mysql_config))
{
  $t->skip('Mysql credentials needed to run these tests', $plan);
  exit(0);
}

try
{
  // Creating mysql database connection
  $database = new sfMySQLDatabase($mysql_config);
  $connection = $database->getResource();
}
catch (sfDatabaseException $e)
{
  $t->diag($e->getMessage());
  $t->skip('Unable to connect to MySQL database, skipping', $plan);
  exit(0);
}

// Creates test database
mysql_query('DROP DATABASE IF EXISTS sf_mysql_storage_unit_test', $connection);
mysql_query('CREATE DATABASE sf_mysql_storage_unit_test', $connection) or $t->fail('Cannot create database sf_mysql_storage_unit_test');
mysql_select_db('sf_mysql_storage_unit_test', $connection);
mysql_query("CREATE TABLE `session` (
  `sess_id` varchar(40) NOT NULL PRIMARY KEY,
  `sess_time` int(10) unsigned NOT NULL default '0',
  `sess_data` text collate utf8_unicode_ci
) ENGINE=MyISAM", $connection) 
  or $t->fail('Can not create table session');

ini_set('session.use_cookies', 0);
$session_id = "1";

$storage = new sfMySQLSessionStorage(array(
  'db_table'   => 'session',
  'session_id' => $session_id,
  'database'   => $database)
);

$t->ok($storage instanceof sfStorage, 'sfMySQLSessionStorage is an instance of sfStorage');
$t->ok($storage instanceof sfDatabaseSessionStorage, 'sfMySQLSessionStorage is an instance of sfDatabaseSessionStorage');

// regenerate()
$oldSessionData = 'foo:bar';
$storage->sessionWrite($session_id, $oldSessionData);
$storage->regenerate(false);

$newSessionData = 'foo:bar:baz';
$storage->sessionWrite(session_id(), $newSessionData);
$t->isnt(session_id(), $session_id, 'regenerate() regenerated the session with a different session id');

// checking if the old session record still exists
$result = mysql_query(sprintf('SELECT sess_data FROM session WHERE sess_id = "%s"', $session_id), $connection);
$t->is(mysql_num_rows($result), 1, 'regenerate() has kept destroyed old session');
$rSessionData = list($thisSessData) = mysql_fetch_row($result);
$t->is($rSessionData[0], $oldSessionData, 'regenerate() has kept destroyed old session data');

// checking if the new session record has been created
$result = mysql_query(sprintf('SELECT sess_data FROM session WHERE sess_id = "%s"', session_id()), $connection);
$t->is(mysql_num_rows($result), 1, 'regenerate() has created a new session record');
$rSessionData = list($thisSessData) = mysql_fetch_row($result);
$t->is($rSessionData[0], $newSessionData, 'regenerate() has created a new record with correct data');

$session_id = session_id();

// check session data in the database
$result = mysql_query(sprintf('SELECT sess_data FROM session WHERE sess_id = "%s"', $session_id), $connection);
list($thisSessData) = mysql_fetch_row($result);
$t->is(mysql_num_rows($result), 1, 'session is stored in the database');
$t->is($thisSessData, $newSessionData, 'session variables are stored in the database');

mysql_free_result($result);
unset($thisSessData, $result);

// sessionRead()
try
{
  $retrieved_data = $storage->sessionRead($session_id);
  $t->pass('sessionRead() does not throw an exception');
}
catch (Exception $e)
{
  $t->fail('sessionRead() does not throw an exception');
}
$t->is($retrieved_data, $newSessionData, 'sessionRead() reads session data');

// sessionWrite()
$otherSessionData = 'foo:foo:foo';
try
{
  $write = $storage->sessionWrite($session_id, $otherSessionData);
  $t->pass('sessionWrite() does not throw an exception');
}
catch (Exception $e)
{
  $t->fail('sessionWrite() does not throw an exception');
}

$t->ok($write, 'sessionWrite() returns true');
$t->is($storage->sessionRead($session_id), $otherSessionData, 'sessionWrite() wrote session data');

// sessionDestroy()
try
{
  $storage->sessionDestroy($session_id);
  $t->pass('sessionDestroy() does not throw an exception');
}
catch (Exception $e)
{
  $t->fail('sessionDestroy() does not throw an exception');
}

$result = mysql_query(sprintf('SELECT COUNT(sess_id) FROM session WHERE sess_id = "%s"', $session_id), $connection);

list($count) = mysql_fetch_row($result);
$t->is($count, 0, 'session is removed from the database');

mysql_free_result($result);
unset($count, $result);
