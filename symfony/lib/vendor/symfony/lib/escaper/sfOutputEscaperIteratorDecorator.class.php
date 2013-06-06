<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Output escaping iterator decorator.
 *
 * This takes an object that implements the Traversable interface and turns it
 * into an iterator with each value escaped.
 *
 * Note: Prior to PHP 5.1, the IteratorIterator class was not implemented in the
 * core of PHP. This means that although it will still work with classes that
 * implement Iterator or IteratorAggregate, internal PHP classes that only
 * implement the Traversable interface will cause the constructor to throw an
 * exception.
 *
 * @see        sfOutputEscaper
 * @package    symfony
 * @subpackage view
 * @author     Mike Squire <mike@somosis.co.uk>
 * @version    SVN: $Id: sfOutputEscaperIteratorDecorator.class.php 23436 2009-10-29 16:10:39Z fabien $
 */
class sfOutputEscaperIteratorDecorator extends sfOutputEscaperObjectDecorator implements Iterator, ArrayAccess
{
  /**
   * The iterator to be used.
   *
   * @var IteratorIterator
   */
  private $iterator;

  /**
   * Constructs a new escaping iteratoror using the escaping method and value supplied.
   *
   * @param string      $escapingMethod  The escaping method to use
   * @param Traversable $value           The iterator to escape
   */
  public function __construct($escapingMethod, Traversable $value)
  {
    // Set the original value for __call(). Set our own iterator because passing
    // it to IteratorIterator will lose any other method calls.

    parent::__construct($escapingMethod, $value);

    $this->iterator = new IteratorIterator($value);
  }

  /**
   * Resets the iterator (as required by the Iterator interface).
   *
   * @return bool true, if the iterator rewinds successfully otherwise false
   */
  public function rewind()
  {
    return $this->iterator->rewind();
  }

  /**
   * Escapes and gets the current element (as required by the Iterator interface).
   *
   * @return mixed The escaped value
   */
  public function current()
  {
    return sfOutputEscaper::escape($this->escapingMethod, $this->iterator->current());
  }

  /**
   * Gets the current key (as required by the Iterator interface).
   *
   * @return string Iterator key
   */
  public function key()
  {
    return $this->iterator->key();
  }

  /**
   * Moves to the next element in the iterator (as required by the Iterator interface).
   */
  public function next()
  {
    return $this->iterator->next();
  }

  /**
   * Returns whether the current element is valid or not (as required by the
   * Iterator interface).
   *
   * @return bool true if the current element is valid; false otherwise
   */
  public function valid()
  {
    return $this->iterator->valid();
  }

  /**
   * Returns true if the supplied offset isset in the array (as required by the ArrayAccess interface).
   *
   * @param  string $offset  The offset of the value to check existance of
   *
   * @return bool true if the offset isset; false otherwise
   */
  public function offsetExists($offset)
  {
    return isset($this->value[$offset]);
  }

  /**
   * Returns the element associated with the offset supplied (as required by the ArrayAccess interface).
   *
   * @param  string $offset  The offset of the value to get
   *
   * @return mixed The escaped value
   */
  public function offsetGet($offset)
  {
    return sfOutputEscaper::escape($this->escapingMethod, $this->value[$offset]);
  }

  /**
   * Throws an exception saying that values cannot be set (this method is
   * required for the ArrayAccess interface).
   *
   * This (and the other sfOutputEscaper classes) are designed to be read only
   * so this is an illegal operation.
   *
   * @param  string $offset  (ignored)
   * @param  string $value   (ignored)
   *
   * @throws sfException
   */
  public function offsetSet($offset, $value)
  {
    throw new sfException('Cannot set values.');
  }

  /**
   * Throws an exception saying that values cannot be unset (this method is
   * required for the ArrayAccess interface).
   *
   * This (and the other sfOutputEscaper classes) are designed to be read only
   * so this is an illegal operation.
   *
   * @param  string $offset  (ignored)
   *
   * @throws sfException
   */
  public function offsetUnset($offset)
  {
    throw new sfException('Cannot unset values.');
  }
}
