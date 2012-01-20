<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(6);

$t->diag('sfTimer starting and stopping');
$timer = new sfTimer();
$timer->addTime();
sleep(1);
$timer->addTime();
$t->is($timer->getCalls(), 2, '->getCalls() returns the amount of addTime() calls');
$t->ok($timer->getElapsedTime() > 0, '->getElapsedTime() returns a value greater than zero. No precision is tested by the unit test to avoid false alarms');

$t->diag('sfTimerManager');
$timerA = sfTimerManager::getTimer('timerA');
$timerB = sfTimerManager::getTimer('timerB');
$t->isa_ok($timerA, 'sfTimer', '::getTimer() returns an sfTimer instance');
$timers = sfTimerManager::getTimers();
$t->is(count($timers), 2, '::getTimers() returns an array with the timers created by the timer manager');
$t->is($timers['timerA'], $timerA, '::getTimers() returns an array with keys being the timer name');
sfTimerManager::clearTimers();
$t->is(count(sfTimerManager::getTimers()), 0, '::clearTimers() empties the list of the timer instances');