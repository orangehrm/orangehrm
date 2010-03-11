<?php

/**
 * Subclass for representing a row from the 'product' table.
 *
 * 
 *
 * @package    lib.model
 * @subpackage model
 */
class Product extends BaseProduct
{
	/**
	 * Initializes internal state of Product object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

}
