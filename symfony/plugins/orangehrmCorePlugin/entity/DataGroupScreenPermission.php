<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataGroupScreenPermission
 *
 * @ORM\Table(name="ohrm_data_group_screen")
 * @ORM\Entity
 */
class DataGroupScreenPermission
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="data_group_id", type="integer")
     */
    private $dataGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="screen_id", type="integer")
     */
    private $screenId;

    /**
     * @var int
     *
     * @ORM\Column(name="permission", type="integer")
     */
    private $permission;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\DataGroup", mappedBy="DataGroupScreenPermission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="data_group_id", referencedColumnName="id")
     * })
     */
    private $DataGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Screen", mappedBy="DataGroupScreenPermission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="screen_id", referencedColumnName="id")
     * })
     */
    private $Screen;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->DataGroup = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Screen = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
