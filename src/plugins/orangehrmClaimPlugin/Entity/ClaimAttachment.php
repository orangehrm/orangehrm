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


/**
 * @ORM\Entity(repositoryClass="App\Repository\ClaimAttachmentRepository")
 * @ORM\Table(name="ohrm_claim_attachment")
 */
class ClaimAttachment
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="ClaimRequest", inversedBy="attachments")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private $request;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $eattach_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $eattach_size;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $eattach_desc;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $eattach_filename;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $eattach_attachment;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $eattach_type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $attached_by;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $attached_by_name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $attached_time;

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request): void
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getEattachId()
    {
        return $this->eattach_id;
    }

    /**
     * @param mixed $eattach_id
     */
    public function setEattachId($eattach_id): void
    {
        $this->eattach_id = $eattach_id;
    }

    /**
     * @return mixed
     */
    public function getEattachSize()
    {
        return $this->eattach_size;
    }

    /**
     * @param mixed $eattach_size
     */
    public function setEattachSize($eattach_size): void
    {
        $this->eattach_size = $eattach_size;
    }

    /**
     * @return mixed
     */
    public function getEattachDesc()
    {
        return $this->eattach_desc;
    }

    /**
     * @param mixed $eattach_desc
     */
    public function setEattachDesc($eattach_desc): void
    {
        $this->eattach_desc = $eattach_desc;
    }

    /**
     * @return mixed
     */
    public function getEattachFilename()
    {
        return $this->eattach_filename;
    }

    /**
     * @param mixed $eattach_filename
     */
    public function setEattachFilename($eattach_filename): void
    {
        $this->eattach_filename = $eattach_filename;
    }

    /**
     * @return mixed
     */
    public function getEattachAttachment()
    {
        return $this->eattach_attachment;
    }

    /**
     * @param mixed $eattach_attachment
     */
    public function setEattachAttachment($eattach_attachment): void
    {
        $this->eattach_attachment = $eattach_attachment;
    }

    /**
     * @return mixed
     */
    public function getEattachType()
    {
        return $this->eattach_type;
    }

    /**
     * @param mixed $eattach_type
     */
    public function setEattachType($eattach_type): void
    {
        $this->eattach_type = $eattach_type;
    }

    /**
     * @return mixed
     */
    public function getAttachedBy()
    {
        return $this->attached_by;
    }

    /**
     * @param mixed $attached_by
     */
    public function setAttachedBy($attached_by): void
    {
        $this->attached_by = $attached_by;
    }

    /**
     * @return mixed
     */
    public function getAttachedByName()
    {
        return $this->attached_by_name;
    }

    /**
     * @param mixed $attached_by_name
     */
    public function setAttachedByName($attached_by_name): void
    {
        $this->attached_by_name = $attached_by_name;
    }

    /**
     * @return mixed
     */
    public function getAttachedTime()
    {
        return $this->attached_time;
    }

    /**
     * @param mixed $attached_time
     */
    public function setAttachedTime($attached_time): void
    {
        $this->attached_time = $attached_time;
    }



}