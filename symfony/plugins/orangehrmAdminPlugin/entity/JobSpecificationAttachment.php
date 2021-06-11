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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\JobSpecificationAttachmentDecorator;

/**
 * @method JobSpecificationAttachmentDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_job_specification_attachment")
 * @ORM\Entity
 */
class JobSpecificationAttachment
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
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=200)
     */
    private string $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=200)
     */
    private string $fileType;

    /**
     * @var int
     *
     * @ORM\Column(name="file_size", type="integer")
     */
    private int $fileSize;

    /**
     * @var string|resource
     *
     * @ORM\Column(name="file_content", type="blob")
     */
    private $fileContent;

    /**
     * @var JobTitle
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\JobTitle", inversedBy="jobSpecificationAttachment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="job_title_id", nullable=false)
     */
    private JobTitle $jobTitle;

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
    public function setId(int $id)
    {
        $this->id = $id;
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
    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType(string $fileType)
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
    public function setFileSize(int $fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return string|resource
     */
    public function getFileContent()
    {
        return $this->fileContent;
    }

    /**
     * @param string $fileContent
     */
    public function setFileContent(string $fileContent)
    {
        $this->fileContent = $fileContent;
    }

    /**
     * @return JobTitle
     */
    public function getJobTitle(): JobTitle
    {
        return $this->jobTitle;
    }

    /**
     * @param JobTitle $jobTitle
     */
    public function setJobTitle(JobTitle $jobTitle)
    {
        $this->jobTitle = $jobTitle;
    }
}
