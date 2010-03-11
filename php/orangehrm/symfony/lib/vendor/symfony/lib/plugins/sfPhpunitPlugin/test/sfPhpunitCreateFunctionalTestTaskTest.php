<?php
/*
 * Test for checking if the stub classes for functional tests are generated correctly from the tasks.
 *
 * Run this test via:
 * php symfony test:unit sfPhpunitCreateFunctionalTestTask
 *
 * @see http://trac.symfony-project.org/browser/branches/1.2/test/unit/task/cache/sfCacheClearTaskTest.php
 *
 * @author Frank Stelzer <dev@frankstelzer.de>
 */

$root = realpath(dirname(__FILE__).'/../..');
include($root.'/test/bootstrap/unit.php');

// this function is used in several tests, but is not shared within a common file
if(!function_exists('_syntax_check'))
{
	function _syntax_check($file)
	{
		$result = exec('php -l '.$file);
		return strstr($result, 'No syntax errors detected in');
	}
}

$application = 'testapp';
$module = 'foo';
$module2 = 'foo2';

// cleanup
exec('rm -rf apps/'.$application);


$t = new lime_test(19, new lime_output_color());

$dispatcher = new sfEventDispatcher();
$formatter = new sfFormatter();


// create the test application
// some chmod warnings are thrown in my env, but they could be ignored for the test!
$task = new sfGenerateAppTask($dispatcher, $formatter);
$task->run(array($application));

$bootstrapFile = $root.'/test/phpunit/bootstrap/functional.php';
$baseTestFile = $root.'/test/phpunit/BasePhpunitFunctionalTestCase.class.php';
$testFile = sprintf($root.'/test/phpunit/functional/%s/%sActionsTest.php', $application, $module);

$baseTestFile2 = $root.'/test/phpunit/BaseSomeTestCase.class.php';
$testFile2 = sprintf($root.'/test/phpunit/functional/%s/%sActionsTest.php', $application, $module2);

$allTestsFile = $root.'/test/phpunit/AllPhpunitTests.php';

// cleanup existing test files
@unlink($bootstrapFile);
@unlink($baseTestFile);
@unlink($testFile);

@unlink($baseTestFile2);
@unlink($testFile2);

@unlink($allTestsFile);

// --------------------------------------------
// 1. functional test generation
// --------------------------------------------
try
{
	$arguments = array($application, $module);
	$options = array('overwrite');

	// create functional test file
	// with default base class
	$task = new sfPhpunitCreateFunctionalTestTask($dispatcher, $formatter);
	$task->run($arguments, $options);
}
catch (Exception $e)
{
	$t->fail($e->getMessage());
}

// START: check bootstrap class
$t->diag('testing bootstrap file generation');
$t->ok(file_exists($bootstrapFile), 'bootstrap file was created');
$t->ok(_syntax_check($bootstrapFile), 'php syntax check of bootstrap file is ok');
// END: check bootstrap class


// START: check base class
$t->diag('testing base test class file generation');
// template var replacing has worked, when no syntax errors occur
$t->ok(file_exists($baseTestFile), 'base test class exists');
$t->ok(_syntax_check($baseTestFile), 'php syntax check of base test class is ok');

if (!$content = file_get_contents($baseTestFile))
{
	$t->fail('could not load test file');
}
$declaration = sprintf('abstract class %s extends sfBasePhpunitFunctionalTestCase', 'BasePhpunitFunctionalTestCase');
$t->ok(strstr($content, $declaration), 'class declaration in base test class is ok');
// END: check base class

$t->ok(file_exists($allTestsFile), 'AllTests file is generated when it does not exist');

// START: check test class
$t->diag('testing test class file generation');
$t->ok(file_exists($testFile), 'created functional test file exists');

if (!$content = file_get_contents($testFile))
{
	$t->fail('could not load test file');
}

$declaration = sprintf('class %sTest extends BasePhpunitFunctionalTestCase', $module . 'Actions');
$t->ok(strstr($content, $declaration), 'class declaration is ok');
$t->ok(_syntax_check($testFile), 'php syntax check is ok');
// END: check test class


// --------------------------------------------
// 2. functional test generation
// --------------------------------------------
$baseClass = 'BaseSomeTestCase';

$t->diag('2. functional test generation is running');
try
{
	// take another module for this test

	$arguments = array($application, $module2);
	$options = array('overwrite', '--base_test_name='.$baseClass);

	// create functional test file
	// with default base class
	$task = new sfPhpunitCreateFunctionalTestTask($dispatcher, $formatter);
	$task->run($arguments, $options);
}
catch ( Exception $e )
{
	$t->fail($e->getMessage());
}

// START: check base class
$t->diag('testing base test class file generation');
// template var replacing has worked, when no syntax errors occur
$t->ok(file_exists($baseTestFile2), 'base test class exists');
$t->ok(_syntax_check($baseTestFile2), 'php syntax check of base test class is ok');

if (!$content = file_get_contents($baseTestFile2))
{
	$t->fail('could not load test file');
}
$declaration = sprintf('abstract class %s extends sfBasePhpunitFunctionalTestCase', $baseClass);
$t->ok(strstr($content, $declaration), 'class declaration in base test class is ok');
// END: check base class


// START: check test class
$t->diag('testing test class file generation');
$t->ok(file_exists($testFile2), 'created functional test file exists');

if (!$content = file_get_contents($testFile2))
{
	$t->fail('could not load test file');
}

$declaration = sprintf('class %sTest extends %s', $module2 . 'Actions', $baseClass);
$t->ok(strstr($content, $declaration), 'class declaration is ok');
$t->ok(_syntax_check($testFile), 'php syntax check is ok');
// END: check test class


// --------------------------------------------
// 3.1 functional test generation (option test for "overwrite_alltests" and "overwrite_base_test")
// --------------------------------------------
$t->diag('3. functional test generation is running');
// should not be overwritten now
file_put_contents($allTestsFile, 'hello world');
file_put_contents($baseTestFile, 'hello you');

try
{
	// take another module for this test

	$arguments = array($application, $module2);
	$options = array('overwrite');

	// create functional test file
	// with default base class
	$task = new sfPhpunitCreateFunctionalTestTask($dispatcher, $formatter);
	$task->run($arguments, $options);
}
catch ( Exception $e )
{
	$t->fail($e->getMessage());
}

$content = file_get_contents($allTestsFile);
$t->is('hello world', $content, 'generation of alltests is skipped, when overwrite_alltests option is not assigned');

// content of $allTestsFile should not be overwritten here
$content = file_get_contents($baseTestFile);
$t->is('hello you', $content, 'generation of base test is skipped, when overwrite_base_test option is not assigned');


// --------------------------------------------
// 3.2 functional test generation (option test for "skip_alltests" and "skip_base_test")
// --------------------------------------------
file_put_contents($allTestsFile, 'hello world');
file_put_contents($baseTestFile, 'hello you');

try
{
	// take another module for this test

	$arguments = array($application, $module2);
	$options = array('overwrite', '--overwrite_alltests', '--overwrite_base_test');

	// create functional test file
	// with default base class
	$task = new sfPhpunitCreateFunctionalTestTask($dispatcher, $formatter);
	$task->run($arguments, $options);
}
catch ( Exception $e )
{
	$t->fail($e->getMessage());
}

// content of $allTestsFile should be overwritten here
$content = file_get_contents($allTestsFile);
$t->isnt('hello world', $content, 'overwrite_alltests option is working');

// content of $allTestsFile should be overwritten here
$content = file_get_contents($baseTestFile);
$t->isnt('hello you', $content, 'overwrite_base_test option is working');


// cleanup again
exec('rm -rf apps/'.$application);
exec('rm -rf test/functional/'.$application);
exec('rm -rf test/phpunit/functional/'.$application);
@unlink($baseTestFile2);



