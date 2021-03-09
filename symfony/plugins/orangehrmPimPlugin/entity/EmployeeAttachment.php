<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeAttachment
 *
 * @ORM\Table(name="hs_hr_emp_attachment")
 * @ORM\Entity
 */
class EmployeeAttachment
{
    /**
     * @var int
     *
     * @ORM\Column(name="emp_number", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $emp_number;

    /**
     * @var int
     *
     * @ORM\Column(name="eattach_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $attach_id;

    /**
     * @var int
     *
     * @ORM\Column(name="eattach_size", type="integer", length=4)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="eattach_desc", type="string", length=200)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="eattach_filename", type="string", length=100)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="eattach_attachment", type="blob", length=2147483647)
     */
    private $attachment;

    /**
     * @var string
     *
     * @ORM\Column(name="eattach_type", type="string", length=200)
     */
    private $file_type;

    /**
     * @var string
     *
     * @ORM\Column(name="screen", type="string", length=100)
     */
    private $screen;

    /**
     * @var int
     *
     * @ORM\Column(name="attached_by", type="integer", length=4)
     */
    private $attached_by;

    /**
     * @var string
     *
     * @ORM\Column(name="attached_by_name", type="string", length=200)
     */
    private $attached_by_name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="attached_time", type="datetime")
     */
    private $attached_time;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="EmployeeAttachment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     * })
     */
    private $Employee;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Employee = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
