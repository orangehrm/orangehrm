<?php
/**
 * Initialize testing system
 *
 * @package    sfPhpunitPlugin
 * @subpackage task
 *
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class sfPhpunitInitTestTask extends sfPhpunitCreateBaseTask
{
    /**
     * SetUp Task
     */
    protected function configure()
    {
        $this->addOptions(array(
            new sfCommandOption('overwrite', 'o', sfCommandOption::PARAMETER_NONE, 'Overwrite existing test files (Default: no)'),
            new sfCommandOption('verbose',   'v', sfCommandOption::PARAMETER_NONE, 'Print extra information'),
        ));

        $this->namespace = 'phpunit';
        $this->name = 'init';
        $this->briefDescription = 'Prepare environment in "test" dir.';

        $this->detailedDescription = trim("
            The [phpunit:init] loads required files into `test` dir.
        ");
    }


    /**
     * Run
     */
    protected function execute($arguments = array(), $options = array())
    {
        $this->createBootstrapFile('bootstrap', $options);
        $this->createAllTestFile($options);
        $this->createBaseTestCaseClassFile($options, 'unit/BasePhpunitTestCase', 'myUnitTestCase');
        $this->createBaseTestCaseClassFile($options, 'functional/BasePhpunitFunctionalTestCase', 'myFunctionalTestCase');

        // Unit example
        $file = sfConfig::get('sf_test_dir').'/unit/ExampleTest.php';
        if ($this->createFile($file, 'unit/example', array(), $options)) {
            $this->logSection('phpunit', sprintf('Created %s file.', $file));
        }

        // Functional example
        $file = sfConfig::get('sf_test_dir').'/functional/frontend/ExampleModuleActionTest.php';
        if ($this->createFile($file, 'functional/example', array(), $options)) {
            $this->logSection('phpunit', sprintf('Created %s file.', $file));
        }

        return true;
    }

}
