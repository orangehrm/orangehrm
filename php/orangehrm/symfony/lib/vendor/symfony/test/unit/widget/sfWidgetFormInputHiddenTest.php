<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(2, new lime_output_color());

$w = new sfWidgetFormInputHidden();

// ->render()
$t->diag('->render()');
$t->is($w->render('foo'), '<input type="hidden" name="foo" id="foo" />', '->render() renders the widget as HTML');

// ->isHidden()
$t->diag('->isHidden()');
$t->is($w->isHidden(), true, '->isHidden() returns true');
