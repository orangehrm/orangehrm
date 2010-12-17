<?php

require_once dirname(__FILE__).'/../../config/ProjectConfiguration.class.php';

new ProjectConfiguration();

class AllTests
{
  public static function suite()
  {     
    $suite = new sfBasePhpunitTestSuite();
    
    $suite->addTestSuite(sfPhpunitProjectTestLoader::factory()->getSuite());
    
    $plugins = ProjectConfiguration::getActive()->getPluginPaths();
    $suite->addTestSuite(sfPhpunitPluginTestLoader::factory($plugins)->suite());
    
    return $suite; 
  }
}