<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/../lib/util/sfToolkit.class.php');
require_once($_test_dir.'/../lib/util/sfInflector.class.php');

$t = new lime_test(7);

// ::camelize()
$t->diag('::camelize()');
$t->is(sfInflector::camelize('symfony'), 'Symfony', '::camelize() upper-case the first letter');
$t->is(sfInflector::camelize('symfony_is_great'), 'SymfonyIsGreat', '::camelize() upper-case each letter after a _ and remove _');

// ::underscore()
$t->diag('::underscore()');
$t->is(sfInflector::underscore('Symfony'), 'symfony', '::underscore() lower-case the first letter');
$t->is(sfInflector::underscore('SymfonyIsGreat'), 'symfony_is_great', '::underscore() lower-case each upper-case letter and add a _ before');
$t->is(sfInflector::underscore('HTMLTest'), 'html_test', '::underscore() lower-case all other letters');

// ::humanize()
$t->diag('::humanize()');
$t->is(sfInflector::humanize('symfony'), 'Symfony', '::humanize() upper-case the first letter');
$t->is(sfInflector::humanize('symfony_is_great'), 'Symfony is great', '::humanize() replaces _ by a space');
