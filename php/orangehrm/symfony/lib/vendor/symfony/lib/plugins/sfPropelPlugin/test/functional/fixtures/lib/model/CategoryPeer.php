<?php

/**
 * Subclass for performing query and update operations on the 'category' table.
 *
 * 
 *
 * @package lib.model
 */ 
class CategoryPeer extends BaseCategoryPeer
{
  static public function getByName($name)
  {
    $c = new Criteria();
    $c->add(self::NAME, $name);

    return self::doSelectOne($c);
  }
}
