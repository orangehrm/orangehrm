<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DataGroupPermission
 *
 * @ORM\Table(name="ohrm_user_role_data_group")
 * @ORM\Entity
 */
class DataGroupPermission
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
     * @ORM\Column(name="user_role_id", type="integer")
     */
    private $userRoleId;

    /**
     * @var int
     *
     * @ORM\Column(name="data_group_id", type="integer")
     */
    private $dataGroupId;

    /**
     * @var int
     *
     * @ORM\Column(name="can_read", type="integer", length=1)
     */
    private $canRead;

    /**
     * @var int
     *
     * @ORM\Column(name="can_create", type="integer", length=1)
     */
    private $canCreate;

    /**
     * @var int
     *
     * @ORM\Column(name="can_update", type="integer", length=1)
     */
    private $canUpdate;

    /**
     * @var int
     *
     * @ORM\Column(name="can_delete", type="integer", length=1)
     */
    private $canDelete;

    /**
     * @var int
     *
     * @ORM\Column(name="self", type="integer", length=1)
     */
    private $self;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\DataGroup", mappedBy="dataGroupPermission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="data_group_id", referencedColumnName="id")
     * })
     */
    private $dataGroup;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\UserRole", mappedBy="dataGroupPermission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_role_id", referencedColumnName="id")
     * })
     */
    private $userRole;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dataGroup = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userRole = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
