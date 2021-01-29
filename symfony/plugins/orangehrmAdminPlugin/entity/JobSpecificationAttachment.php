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
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="job_title_id", type="integer", length=13)
     */
    private $jobTitleId;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\JobTitle", mappedBy="JobSpecificationAttachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="jobTitleId", referencedColumnName="id")
     * })
     */
    private $JobTitle;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->JobTitle = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
