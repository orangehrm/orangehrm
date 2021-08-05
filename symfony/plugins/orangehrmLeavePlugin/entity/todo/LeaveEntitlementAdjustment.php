<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_leave_entitlement_adjustment")
 * @ORM\Entity
 */
class LeaveEntitlementAdjustment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="adjustment_id", type="integer", length=4)
     */
    private $adjustment_id;

    /**
     * @var int
     *
     * @ORM\Column(name="entitlement_id", type="integer", length=4)
     */
    private $entitlement_id;

    /**
     * @var string
     *
     * @ORM\Column(name="length_days", type="decimal", length=4)
     */
    private $length_days;


}
