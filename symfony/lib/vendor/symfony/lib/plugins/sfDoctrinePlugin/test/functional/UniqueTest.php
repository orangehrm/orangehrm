<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
require_once(dirname(__FILE__).'/../bootstrap/functional.php');

$t = new lime_test(2);

$data = array(
  'unique_test1' => 'test',
  'unique_test2' => 'test',
  'unique_test3' => 'test',
  'unique_test4' => 'test'
);

$uniqueTestForm = new UniqueTestForm();
$uniqueTestForm->bind($data);
$uniqueTestForm->save();

$uniqueTestForm = new UniqueTestForm();
$uniqueTestForm->bind($data);
$t->is($uniqueTestForm->isValid(), false);
$t->is((string) $uniqueTestForm->getErrorSchema(), 'unique_test1 [An object with the same "unique_test1" already exist. An object with the same "unique_test1, unique_test2" already exist.] unique_test4 [An object with the same "unique_test4" already exist.]');
