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

namespace OrangeHRM\Entity;

use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\InterviewAttachmentDecorator;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method InterviewAttachmentDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_job_interview_attachment")
 * @ORM\Entity
 */
class InterviewAttachment
{
    use DecoratorTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Interview
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Interview", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="interview_id", referencedColumnName="id", nullable=false)
     */
    private Interview $interview;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=200)
     */
    private string $fileName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_type", type="string", length=200)
     */
    private ?string $fileType = null;

    /**
     * @var int
     *
     * @ORM\Column(name="file_size", type="integer", length=11)
     */
    private int $fileSize;

    /**
     * @var string|resource
     *
     * @ORM\Column(name="file_content", type="blob")
     */
    private $fileContent;

    /**
     * @var int|null
     *
     * @ORM\Column(name="attachment_type", type="integer", length=4)
     */
    private ?int $attachmentType = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private ?string $comment = null;

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
     * @return Interview
     */
    public function getInterview(): Interview
    {
        return $this->interview;
    }

    /**
     * @param Interview $interview
     */
    public function setInterview(Interview $interview): void
    {
        $this->interview = $interview;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string|null $fileType
     */
    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize(int $fileSize): void
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return resource|string
     */
    public function getFileContent()
    {
        return $this->fileContent;
    }

    /**
     * @param resource|string $fileContent
     */
    public function setFileContent(string $fileContent): void
    {
        $this->fileContent = $fileContent;
    }

    /**
     * @return int|null
     */
    public function getAttachmentType(): ?int
    {
        return $this->attachmentType;
    }

    /**
     * @param int|null $attachmentType
     */
    public function setAttachmentType(?int $attachmentType): void
    {
        $this->attachmentType = $attachmentType;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
}
