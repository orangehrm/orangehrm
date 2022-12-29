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
class ClaimEvent
{
    /**
     * @var int
     * @ORM\Column(name="id",type="integer")
     */
    private int $id;

    /**
     * @var String
     * @ORM\Column(name="name",type="text")
     */
    private String $name;
    /**
     * @var String
     * @ORM\Column(name="description",type="string")
     */
    private String $description;

    /**
     * @var int
     * @ORM\Column(name="added_by",type="integer")
     */
    private int $added_by;
    /**
     * @var String
     * @ORM\Column(name="status",type="string")
     */
    private String $status;
    /**
     * @var String
     * @ORM\Column(name="is_deleted",type="smallint")
     */
    private String $is_deleted;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return String
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return String
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param String $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getAddedBy(): int
    {
        return $this->added_by;
    }

    /**
     * @param int $added_by
     */
    public function setAddedBy(int $added_by): void
    {
        $this->added_by = $added_by;
    }

    /**
     * @return String
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param String $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return String
     */
    public function getIsDeleted(): string
    {
        return $this->is_deleted;
    }

    /**
     * @param String $is_deleted
     */
    public function setIsDeleted(string $is_deleted): void
    {
        $this->is_deleted = $is_deleted;
    }


}