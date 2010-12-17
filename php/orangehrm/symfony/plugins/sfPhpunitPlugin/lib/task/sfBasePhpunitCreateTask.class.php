<?php

/**
 * Base class for sfPhpunit tasks which generate test files from templates.
 *
 * @package    sfPhpunitPlugin
 * @subpackage task

 * @author     Pablo Godel <pgodel@gmail.com>
 * @author     Frank Stelzer <dev@frankstelzer.de>
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
abstract class sfBasePhpunitCreateTask extends sfBaseTask
{
  protected 
    $_isVerbose = null,
    $_isOverwrite = null;
  
  protected function configure()
  {   
    $this->addOptions(array(
      new sfCommandOption('overwrite', null, sfCommandOption::PARAMETER_OPTIONAL, 'Overwrite existing test files (Default: false)', 0),
      new sfCommandOption('verbose', null, sfCommandOption::PARAMETER_OPTIONAL, 'Print extra information', 0),
    ));
  }
  
  protected function execute($arguments = array(), $options = array())
  {
    $this->_isVerbose = (bool) $options['verbose'];
    $this->_isOverwrite = (bool) $options['overwrite'];
  }
  
	protected function _createDir($dir)
	{
		if (file_exists($dir)) {
		  if ($this->_isVerbose()) {
		    $this->logSection('phpunit', sprintf('Skipped existing dir %s', $dir));
		  }
			return false;
		}
		
		if (!mkdir($dir, 0777, true)) {
      throw new sfCommandException(sprintf('Failed to create target directory %s', $dir));
    }
    $this->logSection('phpunit', sprintf('Created dir %s', $dir));
    
    return true;
	}
	
  protected function _createSuiteClass($targetDir, $source, array $vars = array())
  {
    if (isset($vars['className']) && strpos($vars['className'],'TestSuite') === false) {
      throw new Exception('Generated suite class name must end `*TestSuite`');
    }
    
    return $this->_createClass($targetDir, $source, $vars);
  }
	
	protected function _createClass($targetDir, $source, array $vars = array())
	{
	  if (!isset($vars['className'])) {
	    throw new Exception('The parameter `className` should be defined in the input vars array');
	  }
//	  if (isset($vars['parentName']) && !class_exists($vars['parentName'],true)) {
//      throw new Exception('The class `'.$vars['parentName'].'` that you try to use as a parent is not exist');
//    }
    
    if (!empty($targetDir)) $targetDir .= '/';
    $target = $targetDir.$vars['className'].'.php';
    
    return $this->_createFile($target, $source, $vars);
	}

  protected function _createFile($target, $source, array $vars = array())
  {
    $target = sfConfig::get('sf_phpunit_dir').'/'.$target;
    
    return $this->_createFileAbsolutePath($target, $source, $vars);
  }
  
  protected function _createFileAbsolutePath($target, $source, array $vars = array())
  {    
    $this->_createDir(dirname($target));
    
    if (!$this->_isOverwrite() && file_exists($target)) {
      if ($this->_isVerbose()) {
        $this->logSection('phpunit', sprintf('Skipped existing file %s', $target));
      }
      
      return false;
    }

    if (!$fp = fopen($target, 'w+')) {
      throw new sfCommandException(sprintf('Failed to open %s for writing', basename($target)));
    }
    fputs($fp, $this->_renderTemplate($source, $vars));
    fclose($fp);

    $this->logSection('phpunit', sprintf('Generate file %s', $target));
    return true;
  }

	/**
	 * Renders a template content and assembles the variable placeholders
	 *
	 * @param string $content the template content
	 * @param array $vars
	 *
	 * @return string the assembled content
	 */
	protected function _renderTemplate($source, array $input_vars = array())
	{
	  $vars = array();
	  foreach($input_vars as $key => $val) {
	    $vars["{{$key}}"] = $val;
		}
		
		return str_replace(
		  array_keys($vars), 
		  array_values($vars), 
		  $this->_getTemplate($source));
	}
	
  /**
   * Returns the (raw) content of a template
   *
   * @param string $source
   * @return string the file content
   */
  protected function _getTemplate($file)
  {
    $pluginDataDir = dirname(__FILE__).'/../../data';
    $projectDataDir = sfConfig::get('sf_data_dir').'/sfPhpunitPlugin'; 
    
    if (file_exists($projectDataDir.'/'.$file)) {
      return file_get_contents($projectDataDir.'/'.$file); 
    } elseif (file_exists($pluginDataDir.'/'.$file)) {
      return file_get_contents($pluginDataDir.'/'.$file); 
    }
    
    throw new Exception('The template file `'.$file.'` was not found');
  }
	
	protected function _isOverwrite()
	{
    return $this->_isOverwrite;	  
	}
	
	protected function _isVerbose()
	{
	  return $this->_isVerbose;
	}
	
	protected function _runInitTask()
	{	  
	  $initTask = new sfPhpunitInitTask($this->dispatcher, $this->formatter);
    $initTask->run(array(), array());
//      '--overwrite' => $this->_isOverwrite(),
//      '--verbose' => $this->_isVerbose()));
	}
}