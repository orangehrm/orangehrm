<?php

class sfPhpunitPropelData extends sfPropelData
{
  protected static $_snapshots = array();
  
  protected $deleteCurrentData = false;
  
  protected function doLoadData($files)
  {
//    $this->object_references = array();
//    $this->maps = array();

    foreach ($files as $file)
    {
      $this->doLoadDataFromFile($file);
    }
  }
  
	/**
	 * (non-PHPdoc)
	 * @see plugins/sfPhpunitPlugin/lib/fixture/data/sfPhpunitDataInterface#getObject($id, $class)
	 */
	public function getObject($id)
	{	
	  if (strpos($id, '_') === false) {
      throw new Exception('The id should match the pattern {class}_{id} but you provide: `'.$id.'`'); 
    }

	  if (!isset($this->object_references[$id])) {
      throw new Exception('The data object with given id `'.$id.'` does not exist');  
    }
    
    $object = $this->object_references[$id];
    $object->reload();
    
		return clone $object;
	}
	
	public function doSnapshot($name)
	{
	  self::$_snapshots[$name] = $this->object_references;
	}
	
	public function loadSnapshot($name)
	{	  
	  $this->object_references = self::$_snapshots[$name];
	}
	
	public function cleanObjects()
	{
		$this->object_references = array();
	}
}