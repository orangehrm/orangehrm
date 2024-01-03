<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Recruitment\Dto;

class RecruitmentAttachment
{
    /**
     * @var int|null
     */
    private ?int $id;

    /**
     * @var string|null
     */
    private ?string $fileName;

    /**
     * @var string|null
     */
    private ?string $fileType;

    /**
     * @var string|null
     */
    private ?string $fileSize;

    /**
     * @var int|null
     */
    private ?int $fkIdentity;

    /**
     * @var string|null
     */
    private ?string $comment;

    /**
     * @param int|null $id
     * @param string|null $fileName
     * @param string|null $fileType
     * @param string|null $fileSize
     * @param int|null $fkIdentity
     * @param string|null $comment
     */
    public function __construct(
        ?int $id,
        ?string $fileName,
        ?string $fileType,
        ?string $fileSize,
        ?int $fkIdentity,
        ?string $comment = null
    ) {
        $this->id = $id;
        $this->fileName = $fileName;
        $this->fileType = $fileType;
        $this->fileSize = $fileSize;
        $this->fkIdentity = $fkIdentity;
        $this->comment = $comment;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @return string|null
     */
    public function getFileSize(): ?string
    {
        return $this->fileSize;
    }

    /**
     * @return int|null
     */
    public function getFkIdentity(): ?int
    {
        return $this->fkIdentity;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}
