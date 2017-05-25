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

class Education implements Serializable
{
    /**
     * @var
     */
    private $level = '';

    private $institute = '';

    private $from = '';

    private $to = '';

    private $year = '';

    private $seqId ;

    private $gpa = '';

    private $specialization = '';

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getInstitute()
    {
        return $this->institute;
    }

    /**
     * @param string $institute
     */
    public function setInstitute($institute)
    {
        $this->institute = $institute;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getSeqId()
    {
        return $this->seqId;
    }

    /**
     * @param mixed $seqId
     */
    public function setSeqId($seqId)
    {
        $this->seqId = $seqId;
    }

    /**
     * @return string
     */
    public function getGpa()
    {
        return $this->gpa;
    }

    /**
     * @param string $gpa
     */
    public function setGpa($gpa)
    {
        $this->gpa = $gpa;
    }

    /**
     * @return string
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * @param string $specialization
     */
    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;
    }




    public function toArray()
    {
        return array(
            'seqId'      => $this->getSeqId(),
            'level' => $this->getLevel(),
            'institute' => $this->getInstitute(),
            'specialization' => $this->getSpecialization(),
            'year' => $this->getYear(),
            'fromDate'=> $this->getFrom(),
            'toDate'=> $this->getTo(),
            'gpa'=> $this->getGpa()
        );
    }

    public function build(\EmployeeEducation $education){

        $this->setSeqId($education->getId());
        $this->setLevel($education->getEducation()->getName());
        $this->setSpecialization($education->getMajor());
        $this->setYear($education->getYear());
        $this->setFrom($education->getStartDate());
        $this->setTo($education->getEndDate());
        $this->setGpa($education->getScore());
        $this->setInstitute($education->getInstitute());

    }
}