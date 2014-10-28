<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../bootstrap/unit.php';
require_once dirname(__FILE__).'/../sfContextMock.class.php';

$t = new lime_test(1);

$context = sfContext::getInstance(array());
$dispatcher = new sfEventDispatcher();
$logger = new sfWebDebugLogger($dispatcher);

// ->handlePhpError()
$t->diag('->handlePhpError()');

$error = error_get_last();
$logger->handlePhpError(E_NOTICE, '%', __FILE__, __LINE__);
$t->is_deeply(error_get_last(), $error, '->handlePhpError() works when message has a "%" character');
