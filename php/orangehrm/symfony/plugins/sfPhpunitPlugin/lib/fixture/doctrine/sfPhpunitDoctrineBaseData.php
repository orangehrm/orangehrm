<?php

class sfPhpunitDoctrineBaseData extends Doctrine_Data_Import
{
  protected $_snapshots = array();
    
  /**
   * Do the importing of the data parsed from the fixtures
   *
   * @return void
   */
  public function doImport($append = false)
  {
    $this->_rows = array();
    
    return parent::doImport($append);
  }
  
  public function cleanObjects()
  {
    $this->_importedObjects = array();
    $this->_rows = array();
    
    $connection = Doctrine_Manager::getInstance()->getCurrentConnection()->clear();
  }
  
  public function doSnapshot($name)
  {
    self::$_snapshots[$name]['importedObjects'] = $this->_importedObjects;
    self::$_snapshots[$name]['rows'] = $this->_rows;
  }
  
  public function loadSnapshot($name)
  {   
    $this->_importedObjects = self::$_snapshots[$name]['importedObjects'];
    $this->_rows = self::$_snapshots[$name]['rows'];
  }
}