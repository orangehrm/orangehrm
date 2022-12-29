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


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClaimRequestRepository")
 * @ORM\Table(name="claim_requests")
 */
class ClaimRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $emp_number;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $added_by;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"claim_request_details"})
     */
    private $reference_id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Groups({"claim_request_details"})
     */
    private $event_type_id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"claim_request_details"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank
     * @Groups({"claim_request_details"})
     */
    private $currency;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"claim_request_details"})
     */
    private $is_deleted;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"claim_request_details"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"claim_request_details"})
     */
    private $created_date;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"claim_request_details"})
     */
    private $submitted_date;

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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmpNumber()
    {
        return $this->emp_number;
    }

    /**
     * @param mixed $emp_number
     */
    public function setEmpNumber($emp_number): void
    {
        $this->emp_number = $emp_number;
    }

    /**
     * @return mixed
     */
    public function getAddedBy()
    {
        return $this->added_by;
    }

    /**
     * @param mixed $added_by
     */
    public function setAddedBy($added_by): void
    {
        $this->added_by = $added_by;
    }

    /**
     * @return mixed
     */
    public function getReferenceId()
    {
        return $this->reference_id;
    }

    /**
     * @param mixed $reference_id
     */
    public function setReferenceId($reference_id): void
    {
        $this->reference_id = $reference_id;
    }

    /**
     * @return mixed
     */
    public function getEventTypeId()
    {
        return $this->event_type_id;
    }

    /**
     * @param mixed $event_type_id
     */
    public function setEventTypeId($event_type_id): void
    {
        $this->event_type_id = $event_type_id;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * @param mixed $is_deleted
     */
    public function setIsDeleted($is_deleted): void
    {
        $this->is_deleted = $is_deleted;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param mixed $created_date
     */
    public function setCreatedDate($created_date): void
    {
        $this->created_date = $created_date;
    }

    /**
     * @return mixed
     */
    public function getSubmittedDate()
    {
        return $this->submitted_date;
    }

    /**
     * @param mixed $submitted_date
     */
    public function setSubmittedDate($submitted_date): void
    {
        $this->submitted_date = $submitted_date;
    }




}