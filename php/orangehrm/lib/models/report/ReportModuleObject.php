<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.

 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTabILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA 
 */

 abstract class ReportModuleObject {
     protected $dataValues = array();
     
     public function __set($name, $value) {
        if (array_key_exists($name, $this->dataValues)) {
            $this->dataValues[$name] = $value;
        }
        elseif (array_key_exists($name, $this->publicPropertyMapping)) {
            $key = $this->publicPropertyMapping[$name];
            $this->dataValues[$key];
        } else {
            throw new Exception('PropertyNotSet');
        }
    }

    public function __get($name) {
        if (array_key_exists($name, $this->dataValues)) {
            return $this->dataValues[$name];
        } else {
            // TODO: Warn
            return null;
        }
    }

    public function __isset($name) {
        return (array_key_exists($name, $this->dataValues) && isset ($this->dataValues[$name]));
    }
 }