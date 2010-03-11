<?php

/*
* This file is part of the symfony package.
* (c) Fabien Potencier <fabien.potencier@symfony-project.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * Promote a user as a super administrator.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardCreateAdminTask.class.php 10127 2008-07-04 21:02:40Z fabien $
 */
class sfPhpunitCreateTestTask extends sfBaseTask
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
    ),
    'doctrine' => array(
    'classFileSuffix' => '.class.php',
    'default_model_path' => 'lib/model/doctrine',
    'default_connection' => 'doctrine',
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
        new sfCommandOption('type', null, sfCommandOption::PARAMETER_REQUIRED , 'Model type (propel,doctrine)', 'propel' ),
        new sfCommandOption('connection', 'c', sfCommandOption::PARAMETER_REQUIRED, 'Database connection name', ''),
        new sfCommandOption('env', 'e', sfCommandOption::PARAMETER_REQUIRED, 'Environment that will be used to load configuration before running tests', 'test'),
        new sfCommandOption('target', null, sfCommandOption::PARAMETER_REQUIRED, 'The location where to save the tests (inside test directory)', 'model'),
        new sfCommandOption('model_path', 'p', sfCommandOption::PARAMETER_REQUIRED, 'Path to look for class files', ''),
        new sfCommandOption('skip_methods', 's', sfCommandOption::PARAMETER_OPTIONAL, 'List of methods to skip (multiple methods separated by comma)', ''),
        new sfCommandOption('alltests', 'a', sfCommandOption::PARAMETER_OPTIONAL, 'Create AllTests class file', 'AllTests.php' ),
        new sfCommandOption('overwrite', 'o', sfCommandOption::PARAMETER_NONE, 'Overwrite existing test files (Default: no)' ),
        new sfCommandOption('verbose', 'v', sfCommandOption::PARAMETER_NONE, 'Print extra information' ),
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

    $options['application'] = $arguments['application'];

    if ( strtolower( $options['alltests'] != '' ) )
    {
        $this->createAllTestFile( $options, $options['overwrite'] );
    }


    if ( empty( $options['model_path'] ) )
    {
        $options['model_path'] = $this->modelTypes[ $options['type'] ]['default_model_path'];
    }

    if ( empty( $options['connection'] ) )
    {
        $options['connection'] = $this->modelTypes[ $options['type'] ]['default_connection'];
    }

    if ( !empty( $options['skip_methods'] ) )
    {
        $methods = explode( ',', $options['skip_methods'] );

        foreach( $methods as $method )
        {
            array_push( $this->skipMethods, $method );
        }
    }

    if ( !empty( $options['model'] ) && $options['model'] != 'All' )
    {
        if ( strpos( $options['model_path'] , ':' ) !== false )
        {
            throw new sfCommandException( ': is not supported in model_path when specifying the model name.');
        }


        $options['libpath'] = $options['model_path'];

        $this->createTestClass( $options );

        return;
    }

    $paths = explode( ':', $options['model_path'] );

    foreach( $paths as $path )
    {
        $options['libpath'] = $path;

        $dir = new DirectoryIterator( $path );

        $this->logSection('phpunit', sprintf('Searching %s', $path ) );

        while($dir->valid())
        {
            if( strpos( $dir, '.php' ) !== false )
            {
                $options['model'] = str_replace( $this->modelTypes[ $options['type'] ]['classFileSuffix'], '', $dir );

                $this->createTestClass( $options );

            }

            $dir->next();
        }

    }

}

private function createTestClass( $arguments )
{
    $methodTplFilename = dirname( __FILE__).'/../../data/method_template.tpl';

    $className = $arguments['model'];

    if ((strpos($arguments['model'],'Table') > 0) && (strpos($arguments['model'],'Table')-strlen($arguments['model'])+5 === 0)){
        $tplFilename = dirname( __FILE__).'/../../data/file_table_template.tpl';
    }else{
        $tplFilename = dirname( __FILE__).'/../../data/file_template.tpl';
    }


    if ( empty( $className ) )
    {
        throw new sfCommandException( 'Model not specified.');
    }

    // if path is relative, add symfony project root path
    if ( $arguments['libpath'][0] != DIRECTORY_SEPARATOR )
    {
        $arguments['libpath'] = sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.$arguments['libpath'];
    }


    $targetDir = sfConfig::get('sf_root_dir').'/test/'.$arguments['target'];

    if ( ! file_exists( $targetDir ) )
    {
        if ( ! mkdir( $targetDir ) )
        {
            $this->logSection('phpunit', sprintf('Created dir %s', $targetDir ) );
            throw new sfCommandException( sprintf( 'Failed to create target directory %s', $targetDir ) );
        }
    }

    $testFile = $targetDir.'/'.$className.'Test.php';

    if ( file_exists( $testFile ) && ! $arguments['overwrite'] )
    {
        if ( $arguments['verbose'] ) $this->logSection('phpunit', sprintf('Skipped existing file %s', basename( $testFile ) ) );
        return;
    }

    $testClass = $className.'Test';


    // if class has interface in name, ignore it.
    if ( stripos( $className, 'interface' ) !== false )
    {
        if ( $arguments['verbose'] ) $this->logSection('phpunit', sprintf('Skipped interface class %s', $className ) );
        return;
    }

    $classFile = $className.$this->modelTypes[ $arguments['type'] ]['classFileSuffix'];

    $classFilePath = $arguments['libpath'].DIRECTORY_SEPARATOR.$classFile;
    if ( ! file_exists(  $classFilePath ) )
    {
        throw new sfCommandException( sprintf( 'PHP file %s not found.', $classFilePath ) );
    }

    include_once( $classFilePath );

    $rc = new ReflectionClass($className);

    $methodsOutput = '';

    $methods = $rc->getMethods();

    $methodTemplate = file_get_contents( $methodTplFilename );

    if ( empty( $methodTemplate ) )
    {
        throw new sfCommandException( sprintf( '%s template file is empty.', $methodTplFilename ) );
    }

    foreach ( $methods as $method )
    {
        $methodName = $method->getName();

        // compare filename where method resides to make sure we are not including a method from a parent class.
        // also, skip toString and constructor methods.
        if ( $method->getFileName() == $classFilePath && array_search( $methodName, $this->skipMethods ) === false )
        {
            $vars = array(
            'methodName' => ucfirst( $methodName ),
            );

            $methodsOutput .= $this->renderTemplate( $methodTemplate, $vars );
        }
    }

    // if no methods, then do not create test file
    if ( ! $methodsOutput )
    {
        if ( $arguments['verbose'] ) $this->logSection('phpunit', sprintf('Skipped class %s with no methods', $className ) );
        return;
    }



    $vars = array(
    'application' => $arguments['application'],
    'env' => $arguments['env'],
    'target' => $arguments['target'],
    'connection' => $arguments['connection'],
    'testClassName' => $testClass,
    'className' => $arguments['model'],
    'methods' => $methodsOutput,
    );

    if ( $this->createFile( $testFile, $tplFilename, $vars, $arguments ) )
    {
        $this->logSection('phpunit', sprintf('Created test class %s for %s', $testClass, $className ));
    }

}




private function createAllTestFile( $arguments, $overwrite = false )
{
    $fname = $arguments['alltests'];

    if ( $fname[0] != DIRECTORY_SEPARATOR )
    {
        $fname = sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.$fname;
    }

    $tplFilename  = dirname( __FILE__).'/../../data/alltests_template.tpl';

    $vars = array(
    'application' => $arguments['application'],
    'env' => $arguments['env'],
    'target' => $arguments['target'],
    'connection' => $arguments['connection'],
    );

    if ( $this->createFile( $fname, $tplFilename, $vars, $arguments ) )
    {
        $this->logSection('phpunit', sprintf('Created %s file.', $fname ) );
    }

}

private function createFile( $fileName, $templateFile, $vars, $arguments )
{
    if ( ! $arguments['overwrite'] && file_exists( $fileName ) )
    {
        if ( $arguments['verbose'] ) $this->logSection('phpunit', sprintf('Skipped existing file %s', $fileName ));
        return false;
    }


    $tpl = file_get_contents( $templateFile );

    if ( empty( $tpl ) ) throw new sfCommandException( sprintf( '%s template file is empty.', $templateFile ) );

    //echo  $this->renderTemplate( $tpl, $vars );


    if ( ! $fp = fopen( $fileName, 'w' ) )
    {
        throw new sfCommandException( sprintf( 'Failed to open %s for writing', basename( $fname ) ) );
    }

    fputs( $fp, $this->renderTemplate( $tpl, $vars ) );

    fclose( $fp );

    return true;
}

private function renderTemplate( $content, $vars )
{
    foreach( $vars as $key => $val )
    {
        $content = str_replace( '{'.$key.'}', $val, $content );
    }

    return $content;
}



}
