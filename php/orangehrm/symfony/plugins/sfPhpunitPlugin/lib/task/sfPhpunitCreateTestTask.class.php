<?php
/**
 * Task for creating functional test files for PHPUnit testing
 *
 * @package    sfPhpunitPlugin
 * @subpackage task
 *
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitCreateTestTask extends sfBasePhpunitCreateTask
{
	/**
	 * @see sfTask
	 */
	protected function configure()
	{ 
	  parent::configure();
	  
		$this->addArguments(array(
      new sfCommandArgument('className', sfCommandArgument::REQUIRED, 'The generated class name'),
      new sfCommandArgument('targetDir', sfCommandArgument::OPTIONAL, 'The path where generate the testcase class', null),
    ));
    $this->addOptions(array(
      new sfCommandOption('source', null, sfCommandOption::PARAMETER_OPTIONAL, 'The template name that will be used for rendering', 'unit/TestCase.tpl'),
      new sfCommandOption('parentName', null, sfCommandOption::PARAMETER_OPTIONAL , 'The parent class name', 'sfBasePhpunitTestCase'),
      new sfCommandOption('fixtureType', null, sfCommandOption::PARAMETER_OPTIONAL , 'The fixtures type that can be used in this test', null),
    ));
	  
	  $this->namespace = 'phpunit';
		$this->name = 'create';
		$this->briefDescription = 'Creates a stub testcase class';

		$this->detailedDescription = <<<EOF
The [phpunit:create-functional] task creates a test class of a module for PHPUnit testing
EOF;
	}

	/**
	 * @see sfTask
	 */
	protected function execute($arguments = array(), $options = array())
	{	  
	  parent::execute($arguments, $options);
	  
    $this->_runInitTask();
    
    $interfaces = '';
    if (!is_null($options['fixtureType'])) {
      $map = array(
        'propel' => 'sfPhpunitFixturePropelAggregator',
        'doctrine' => 'sfPhpunitFixtureDoctrineAggregator',
        'dbunit' => 'sfPhpunitFixtureDbUnitAggregator');
      if (!array_key_exists($options['fixtureType'], $map)) {
        throw new Exception('The fixtureType option can be only `'.implode('`, `',array_keys($map)).'`. You give: `'.$options['fixtureType'].'`');
      }
      
      $interfaces = 'implements '.$map[$options['fixtureType']];
    }
	  
	  $this->_createClass($arguments['targetDir'],$options['source'],array(
      'className' => $arguments['className'],
      'parentName' => $options['parentName'],
	    'interfaces' => $interfaces));
	}
}