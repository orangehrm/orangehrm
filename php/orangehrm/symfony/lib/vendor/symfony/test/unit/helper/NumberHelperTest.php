<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/helper/NumberHelper.php');

$t = new lime_test(7, new lime_output_color());

// format_number()
$t->diag('format_number()');
$t->is(format_number(10012.1, 'en'), '10,012.1', 'format_number() takes a number as its first argument');
//$t->is(format_number(10012.1, 'fr'), '10.012,1', 'format_number() takes a culture as its second argument');

$t->todo('format_number() takes the current user culture if no second argument is given');

// format_currency()
$t->is(format_currency(1200000.00, 'USD', 'en'), 'US$1,200,000.00', 'format_currency() takes a number as its first argument');
$t->is(format_currency(1200000.1, 'USD', 'en'), 'US$1,200,000.10', 'format_currency() takes a number as its first argument');
$t->is(format_currency(1200000.10, 'USD', 'en'), 'US$1,200,000.10', 'format_currency() takes a number as its first argument');
$t->is(format_currency(1200000.101, 'USD', 'en'), 'US$1,200,000.10', 'format_currency() takes a number as its first argument');
$t->is(format_currency('1200000', 'USD', 'en'), 'US$1,200,000.00', 'format_currency() takes a number as its first argument');
