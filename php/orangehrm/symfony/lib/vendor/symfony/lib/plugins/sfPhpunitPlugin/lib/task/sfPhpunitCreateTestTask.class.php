<?php
/**
 * Task for creating unit test files for PHPUnit testing
 *
 * @package    sfPhpunitPlugin
 * @subpackage task
 *
 * @author     Pablo Godel <pgodel@gmail.com>
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPhpunitCreateTestTask extends sfPhpunitCreateBaseTask
{
	// list of methods to not create a test method.
	private $skipMethods = array(
    '__toString',
    '__construct',
	);

	private $modelTypes = array(
    'propel' => array(
    'classFileSuffix' => '.php',
    'default_model_path' => 'lib/model',
    'default_connection' => 'propel',
  	'default_target' => 'model',
    'ignored_directory' => array('om', 'map'),
	),
    'doctrine' => array(
    'classFileSuffix' => '.class.php',
    'default_model_path' => 'lib/model/doctrine',
    'default_connection' => 'doctrine',
		'default_target' => 'model',
    'ignored_directory' => array('om', 'map'),
	),
	);

	/**
	 * @see sfTask
	 */
	protected function configure()
	{
		$this->addArguments(array(
		new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'Application that will be used to load configuration before running tests'),
		));

		$this->addOptions(array(
		new sfCommandOption('model', 'm', sfCommandOption::PARAMETER_OPTIONAL, 'The model', 'All'),
		new sfCommandOption('class', 'c', sfCommandOption::PARAMETER_OPTIONAL, 'The class', ''),
		new sfCommandOption('type', null, sfCommandOption::PARAMETER_REQUIRED , 'Model type (propel,doctrine)', 'propel'),
		new sfCommandOption('connection', 'o', sfCommandOption::PARAMETER_REQUIRED, 'Database connection name', ''),
		new sfCommandOption('env', 'e', sfCommandOption::PARAMETER_REQUIRED, 'Environment that will be used to load configuration before running tests', 'test'),
		new sfCommandOption('target', null, sfCommandOption::PARAMETER_REQUIRED, 'The location where to save the tests (inside test directory)', ''),
		new sfCommandOption('model_path', 'p', sfCommandOption::PARAMETER_REQUIRED, 'Path to look for class files(deprecated, use class_path)', ''),
		new sfCommandOption('class_path', 'l', sfCommandOption::PARAMETER_REQUIRED, 'Path to look for class files', ''),
		new sfCommandOption('skip_methods', 's', sfCommandOption::PARAMETER_OPTIONAL, 'List of methods to skip (multiple methods separated by comma)', ''),
		new sfCommandOption('alltests', 'a', sfCommandOption::PARAMETER_OPTIONAL, 'Create AllTests class file', 'AllPhpunitTests.php'),
		new sfCommandOption('overwrite_alltests', null, sfCommandOption::PARAMETER_NONE, 'Flag if the contents of the AllTests class should be overwritten'),
		new sfCommandOption('overwrite', 'w', sfCommandOption::PARAMETER_NONE, 'Overwrite existing test files (Default: no)'),
		new sfCommandOption('verbose', 'v', sfCommandOption::PARAMETER_NONE, 'Print extra information'),
		new sfCommandOption('base_test_name', null, sfCommandOption::PARAMETER_REQUIRED, 'Class name of base test class', 'myUnitTestCase' ),
		new sfCommandOption('overwrite_base_test', null, sfCommandOption::PARAMETER_NONE, 'Flag if the content of base test class should be overwritten'),
		new sfCommandOption('file_suffix', 'x', sfCommandOption::PARAMETER_REQUIRED, 'File suffix/extension, only needed if type option is not defined', '' ),
		));

		$this->namespace = 'phpunit';
		$this->name = 'create';
		$this->briefDescription = 'Creates a stub class of a lib/model class for PHPUnit testing';

		$this->detailedDescription = <<<EOF
The [phpunit:create] task creates a stub class of a lib/model Class to be used by PHPUnit testing
EOF;
	}

	/**
	 * @see sfTask
	 */
	protected function execute($arguments = array(), $options = array())
	{
		// create base phpunit dir
		$phpunitDir= sfConfig::get('sf_test_dir').'/';
		if (!file_exists($phpunitDir))
		{
			if (!mkdir($phpunitDir))
			{
				throw new sfCommandException(sprintf('Failed to create target directory %s', $phpunitDir));
			}
			$this->logSection('phpunit', sprintf('Created dir %s', $phpunitDir));
		}

		$options['application'] = $arguments['application'];

		// create unit bootstrap file
		$this->createBootstrapFile('unit', $options);

		$this->createAllTestFile($options);

		$this->createBaseTestCaseClassFile($options, 'unit/BasePhpunitTestCase', $options['base_test_name']);

		// We have to do some BC stuff here.
		// Only the "model_path" and "model" options were available in the old version.
		// The new version uses the additional "class_path" and "class" options.
		// The old options are switched to the newer ones in the next lines...
		if (!empty($options['model_path']))
		{
			$options['class_path'] = $options['model_path'];
		}

		// still no class_path given? take the default one!
		if (empty($options['class_path']))
		{
			$options['class_path'] = $this->modelTypes[$options['type']]['default_model_path'];
		}

		if (empty($options['connection']))
		{
			$options['connection'] = $this->modelTypes[$options['type']]['default_connection'];
		}

		if (!empty($options['skip_methods']))
		{
			$methods = explode(',', $options['skip_methods']);

			foreach($methods as $method)
			{
				array_push($this->skipMethods, $method);
			}
		}

		// a custom class given?
		if(!empty($options['class']))
		{
			if (strpos($options['class_path'] , ':') !== false)
			{
				throw new sfCommandException(': is not supported in class_path when specifying the class name.');
			}

			$options['libpath'] = $options['class_path'];

			$this->createTestClass($options, $options['target']);

			return;
		}
		// BC
		// a model given?
		elseif (!empty($options['model']) && $options['model'] != 'All')
		{
			if (strpos($options['class_path'] , ':') !== false)
			{
				throw new sfCommandException(': is not supported in class_path when specifying the model name.');
			}

			$options['class'] = $options['model'];
			$options['libpath'] = $options['class_path'];

			// all model tests should be saved in a subfolder
			$subfolder = empty($options['target'])? $this->modelTypes[$options['type']]['default_target'] : $options['target'];
			$this->createTestClass($options, $subfolder);

			return;
		}


		$paths = explode(':', $options['class_path']);

		$namespaces= array();

		foreach($paths as $path)
		{
			$finder= sfFinder::type('directory');

			$ignoredDirs= $this->modelTypes[$options['type']]['ignored_directory'];

			foreach ($ignoredDirs as $ignDir)
			{
				$finder= $finder->not_name($ignDir);
			}

			$dirs= $finder->in($path);

			foreach ($dirs as $dir)
			{
				if (is_dir($dir))
				{
					$namespaces[]= $dir;
				}
			}
		}

		$paths= array_merge($paths, $namespaces);

		foreach ($paths as $path)
		{
			$options['libpath'] = $path;

			$dir = new DirectoryIterator($path);

			$this->logSection('phpunit', sprintf('Searching %s', $path));

			while ($dir->valid())
			{
				if (strpos($dir, '.php') !== false)
				{
					$subfolder = basename(dirname($path.DIRECTORY_SEPARATOR.$dir));

					$suffix = !empty($options['file_suffix'])? $options['file_suffix'] : $this->modelTypes[$options['type']]['classFileSuffix'];
					$options['class'] = str_replace($suffix, '', $dir);

					$this->createTestClass($options, $subfolder);
				}

				$dir->next();
			}

		}

	}

	private function createTestClass($arguments, $subfolder = null)
	{
		$className = $arguments['class'];

		if (empty($className))
		{
			throw new sfCommandException('Class not specified.');
		}

		// if path is relative, add symfony project root path
		if ($arguments['libpath'][0] != DIRECTORY_SEPARATOR)
		{
			$arguments['libpath'] = sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.$arguments['libpath'];
		}

		$targetDir = sfConfig::get('sf_test_dir').'/unit';

		if (!file_exists($targetDir))
		{
			if (!mkdir($targetDir, 0777,  true))
			{
				throw new sfCommandException(sprintf('Failed to create target directory %s', $targetDir));
			}
			$this->logSection('phpunit', sprintf('Created dir %s', $targetDir));
		}

		if ($subfolder)
		{
			$targetDir.= DIRECTORY_SEPARATOR.$subfolder;
		}

		if (!file_exists($targetDir))
		{
			if (!mkdir($targetDir))
			{
				throw new sfCommandException(sprintf('Failed to create target sub directory %s', $targetDir));
			}
			$this->logSection('phpunit', sprintf('Created sub dir %s', $targetDir));
		}

		$testClass = $className.'Test';
		$testFile = $targetDir.'/'.$testClass.'.php';

		// skip already existing files, if no overwrite flag is assigned
		if (file_exists($testFile) && !$arguments['overwrite'])
		{
			if ($arguments['verbose'])
			{
				$this->logSection('phpunit', sprintf('Skipped existing file %s', basename($testFile)));
			}
			return;
		}

		// if class has interface in name, ignore it.
		if (stripos($className, 'interface') !== false)
		{
			if ($arguments['verbose'])
			{
				$this->logSection('phpunit', sprintf('Skipped interface class %s', $className));
			}
			return;
		}

		$suffix = !empty($arguments['file_suffix'])? $arguments['file_suffix'] : $this->modelTypes[$arguments['type']]['classFileSuffix'];

		$classFile = $className.$suffix;

		$classFilePath = $arguments['libpath'].DIRECTORY_SEPARATOR.$classFile;
		if (!file_exists($classFilePath))
		{
			throw new sfCommandException(sprintf('PHP file %s not found.', $classFilePath));
		}

		include_once($classFilePath);

		$rc = new ReflectionClass($className);

		// generate the method stubs
		$methodsOutput = '';

		$methods = $rc->getMethods();

		$methodTplFilename = 'unit/method';
		$methodTemplate = $this->getTemplate($methodTplFilename);

		if (empty($methodTemplate))
		{
			throw new sfCommandException(sprintf('%s template file is empty.', $methodTplFilename));
		}

		foreach ($methods as $method)
		{
			$methodName = $method->getName();

			// compare filename where method resides to make sure we are not including a method from a parent class.
			// also, skip toString and constructor methods.
			if ($method->getFileName() == $classFilePath && array_search($methodName, $this->skipMethods) === false)
			{
				$vars = array(
            'methodName' => ucfirst($methodName),
				);

				$methodsOutput .= $this->renderTemplate($methodTemplate, $vars);
			}
		}

		// if no methods, then do not create test file
		if (!$methodsOutput)
		{
			if ($arguments['verbose'])
			{
				$this->logSection('phpunit', sprintf('Skipped class %s with no methods', $className));
			}
			return;
		}

		if ((strpos($arguments['class'],'Table') > 0) && (strpos($arguments['class'],'Table')-strlen($arguments['class'])+5 === 0))
		{
			$tplFilename = 'unit/file_table';
		}
		else
		{
			$tplFilename = 'unit/file';
		}

		$vars = array(
    'application' => $arguments['application'],
    'env' => $arguments['env'],
    'target' => $arguments['target'],
    'connection' => $arguments['connection'],
    'testClassName' => $testClass,
    'className' => $arguments['class'],
    'methods' => $methodsOutput,
    'baseTestName' => $arguments['base_test_name'],
		// if we have a target (sub folder) defined, the test will be saved one level deeper
    'relPath' => !empty($arguments['target'])? '/../..' : '/..'
    );

    if ($this->createFile($testFile, $tplFilename, $vars, $arguments))
    {
    	$this->logSection('phpunit', sprintf('Created test class %s for %s', $testClass, $className));
    }

	}

}
