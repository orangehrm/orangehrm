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

$v = new sfValidatorDateTime();

$t->is($v instanceof sfValidatorDate, 'sfValidatorDateTime extends sfValidatorDate');

// with_time option
$t->diag('with_time option');
$t->is($v->clean(time()), date('Y-m-d H:i:s', time()), '->clean() validates date with time');
