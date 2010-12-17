<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
include dirname(__FILE__).'/../../bootstrap/functional.php';
include $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

$t = new lime_test(2);

// ->__construct()
$t->diag('->__construct()');

class DefaultValuesForm extends AuthorForm
{
  public function configure()
  {
    $this->setDefault('name', 'John Doe');
  }
}

$author = new Author();
$form = new DefaultValuesForm($author);
$t->is($form->getDefault('name'), 'John Doe', '->__construct() uses form defaults for new objects');

$author = new Author();
$author->setName('Jacques Doe');
$author->save();
$form = new DefaultValuesForm($author);
$t->is($form->getDefault('name'), 'Jacques Doe', '->__construct() uses object value as a default for existing objects');
$author->delete();
