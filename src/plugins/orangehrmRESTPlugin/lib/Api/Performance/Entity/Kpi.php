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

namespace Orangehrm\Rest\Api\Performance\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class Kpi implements Serializable
{
    /**
     * @var
     */
    private $id = '';

    private $jobTitleCode = '';

    private $jobTitle = '';

    private $kpi = '';

    private $minRating = '';

    private $maxRating ;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getJobTitleCode()
    {
        return $this->jobTitleCode;
    }

    /**
     * @param string $jobTitleCode
     */
    public function setJobTitleCode($jobTitleCode)
    {
        $this->jobTitleCode = $jobTitleCode;
    }

    /**
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param string $jobTitle
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return string
     */
    public function getKpi()
    {
        return $this->kpi;
    }

    /**
     * @param string $kpi
     */
    public function setKpi($kpi)
    {
        $this->kpi = $kpi;
    }

    /**
     * @return string
     */
    public function getMinRating()
    {
        return $this->minRating;
    }

    /**
     * @param string $minRating
     */
    public function setMinRating($minRating)
    {
        $this->minRating = $minRating;
    }

    /**
     * @return mixed
     */
    public function getMaxRating()
    {
        return $this->maxRating;
    }

    /**
     * @param mixed $maxRating
     */
    public function setMaxRating($maxRating)
    {
        $this->maxRating = $maxRating;
    }

    public function toArray()
    {
        return array(
            'id'      => $this->getId(),
            'jobTitleCode' => $this->getJobTitleCode(),
            'jobTitle' => $this->getJobTitle(),
            'kpi' => $this->getKpi(),
            'minRating' => $this->getMinRating(),
            'maxRating'=> $this->getMaxRating()
        );
    }

    public function build(\Kpi $kpi){

        $this->setId($kpi->getId());
        $this->setJobTitleCode($kpi->getJobTitle());
        $this->setKpi($kpi->getKpiIndicators());
        $this->setMinRating($kpi->getMinRating());
        $this->setMaxRating($kpi->getMaxRating());

    }
}