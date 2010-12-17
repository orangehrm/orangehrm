<?php
// This is a custom template
require_once realpath(dirname(__FILE__).'{relPath}').'/{baseTestName}.class.php';

/**
 * Unit test case for class {testClassName}
 */
class {testClassName} extends {baseTestName}
{
	/**
	* {className}
	*
	* @var {className}
	*/
	protected $o;

	protected function _start()
	{
		$this->o = new {className}();
	}
{methods}
	protected function _end()
	{
	}

}