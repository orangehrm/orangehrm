<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(1);

$t->ok(class_exists('Doctrine'), 'autoloader loads "Doctrine"');
