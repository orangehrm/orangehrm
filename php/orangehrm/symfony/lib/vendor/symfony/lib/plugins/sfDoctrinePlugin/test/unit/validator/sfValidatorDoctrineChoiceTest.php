<?php

$app = 'frontend';
$fixtures = 'fixtures/fixtures.yml';
include dirname(__FILE__).'/../../bootstrap/functional.php';

$t = new lime_test(1);

// ->clean()
$t->diag('->clean()');

$query = Doctrine_Core::getTable('Author')->createQuery();
$validator = new sfValidatorDoctrineChoice(array('model' => 'Author', 'query' => $query));

$author = Doctrine_Core::getTable('Author')->createQuery()->limit(1)->fetchOne();
$validator->clean($author->id);

$t->is(trim($query->getDql()), 'FROM Author', '->clean() does not change the supplied query object');
