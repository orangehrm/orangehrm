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

$t = new lime_test(23);

$total = 50;
for ($i = 0; $i < $total; $i++)
{
  $author = new Author();
  $author->name = 'Author #' . $i;
  $author->save();
}

$numPerPage = 25;
$pager = new sfDoctrinePager('Author', $numPerPage);
$pager->setTableMethod('testTableMethod');
$pager->setPage(1);
$pager->init();

$t->is($pager->getQuery()->getSqlQuery(), 'SELECT a.id AS a__id, a.name AS a__name, a.type AS a__type FROM author a WHERE (a.id > 0) LIMIT 25');
$t->ok($pager->isFirstPage());

$pager->setPage(2);
$pager->init();

$t->is($pager->getQuery()->getSqlQuery(), 'SELECT a.id AS a__id, a.name AS a__name, a.type AS a__type FROM author a WHERE (a.id > 0) LIMIT 25 OFFSET 25');
$t->is($pager->getQuery()->count(), $total);
$t->ok($pager->isLastPage());

$results = $pager->getResults();

$t->is(gettype($results), 'object');
$t->is(get_class($results), 'Doctrine_Collection');
$t->is(count($results), $numPerPage);
$t->is($pager->getCountQuery()->count(), $total);

$pager = new sfDoctrinePager('Author', $numPerPage);
$pager->setTableMethod('testTableMethod');
$pager->setPage(1);
$pager->init();

$results = $pager->getResults(Doctrine_Core::HYDRATE_ARRAY);

$t->is(gettype($results), 'array');
$t->is(count($results), $numPerPage);

$pager = new sfDoctrinePager('Author', $numPerPage);
$pager->setTableMethod('testTableMethod2');
$pager->setQuery(Doctrine_Query::create()->from('Author a')->where('a.id < 9999999'));
$pager->setPage(1);
$pager->init();

$t->is($pager->getQuery()->getSqlQuery(), 'SELECT a.id AS a__id, a.name AS a__name, a.type AS a__type FROM author a WHERE (a.id < 9999999 AND a.id > 0) LIMIT 25');

$pager = new sfDoctrinePager('Author', $numPerPage);
$pager->setQuery(Doctrine_Query::create()->from('Author a')->where('a.id < 9999999'));
$pager->setPage(1);
$pager->init();

$t->is($pager->getQuery()->getSqlQuery(), 'SELECT a.id AS a__id, a.name AS a__name, a.type AS a__type FROM author a WHERE (a.id < 9999999) LIMIT 25');

// pager interface
$t->diag('iterator interface');

$pager = new sfDoctrinePager('Author', 10);
$pager->init();
foreach ($pager as $author)
{
  $t->isa_ok($author, 'Author');
}
