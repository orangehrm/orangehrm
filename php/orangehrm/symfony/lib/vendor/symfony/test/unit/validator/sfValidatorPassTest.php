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

$v = new sfValidatorPass();

// ->clean()
$t->diag('->clean()');
$t->is($v->clean(''), '', '->clean() always returns the value unmodified');
$t->is($v->clean(null), null, '->clean() always returns the value unmodified');
