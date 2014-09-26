<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
class SimplePager extends sfPager implements Serializable {


  protected $results;

  protected $offset;

  public function __construct($class, $maxPerPage = 10) {
    parent::__construct($class, $maxPerPage);
    $this->offset = null;
  }

  public function setResults($results) {
    $this->results = $results;
  }

  public function setNumResults($count) {
      $this->setNbResults($count);
  }
  
  public function getNumResults(){
  		return $this->getNbResults();
  }

  // function to be called after parameters have been set
  public function init() {

    if ($this->getPage() == 0 || $this->getMaxPerPage() == 0 || $this->getNbResults() == 0) {
      $this->setLastPage(0);
    } else {
      $this->offset = ($this->getPage() - 1) * $this->getMaxPerPage();
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

    }
  }

  public function getOffset() {
      return $this->offset;
  }

  // main method: returns an array of result on the given page
  public function getResults() {
    return $results;
  }


  // used internally by getCurrent()
  protected function retrieveObject($offset) {
    return false;
  }

  /**
   * Serialize the pager object
   *
   * @return string $serialized
   */
  public function serialize() {
    $vars = get_object_vars($this);
    unset($vars['query']);
    return serialize($vars);
  }

  /**
   * Unserialize a pager object
   *
   * @param string $serialized
   * @return void
   */
  public function unserialize($serialized) {
    $array = unserialize($serialized);

    foreach($array as $name => $values)
    {
      $this->$name = $values;
    }
  }

}