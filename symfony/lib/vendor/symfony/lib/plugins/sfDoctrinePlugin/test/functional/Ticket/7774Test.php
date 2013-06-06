<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
require_once(dirname(__FILE__).'/../../bootstrap/functional.php');
if (!is_link(sfConfig::get('sf_config_dir').'/doctrine/linked_schema.yml'))
{
  return;
}

$t = new lime_test(1);

$t->is(class_exists('ModelFromLinkedSchema'), true, 'models from symlinked schema files are built');
