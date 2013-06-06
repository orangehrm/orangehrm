<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
$fixtures = 'fixtures/fixtures.yml';
require_once(dirname(__FILE__).'/../bootstrap/functional.php');

$t = new lime_test(22);

$authors = Doctrine_Core::getTable('Author')->findAll();
$t->is(count($authors), 2);

$author = new Author();

// Accessor overriding
$author->setName('Jonathan H. Wage');
$author->save();

// Propel style accessors with column name
$t->is($author->getName(), $author->name);

// Propel style accessors for id
// Also check new author was not created since Jonathan H. Wage exists in fixtures/fixtures.yml
$t->is($author->getId(), 1);

// Make sure we still have only 2 authors
$authors = Doctrine_Core::getTable('Author')->findAll();
$t->is(count($authors), 2);

$article = new Article();
$article->title = 'test';

// __toString() automatic column finder
$t->is((string) $article, 'test');

// Different style accessors
$t->is($article->getAuthor_id(), $article->author_id);
$t->is($article->getAuthorId(), $article->author_id);
$t->is($article->getauthorId(), $article->author_id);
$t->is($article->getAuthorID(), $article->author_id);
$t->is($article->getauthor_id(), $article->author_id);

// Camel case columns
$camelCase = new CamelCase();
$camelCase->testCamelCase = 'camel';
$camelCase->setTestCamelCase('camel');

$t->is($camelCase->getTestCamelCase(), 'camel');
$t->is($camelCase->gettestCamelCase(), 'camel');
$t->is($camelCase->gettestcamelcase(), 'camel');
$t->is($camelCase->gettest_camel_case(), 'camel');
$t->is($camelCase->getTest_camel_case(), 'camel');

// Propel style accessors work with relationships
$article->setAuthor($author);
$t->is($article->Author, $author);
$t->is($article->getAuthor(), $author);

// Camel case with relationships
$t->is($article->getCamelCase()->getTable()->getOption('name'), 'CamelCase');

// Test getDateTimeObject()
$dateTime = $article->getDateTimeObject('created_at');
$t->is($dateTime instanceof DateTime, true);
$t->is($dateTime->format('m/d/Y'), date('m/d/Y'));

try {
  $article->getDateTimeObject('author_id');
  $t->fail();
} catch (Exception $e) {
  $t->pass();
}

$article->setDateTimeObject('created_at', new DateTime('1985-09-01'));
$t->is($article->getDateTimeObject('created_at')->format('m/d/Y'), '09/01/1985');