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

/**
 * ReviewerRating
 *
 * @ORM\Table(name="ohrm_reviewer_rating")
 * @ORM\Entity
 */
class ReviewerRating
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=6)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rating", type="decimal",precision=18, scale=2)
     */
    private ?string $rating;

    /**
     * @var int|null
     *
     * @ORM\Column(name="kpi_id", type="integer", length=7)
     */
    private ?int $kpiId;


    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=65532)
     */
    private ?string $comment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="PerformanceReview", mappedBy="ReviewerRating")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="review_id", referencedColumnName="id")
     * })
     */
    private $performanceReview;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Reviewer", mappedBy="ReviewerRating")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reviewer_id", referencedColumnName="id")
     * })
     */
    private $reviewer;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Kpi", mappedBy="ReviewerRating")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kpi_id", referencedColumnName="id")
     * })
     */
    private $kpi;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->performanceReview = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reviewer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->kpi = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
