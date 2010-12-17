<?php

class {className} extends sfBasePhpunitTestSuite
  implements sfPhpunitContextInitilizerInterface
{
	/**
	 * Dev hook for custom "setUp" stuff
	 */
	protected function _start()
	{
	  $this->_initFilters();
	}

	/**
	 * Dev hook for custom "tearDown" stuff
	 */
	protected function _end()
	{
	}
	
	protected function _initFilters()
	{
	  $filters = sfConfig::get('app_sfPhpunitPlugin_filter', array());
	  foreach ($filters as $filter) {
	    PHPUnit_Util_Filter::addDirectoryToFilter($filter['path'], $filter['ext']);
	  }
	}
	
	public function getApplication()
	{
	  return '{application}';
	}
}