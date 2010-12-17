<?php

class sfPhpunitDoctrineData12 extends sfPhpunitDoctrineBaseData
{
	/**
	 * (non-PHPdoc)
	 * @see plugins/sfPhpunitPlugin/lib/fixture/data/sfPhpunitDataInterface#getObject($id, $class)
	 */
	public function getObject($id)
	{
	  if (strpos($id, '_') === false) {
	    throw new Exception('The id should match the pattern {class}_{id} but you provide: `'.$id.'`'); 
	  }
	  
	  list($table, $id) = explode('_', $id, 2);
    $id = '('.strtolower($table).') '.strtolower($id);

   	if (!isset($this->_importedObjects[$id])) {
   	  throw new Exception('The data object with given id `'.$id.'` does not exist');  
   	}
   	
   	return $this->_importedObjects[$id];
	}
}