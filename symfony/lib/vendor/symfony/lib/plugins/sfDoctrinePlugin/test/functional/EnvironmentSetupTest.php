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

$t = new lime_test(12);

// Make sure Author records were populated properly
$q = Doctrine_Query::create()
  ->from('Author a');
$results = $q->fetchArray();

$t->is(count($results), 2);
$t->is($results[0]['name'], 'Jonathan H. Wage');
$t->is($results[1]['name'], 'Fabien POTENCIER');

// Make sure data fixtures were loaded
$q = Doctrine_Query::create()
  ->from('Article a')
  ->leftJoin('a.Translation t');

$articles = $q->fetchArray();
$t->is($articles[0]['Translation']['en']['title'], 'English Title');

$manager = Doctrine_Manager::getInstance();
$conn1 = $manager->getConnection('doctrine1');
$conn2 = $manager->getConnection('doctrine2');
$conn3 = $manager->getConnection('doctrine3');

// Make sure all connections are created properly from databases.yml
$t->is(count($manager), 3);
$t->is($conn1->getOption('dsn'), 'sqlite:' . str_replace(DIRECTORY_SEPARATOR, '/', sfConfig::get('sf_data_dir')) . '/database1.sqlite');
$t->is($conn2->getOption('dsn'), 'sqlite:' . str_replace(DIRECTORY_SEPARATOR, '/', sfConfig::get('sf_data_dir')) . '/database2.sqlite');
$t->is($conn3->getOption('dsn'), 'sqlite:' . str_replace(DIRECTORY_SEPARATOR, '/', sfConfig::get('sf_data_dir')) . '/database3.sqlite');

// Set globally by ProjectConfiguration::configureDoctrine()
$t->is($manager->getAttribute(Doctrine_Core::ATTR_VALIDATE), true);

// We disable validation for the doctrine2 connection in ProjectConfiguration::configureDoctrineConnectionDoctrine2()
$t->is($conn2->getAttribute(Doctrine_Core::ATTR_VALIDATE), false);

// We set export attribute on the connection in databases.yml
$t->is($conn3->getAttribute(Doctrine_Core::ATTR_EXPORT), Doctrine_Core::EXPORT_TABLES);

$article = new ReflectionClass('Article');
$parent = new ReflectionClass('myDoctrineRecord');
$t->is($article->isSubclassOf($parent), true);