<?php

$app = 'frontend';
include dirname(__FILE__).'/../../bootstrap/functional.php';

$t = new lime_test(16);

class TestFormFilter extends ArticleFormFilter
{
  public $processedFields = array();

  public function configure()
  {
    $this->setWidgets(array(
      'name'        => new sfWidgetFormInputText(),
      'nomethod_bc' => new sfWidgetFormInputText(),
      'nomethod'    => new sfWidgetFormInputText(),
      'author_id'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'name'        => new sfValidatorPass(),
      'nomethod_bc' => new sfValidatorPass(),
      'nomethod'    => new sfValidatorPass(),
      'author_id'   => new sfValidatorPass(),
    ));
  }

  public function addNameColumnQuery($query, $field, $value)
  {
    $this->processedFields[] = $field;
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'body'        => 'Invalid',
      'nomethod_bc' => 'Text',
      'author_id'   => 'Number',
    ));
  }
}

$t->diag('->getQuery()');

$filter = new ArticleFormFilter();
$filter->bind(array());
$t->isa_ok($filter->getQuery(), 'Doctrine_Query', '->getQuery() returns a Doctrine_Query object');

$query = Doctrine_Query::create()->select('title, body');

$filter = new ArticleFormFilter(array(), array('query' => $query));
$filter->bind(array());
$t->is_deeply($filter->getQuery()->getDqlPart('select'), array('title, body'), '->getQuery() uses the query option');
$t->ok($filter->getQuery() !== $query, '->getQuery() clones the query option');

// BC with symfony 1.2
$filter = new TestFormFilter();
$filter->bind(array('nomethod_bc' => 'nomethod_bc'));
try
{
  $filter->getQuery();
  $t->fail('->getQuery() throws an exception if a field that is not a real column is specified in getFields() but a column method does not exist');
}
catch (Exception $e)
{
  $t->pass('->getQuery() throws an exception if a field that is not a real column is specified in getFields() but a column method does not exist');
}

// BC with symfony 1.2
$filter = new TestFormFilter();
$filter->bind(array('body' => 'body'));
try
{
  $filter->getQuery();
  $t->fail('->getQuery() throws an exception if a field is a real column and neither a column nor type method exists');
}
catch (Exception $e)
{
  $t->pass('->getQuery() throws an exception if a field is a real column and neither a column nor type method exists');
}

// BC with symfony 1.2
$filter = new TestFormFilter();
$filter->bind(array('nomethod' => 'nomethod'));
try
{
  $filter->getQuery();
  $t->pass('->getQuery() does not throw an exception when a value without a query method is passed');
}
catch (Exception $e)
{
  $t->fail('->getQuery() does not throw an exception when a value without a query method is passed');
}

// new in symfony 1.3
$filter = new TestFormFilter();
$filter->bind(array('name' => 'Kris Wallsmith'));
$filter->getQuery();
$t->is_deeply($filter->processedFields, array('name'), '->getQuery() processes fields not specified in getFields()');

// pass 0 to number filter
$filter = new TestFormFilter();
$filter->bind(array('author_id' => array('text' => 0)));
$query = $filter->getQuery();
$t->is(trim($query->getDql()), 'FROM Article r WHERE r.author_id = ?', '->getQuery() filters by a 0 number');
$t->is($query->getFlattenedParams(), array(0), '->getQuery() filters by a 0 number');

$filter = new ArticleFormFilter();
$filter->bind(array('type' => array('is_empty' => '1', 'text' => '')));
$query = $filter->getQuery();
$t->is(trim($query->getDql()), 'FROM Article r WHERE (r.type IS NULL OR r.type = ?)', '->getQuery() tests for null or empty text fields');
$t->is($query->getFlattenedParams(), array(''), '->getQuery() tests for null or empty text fields');

$filter = new ArticleFormFilter();
$filter->bind(array('views' => array('is_empty' => '1', 'text' => '')));
$query = $filter->getQuery();
$t->is(trim($query->getDql()), 'FROM Article r WHERE (r.views IS NULL OR r.views = ?)', '->getQuery() tests for null or empty number fields');
$t->is($query->getFlattenedParams(), array(''), '->getQuery() tests for null or empty number fields');

$t->diag('->setTableMethod()');

$filter = new ArticleFormFilter();
$filter->setTableMethod('getNewQuery');
$filter->bind(array());
$t->is_deeply($filter->getQuery()->getDqlPart('select'), array('title, body'), '->setTableMethod() specifies a method that can return a new query');

$filter = new ArticleFormFilter();
$filter->setTableMethod('filterSuppliedQuery');
$filter->bind(array());
$t->is_deeply($filter->getQuery()->getDqlPart('select'), array('title, body'), '->setTableMethod() specifies a method that can modify the supplied query');

$filter = new ArticleFormFilter();
$filter->setTableMethod('filterSuppliedQueryAndReturn');
$filter->bind(array());
$t->is_deeply($filter->getQuery()->getDqlPart('select'), array('title, body'), '->setTableMethod() specifies a method that can modify and return the supplied query');
