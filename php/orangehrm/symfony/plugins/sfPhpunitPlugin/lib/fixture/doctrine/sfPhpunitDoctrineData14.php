<?php

class sfPhpunitDoctrineData14 extends sfPhpunitDoctrineBaseData
{
	/**
	 * (non-PHPdoc)
	 * @see plugins/sfPhpunitPlugin/lib/fixture/data/sfPhpunitDataInterface#getObject($id, $class)
	 */
	public function getObject($id)
	{	  
	  if (strpos($id, '_') === false) {
	    throw new Exception('The id should match the pattern {table}_{id} but you provide: `'.$id.'`'); 
	  }
	  
	  list($class, $id) = explode('_', $id, 2);
	  $table = Doctrine_Core::getTable($class)->getTableName();	  
    $id = '('.strtolower($table).') '.$id;

   	if (!isset($this->_importedObjects[$id])) {
   	  throw new Exception('The data object with given id `'.$id.'` does not exist');  
   	}
   	
   	return $this->_importedObjects[$id];
	}
}