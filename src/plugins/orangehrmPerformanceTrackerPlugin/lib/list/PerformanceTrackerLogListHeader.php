<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceTrackerListHeader
 *
 * @author Anika
 */
class PerformanceTrackerLogListHeader extends ListHeader{
  public function __construct() {
      $this->elementTypes[] = 'performanceTrackerLogLink';
  }
}

?>
