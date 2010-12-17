<?php

/**
 * Subclass for representing a row from the 'author_article' table.
 *
 * 
 *
 * @package lib.model
 */ 
class AuthorArticle extends BaseAuthorArticle
{
	/**
	 * Initializes internal state of AuthorArticle object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

}
