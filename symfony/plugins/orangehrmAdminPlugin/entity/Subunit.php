<?php

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subunit
 *
 * @ORM\Table(name="ohrm_subunit")
 * @ORM\Entity
 */
class Subunit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_id", type="string", length=100)
     */
    private $unitId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=400)
     */
    private $description;


}
