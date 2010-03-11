<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../test/bootstrap.php';

$h = new lime_harness(new lime_output_color());
$h->base_dir = realpath(dirname(__FILE__).'/../test');
$h->register(sfFinder::type('file')->name('*Test.php')->in($h->base_dir));
$h->run();
