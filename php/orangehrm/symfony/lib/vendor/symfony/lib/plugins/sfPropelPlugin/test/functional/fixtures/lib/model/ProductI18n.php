<?php

/**
 * Subclass for representing a row from the 'product_i18n' table.
 *
 * 
 *
 * @package    lib.model
 * @subpackage model
 */
class ProductI18n extends BaseProductI18n
{
	/**
	 * Initializes internal state of ProductI18n object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

}
