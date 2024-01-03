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

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\ReviewerRatingDecorator;

/**
 * @method ReviewerRatingDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_reviewer_rating")
 * @ORM\Entity
 */
class ReviewerRating
{
    use DecoratorTrait;

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
     * @ORM\Column(name="rating", type="decimal", precision=18, scale=2)
     */
    private ?string $rating;


    /**
     * @var string|null
     *
     * @ORM\Column(name="comment", type="text", length=65532)
     */
    private ?string $comment;

    /**
     * @var PerformanceReview|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\PerformanceReview", inversedBy="ReviewerRating")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="review_id", referencedColumnName="id")
     * })
     */
    private ?PerformanceReview $performanceReview;

    /**
     * @var Reviewer
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Reviewer", inversedBy="ReviewerRating")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="reviewer_id", referencedColumnName="id")
     * })
     */
    private Reviewer $reviewer;

    /**
     * @var Kpi|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Kpi", inversedBy="ReviewerRating")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="kpi_id", referencedColumnName="id")
     * })
     */
    private ?Kpi $kpi;

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
     * @return string|null
     */
    public function getRating(): ?string
    {
        return $this->rating;
    }

    /**
     * @param string|null $rating
     */
    public function setRating(?string $rating): void
    {
        $this->rating = $rating;
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

    /**
     * @return PerformanceReview|null
     */
    public function getPerformanceReview(): ?PerformanceReview
    {
        return $this->performanceReview;
    }

    /**
     * @param PerformanceReview|null $performanceReview
     */
    public function setPerformanceReview(?PerformanceReview $performanceReview): void
    {
        $this->performanceReview = $performanceReview;
    }

    /**
     * @return Reviewer
     */
    public function getReviewer(): Reviewer
    {
        return $this->reviewer;
    }

    /**
     * @param Reviewer $reviewer
     */
    public function setReviewer(Reviewer $reviewer): void
    {
        $this->reviewer = $reviewer;
    }

    /**
     * @return Kpi|null
     */
    public function getKpi(): ?Kpi
    {
        return $this->kpi;
    }

    /**
     * @param Kpi|null $kpi
     */
    public function setKpi(?Kpi $kpi): void
    {
        $this->kpi = $kpi;
    }
}
