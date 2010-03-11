<?php
/**
 * Task for creating functional test files for PHPUnit testing
 *
 * @package    sfPhpunitPlugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPhpunitCreateFunctionalTestTask extends sfPhpunitCreateBaseTask
{
	/**
	 * @see sfTask
	 */
	protected function configure()
	{
		$this->addArguments(array(
		new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'Application that will be used to load configuration before running tests'),
		new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'Module which should be tested'),
		));

		$this->addOptions(array(
		new sfCommandOption('env', 'e', sfCommandOption::PARAMETER_REQUIRED, 'Environment that will be used to load configuration before running tests', 'test'),
		new sfCommandOption('overwrite', 'o', sfCommandOption::PARAMETER_NONE, 'Overwrite existing test files (Default: no)' ),
		new sfCommandOption('verbose', 'v', sfCommandOption::PARAMETER_NONE, 'Print extra information' ),
		new sfCommandOption('alltests', 'a', sfCommandOption::PARAMETER_OPTIONAL, 'Create AllTests class file', 'AllPhpunitTests.php'),
		new sfCommandOption('overwrite_alltests', null, sfCommandOption::PARAMETER_NONE, 'Flag if the content of the AllTests class should be overwritten'),
		new sfCommandOption('base_test_name', null, sfCommandOption::PARAMETER_REQUIRED, 'Class name of base test class', 'myFunctionalTestCase' ),
		new sfCommandOption('overwrite_base_test', null, sfCommandOption::PARAMETER_NONE, 'Flag if the content of base test class should be overwritten'),
		));

		$this->namespace = 'phpunit';
		$this->name = 'create-functional';
		$this->briefDescription = 'Creates a functional test class of a module for PHPUnit testing';

		$this->detailedDescription = <<<EOF
The [phpunit:create-functional] task creates a functional test class of a module for PHPUnit testing
EOF;
	}

	/**
	 * @see sfTask
	 */
	protected function execute($arguments = array(), $options = array())
	{
		$options['application'] = $arguments['application'];

		// create functional bootstrap file
		$this->createBootstrapFile('functional', $options);

		$this->createAllTestFile($options);

		// create base functional test class
		// an existing file will not be overwritten, when the "overwrite" option is not set
		$this->createBaseTestCaseClassFile($options, 'functional/BasePhpunitFunctionalTestCase', $options['base_test_name']);

		// test files will be stored in
		// test/phpunit/functional/{$application}/{$module}ActionsTest.php
		$targetDir = sfConfig::get('sf_root_dir').'/test/phpunit/functional/'.$arguments['application'];
		if (!file_exists($targetDir))
		{
			mkdir($targetDir, 0777, true);
		}

		$moduleName = $arguments['module'];


		$testName = $moduleName.'ActionsTest';
		$fileName = $targetDir.'/'.$testName.'.php';
		$vars = array(
			'testName' => $testName,
			'baseTestName' => $options['base_test_name'],
			'moduleName' => $arguments['module'],
			'application' => $arguments['application']
		);

		$this->createFile($fileName, 'functional/PhpunitFunctionalTestCase', $vars, $options);
	}

}
