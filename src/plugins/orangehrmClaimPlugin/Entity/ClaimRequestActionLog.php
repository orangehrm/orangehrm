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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClaimRequestActionLogRepository")
 * @ORM\Table(name="ohrm_claim_request_action_log")
 */
class ClaimRequestActionLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     * @Groups({"claim_request_action_log_details"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ClaimRequest", inversedBy="actionLogs")
     * @ORM\JoinColumn(name="claim_request_id", referencedColumnName="id")
     * @Groups({"claim_request_action_log_details"})
     */
    private $claimRequest;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="performed_by_id", referencedColumnName="id")
     * @Groups({"claim_request_action_log_details"})
     */
    private $performedBy;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"claim_request_action_log_details"})
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Groups({"claim_request_action_log_details"})
     */
    private $note;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Groups({"claim_request_action_log_details"})
     */
    private $dateTime;

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
    public function getClaimRequest()
    {
        return $this->claimRequest;
    }

    /**
     * @param mixed $claimRequest
     */
    public function setClaimRequest($claimRequest): void
    {
        $this->claimRequest = $claimRequest;
    }

    /**
     * @return mixed
     */
    public function getPerformedBy()
    {
        return $this->performedBy;
    }

    /**
     * @param mixed $performedBy
     */
    public function setPerformedBy($performedBy): void
    {
        $this->performedBy = $performedBy;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action): void
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime): void
    {
        $this->dateTime = $dateTime;
    }



}