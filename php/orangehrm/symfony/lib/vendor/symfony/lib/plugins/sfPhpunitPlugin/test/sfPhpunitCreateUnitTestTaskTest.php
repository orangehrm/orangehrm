<?php
/*
 * Test for checking if the stub classes for unit tests are generated correctly from the tasks.
 *
 * Run this test via:
 * php symfony test:unit sfPhpunitCreateUnitTestTask
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


$t = new lime_test(13, new lime_output_color());

$dispatcher = new sfEventDispatcher();
$formatter = new sfFormatter();


// cleanup
exec('rm -rf apps/'.$application);

$bootstrapFile = $root.'/test/phpunit/bootstrap/unit.php';
$template = $root.'/plugins/sfPhpunitPlugin/data/test/sfPhpunitPluginTestClass.tpl';
$source = $root.'/lib/sfPhpunitPluginTestClass.php';
// baseclass is always saved in the phpunit main folder
$baseTestClass = $root.'/test/phpunit/BasePhpunitTestCase.class.php';
// all unit tests are saved in the unit subfolder
$testClass = $root.'/test/phpunit/unit/sfPhpunitPluginTestClassTest.php';
$testClass2 = $root.'/test/phpunit/unit/subfolder/sfPhpunitPluginTestClassTest.php';
$allTestsFile = $root.'/test/phpunit/AllPhpunitTests.php';


$customTemplateSource = $root.'/plugins/sfPhpunitPlugin/data/test/file.tpl';
$customTemplateDir = $root.'/data/sfPhpunitPlugin/unit';
if (!file_exists($customTemplateDir))
{
	mkdir($customTemplateDir, 0777, true);
}
$customTemplate = $customTemplateDir.'/file.tpl';

// cleanup
@unlink($bootstrapFile);
@unlink($baseTestClass);
@unlink($customTemplate);
@unlink($allTestsFile);
// @unlink($testClass);
// @unlink($testClass2);


// copy the test class
copy($template, $source);

// create the test application
$task = new sfGenerateAppTask($dispatcher, $formatter);
$task->run(array($application));

// --------------------------------------------
// 1. unit test generation (common tests)
// --------------------------------------------
try
{
	$arguments = array($application);
	$options = array('overwrite', '--class=sfPhpunitPluginTestClass', '--class_path=lib');

	// create functional test file
	// with default base class
	$task = new sfPhpunitCreateTestTask($dispatcher, $formatter);
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
$t->ok(file_exists($baseTestClass), 'generated base test class exists');
$t->ok(_syntax_check($baseTestClass), 'php syntax check of base test class is ok');
// END: check base class

$t->ok(file_exists($allTestsFile), 'AllTests file is generated when it does not exist, although the skip_alltests option is set');

// START: check test class
$t->ok(file_exists($testClass), 'generated unit test file exists');
$t->ok(_syntax_check($testClass), 'php syntax check is ok');

if (!$content = file_get_contents($testClass))
{
	$t->fail('could not load test file');
}
$t->ok(strstr($content, "\$this->o = new sfPhpunitPluginTestClass()"), 'first test content check is ok');
$t->ok(strstr($content, "\$this->markTestIncomplete("), 'method stub is ok');
// custom template content should not exist in the generated content now
$t->ok(!strstr($content, "This is a custom template"), 'task uses plugin templates');

$t->ok(strstr($content, "realpath(dirname(__FILE__).'/..')"), 'relative path is ok');
// END: check test class


// copy a custom template
// the next task should use this file and not the one in the plugin dir

copy($customTemplateSource, $customTemplate);


// --------------------------------------------
// 2. unit test generation (custom template test)
// --------------------------------------------
try
{
	$arguments = array($application);
	$options = array('overwrite', '--class=sfPhpunitPluginTestClass', '--class_path=lib');

	// create functional test file
	// with default base class
	$task = new sfPhpunitCreateTestTask($dispatcher, $formatter);
	$task->run($arguments, $options);
}
catch (Exception $e)
{
	$t->fail($e->getMessage());
}

if (!$content = file_get_contents($testClass))
{
	$t->fail('could not load test file');
}
$t->ok(strstr($content, "This is a custom template"), 'task uses custom templates');


// --------------------------------------------
// 3. unit test generation (target test)
// --------------------------------------------
@unlink($baseTestClass);

try
{
	$arguments = array($application);
	$options = array('overwrite', '--overwrite_alltests', '--overwrite_base_test', '--target=subfolder', '--class=sfPhpunitPluginTestClass', '--class_path=lib');

	// create functional test file
	// with default base class
	$task = new sfPhpunitCreateTestTask($dispatcher, $formatter);
	$task->run($arguments, $options);
}
catch (Exception $e)
{
	$t->fail($e->getMessage());
}

if (!$content = file_get_contents($testClass2))
{
	$t->fail('could not load test file');
}
$t->ok(strstr($content, "realpath(dirname(__FILE__).'/../..')"), 'relative path is ok');


// cleanup again
exec('rm -rf apps/'.$application);
exec('rm -rf data/sfPhpunitPlugin/');
exec('rm -rf test/functional/'.$application);
exec('rm -rf test/phpunit/unit/subfolder');
exec('rm -f '.$testClass);
