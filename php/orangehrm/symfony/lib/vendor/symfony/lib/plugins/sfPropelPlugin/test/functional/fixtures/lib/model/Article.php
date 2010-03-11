<?php

/**
 * Subclass for representing a row from the 'article' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Article extends BaseArticle
{
	/**
	 * Initializes internal state of Article object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

}
