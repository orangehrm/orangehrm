<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/../../../../test/unit/sfContextMock.class.php');
require_once(dirname(__FILE__).'/sfValidatorTestHelper.class.php');

$t = new lime_test(36, new lime_output_color());

$context = sfContext::getInstance();
$v = new sfStringValidator($context);

// ->execute()
$t->diag('->execute()');
$text = 'a random string to test string validator';
$error = null;
$t->ok($v->execute($text, $error), '->execute() returns true if you don\'t define any parameter');

$h = new sfValidatorTestHelper($context, $t);

// min
$t->diag('->execute() - min parameter');
$h->launchTests($v, '123456', true, 'min', null, array('min' => 5));
$h->launchTests($v, '12345', true, 'min', null, array('min' => 5));
$h->launchTests($v, '123', false, 'min', 'min_error', array('min' => 5));

// max
$t->diag('->execute() - max parameter');
$h->launchTests($v, '123', true, 'max', null, array('max' => 5));
$h->launchTests($v, '12345', true, 'max', null, array('max' => 5));
$h->launchTests($v, '123456', false, 'max', 'max_error', array('max' => 5));

// values
$t->diag('->execute() - values parameter');
$h->launchTests($v, 'foo', true, 'values', null, array('values' => array('foo')));
$h->launchTests($v, 'bar', true, 'values', null, array('values' => array('foo', 'bar')));
$h->launchTests($v, 'bar', false, 'values', 'values_error', array('values' => array('foo')));
$h->launchTests($v, 'foo', true, 'values', null, array('values' => 'foo'));

// insensitive
$t->diag('->execute() - insensitive parameter');
$h->launchTests($v, 'Foo', false, 'values', null, array('values' => array('foo')));
$h->launchTests($v, 'Foo', true, 'values', null, array('values' => array('foO'), 'insensitive' => true));
$h->launchTests($v, 'Foo', false, 'values', null, array('values' => array('Bar'), 'insensitive' => true));

// utf-8 support
$t->diag('->execute() - utf-8 support');
$h->launchTests($v, 'été', false, 'min', null, array('min' => 5));
$h->launchTests($v, 'été', true, 'max', null, array('max' => 4));
$h->launchTests($v, 'été', true, 'values', null, array('values' => array('été')));
