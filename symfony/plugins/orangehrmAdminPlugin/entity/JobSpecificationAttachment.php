<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JobSpecificationAttachment
 *
 * @ORM\Table(name="ohrm_job_specification_attachment")
 * @ORM\Entity
 */
class JobSpecificationAttachment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=13)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255)
     */
    private $fileType;

    /**
     * @var int
     *
     * @ORM\Column(name="file_size", type="integer", length=30)
     */
    private $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="file_content", type="blob", length=2147483647)
     */
    private $fileContent;

    /**
     * @var JobTitle
     *
     * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\JobTitle", inversedBy="jobSpecificationAttachment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="job_title_id", nullable=false)
     */
    private $jobTitle;

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
     * @return string
     */
    public function getFileContent(): string
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
