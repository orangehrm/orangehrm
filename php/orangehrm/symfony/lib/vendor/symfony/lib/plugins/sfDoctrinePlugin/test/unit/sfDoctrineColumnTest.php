<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(23, new lime_output_color());

$conn = Doctrine_Manager::connection(new Doctrine_Adapter_Mock('mysql'));

class Test extends sfDoctrineRecord
{
  public function setTableDefinition()
  {
    $this->hasColumn('name', 'string', 255, array('notblank' => true));
    $this->hasColumn('test as TEST', 'string', 255);
    $this->hasColumn('email', 'string', 255, array('email' => true, 'notnull' => true));
  }

  public function setUp()
  {
    $this->hasMany('TestRelation as TestRelations', array('local' => 'id', 'foreign' => 'test_id'));
  }
}

class TestRelation extends sfDoctrineRecord
{
  public function setTableDefinition()
  {
    $this->hasColumn('name', 'string', 255);
    $this->hasColumn('test_id', 'integer');
  }

  public function setUp()
  {
    $this->hasOne('Test', array('local' => 'test_id', 'foreign' => 'id'));
  }
}

$column = new sfDoctrineColumn('name', Doctrine::getTable('Test'));
$t->is($column->getName(), 'name');
$t->is($column->getFieldName(), 'name');
$t->is($column->getPhpName(), 'name');
$t->is($column->isNotNull(), true);

$column = new sfDoctrineColumn('test', Doctrine::getTable('Test'));
$t->is($column->getName(), 'test');
$t->is($column->getFieldName(), 'TEST');
$t->is($column->getPhpName(), 'TEST');

$t->is($column->getDoctrineType(), 'string');
$t->is($column->getType(), 'VARCHAR');
$t->is($column->getLength(), 255);
$t->is($column->getSize(), 255);
$t->is($column->hasDefinitionKey('length'), true);
$t->is($column->getDefinitionKey('type'), 'string');
$t->is($column->isNotNull(), false);

// Is not null and has definition key
$column = new sfDoctrineColumn('email', Doctrine::getTable('Test'));
$t->is($column->isNotNull(), true);
$t->is($column->hasDefinitionKey('email'), true);
$t->is($column->getDefinitionKey('email'), true);

// Is primary key
$column = new sfDoctrineColumn('id', Doctrine::getTable('Test'));
$t->is($column->isPrimaryKey(), true);

// Relation/foreign key functions
$column = new sfDoctrineColumn('test_id', Doctrine::getTable('TestRelation'));
$t->is($column->isForeignKey(), true);
$t->is($column->getForeignClassName(), 'Test');
$t->is($column->getForeignTable()->getOption('name'), 'Test');
$t->is($column->getTable()->getOption('name'), 'TestRelation');

// Array access
$t->is($column['type'], 'integer');