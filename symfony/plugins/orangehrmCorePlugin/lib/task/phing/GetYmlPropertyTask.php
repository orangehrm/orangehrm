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
 *
 */

require_once "phing/Task.php";

/**
 * Phing task to get given YML property from given file
 */
class GetYmlPropertyTask extends Task {
    
    private $ymlFile;    
    private $ymlProperty;
    private $returnProperty;
    private $symfonyDir;
    
    public function getYmlFile() {
        return $this->ymlFile;
    }

    public function setYmlFile($ymlFile) {
        $this->ymlFile = $ymlFile;
    }

    public function getYmlProperty() {
        return $this->ymlProperty;
    }

    public function setYmlProperty($ymlProperty) {
        $this->ymlProperty = $ymlProperty;
    }

    public function getReturnProperty() {
        return $this->returnProperty;
    }

    public function setReturnProperty($returnProperty) {
        $this->returnProperty = $returnProperty;
    }

    public function getSymfonyDir() {
        return $this->symfonyDir;
    }

    public function setSymfonyDir($symfonyDir) {
        $this->symfonyDir = $symfonyDir;
    }

    
    /**
     * The init method: Do init steps.
     */
    public function init() {
      // nothing to do here
    }

    /**
     * The main entry point method.
     */
    public function main() {
        
        require_once $this->symfonyDir . '/lib/vendor/symfony/lib/yaml/sfYaml.php'; 
        
        $properties = sfYaml::load($this->ymlFile);
        
        if (is_array($properties)) {
            $flattenedProperties = $this->flattenArray($properties);
            
            print_r($flattenedProperties);
            
            $returnValue = null;
            if (isset($flattenedProperties[$this->ymlProperty])) {
                $returnValue = $flattenedProperties[$this->ymlProperty];
            } else if (isset($this->default)) {
                $returnValue = $this->default;
            } else {
                throw new BuildException("property: $this->ymlProperty not found in $this->ymlFile and no default specified");
            }
        }

        $this->project->setProperty($this->returnProperty, $returnValue);
    }  
    
    protected function flattenArray($array, $prefix = '') {
        $flattenedArray = array();
        
        foreach($array as $key => $item) {
            if (is_array($item)) {
                $flattened = $this->flattenArray($item, $prefix . $key . '_');
                $flattenedArray = array_merge($flattenedArray, $flattened);
            } else {
                $flattenedArray[$prefix . $key] = $item;
            }
        }
        
        return $flattenedArray;
    }
}
