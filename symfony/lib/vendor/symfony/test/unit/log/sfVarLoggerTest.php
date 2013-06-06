<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(9);

$dispatcher = new sfEventDispatcher();

$buffer = fopen('php://memory', 'rw');
$logger = new sfVarLogger($dispatcher);

$logger->log('foo');
$logger->log('{sfFoo} bar', sfLogger::ERR);

$logs = $logger->getLogs();
$t->is(count($logs), 2, 'sfVarLogger logs all messages into its instance');

$t->is($logs[0]['message'], 'foo', 'sfVarLogger returns an array with the message');
$t->is($logs[0]['priority'], 6, 'sfVarLogger returns an array with the priority');
$t->is($logs[0]['priority_name'], 'info', 'sfVarLogger returns an array with the priority name');
$t->is($logs[0]['type'], 'sfOther', 'sfVarLogger returns an array with the type');

$t->is($logs[1]['message'], 'bar', 'sfVarLogger returns an array with the message');
$t->is($logs[1]['priority'], 3, 'sfVarLogger returns an array with the priority');
$t->is($logs[1]['priority_name'], 'err', 'sfVarLogger returns an array with the priority name');
$t->is($logs[1]['type'], 'sfFoo', 'sfVarLogger returns an array with the type');
