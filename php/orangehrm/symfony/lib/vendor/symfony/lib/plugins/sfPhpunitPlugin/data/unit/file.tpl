<?php
require_once realpath(dirname(__FILE__).'{relPath}').'/{baseTestName}.class.php';

/**
 * {testClassName}
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