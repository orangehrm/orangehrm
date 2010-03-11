<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
$fixtures = 'fixtures/fixtures.yml';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestFunctional(new sfBrowser(), null, array('propel' => 'sfTesterPropel'));

ArticlePeer::doDeleteAll();
$c = CategoryPeer::doSelectOne(new Criteria());
$hash = spl_object_hash($c);

$b->
  get('/pooling/addArticleButDontSave/category_id/'.$c->getId())->
  with('propel')->check('Article', null, 0)->

  get('/pooling/addArticleAndSave/category_id/'.$c->getId())->
  with('propel')->check('Article', null, 1)
;
