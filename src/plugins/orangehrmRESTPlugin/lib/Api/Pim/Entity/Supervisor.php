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

namespace Orangehrm\Rest\Api\Pim\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class Supervisor implements Serializable
{

    /**
     * @var
     */
    private $name = '';

    private $id = 0;

    private $code = '';

    private $reportingMethod = '';

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getReportingMethod()
    {
        return $this->reportingMethod;
    }

    /**
     * @param string $reportingMethod
     */
    public function setReportingMethod($reportingMethod)
    {
        $this->reportingMethod = $reportingMethod;
    }

    /**
     * Supervisor constructor.
     * @param $name
     * @param $id
     */
    public function __construct($name, $id, $code = '', $reportingMethod = '')
    {
        $this->setName($name);
        $this->setId($id);
        $this->setReportingMethod($reportingMethod);
        $this->setCode($code);
        return $this;
    }

    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'id' => $this->getId(),
            'code' => $this->getCode(),
            'reportingMethod' => $this->getReportingMethod()
        );
    }

    /**
     * Name and id only
     *
     * @return array
     */
    public function _toArray()
    {
        return array(
            'name' => $this->getName(),
            'id' => $this->getId()
        );
    }
}
