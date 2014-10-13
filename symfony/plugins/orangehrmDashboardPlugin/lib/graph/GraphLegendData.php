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

/**
 * Description of GraphLegendData
 */
class GraphLegendData {

    const DEFAULT_NO_OF_COLUMNS = 3;

    public $labels;
    public $noOfColumns;
    public $legendDivId;
    public $useSeparateContainer;
    
    public function getUseSeparateContainer() {
        return $this->useSeparateContainer;
    }

    public function setUseSeparateContainer($useSeparateContainer) {
        $this->useSeparateContainer = $useSeparateContainer;
    }

        
    public function getLegendDivId() {
        return $this->legendDivId;
    }

    public function setLegendDivId($legendDivId) {
        $this->legendDivId = $legendDivId;
    }

    public function __construct() {
        $this->labels = array();
        $this->noOfColumns = 0;
    }

    public function getLabels() {
        return $this->labels;
    }

    public function setLabels(array $labels) {
        $this->labels = $labels;
        $this->updateNumberOfColumns();
    }

    public function updateNumberOfColumns() {
        if ($this->noOfColumns == 0) {
            $labelCount = count($this->labels);
            $this->noOfColumns = ($labelCount === 0) ? self::DEFAULT_NO_OF_COLUMNS : $labelCount;
        }
    }

}
