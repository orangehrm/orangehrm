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

class WSUtilityService extends BaseService {

    const RESULT_FORMAT_JSON = 'json';

    protected $wsUtilityDao;

    /**
     *
     * @return WSUtilityDao 
     */
    public function getWSUtilityDao() {
        if (!($this->wsUtilityDao instanceof WSUtilityDao)) {
            $this->wsUtilityDao = new WSUtilityDao();
        }
        return $this->wsUtilityDao;
    }

    /**
     *
     * @param WSUtilityDao $webServiceUtilityDao 
     */
    public function setWSUtilityDao(WSUtilityDao $wsUtilityDao) {
        $this->wsUtilityDao = $wsUtilityDao;
    }

    /**
     *
     * @param mixed $result
     * @param string $format
     * @return mixed
     */
    public function format($result, $format) {
        if ($result instanceof Doctrine_Record || $result instanceof Doctrine_Collection) {
            return $this->getWSUtilityDao()->format($result, $format);
        } else {
            if ($format == WSHelper::FORMAT_JSON) {
                return json_encode($result);
            } else {
                // TODO: Implement other formatters
            }
        }
    }

}
