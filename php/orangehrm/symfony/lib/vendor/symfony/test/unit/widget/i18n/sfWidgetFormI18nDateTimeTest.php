<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(1);

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->configure()
$t->diag('->configure()');

$w = new sfWidgetFormI18nDateTime(array('culture' => 'fr'));
$t->is($w->getOption('format'), '%date% %time%', '->configure() automatically changes the date format for the given culture');
