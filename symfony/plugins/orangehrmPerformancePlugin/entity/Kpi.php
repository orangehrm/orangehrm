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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\KpiDecorator;

/**
 * @method KpiDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_kpi")
 * @ORM\Entity
 */
class Kpi
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
     * @var string
     *
     * @ORM\Column(name="kpi_indicators", type="string", length=255)
     */
    private string $title;

    /**
     * @var int
     *
     * @ORM\Column(name="min_rating", type="integer", length=11)
     */
    private int $minRating;

    /**
     * @var int
     *
     * @ORM\Column(name="max_rating", type="integer", length=11)
     */
    private int $maxRating;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="default_kpi", type="boolean", nullable=true)
     */
    private ?bool $defaultKpi = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime")
     */
    private ?DateTime $deletedAt = null;

    /**
     * @var JobTitle|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\JobTitle")
     * @ORM\JoinColumn(name="job_title_code", referencedColumnName="id", nullable=true)
     */
    private ?JobTitle $jobTitle = null;

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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getMinRating(): int
    {
        return $this->minRating;
    }

    /**
     * @param int $minRating
     */
    public function setMinRating(int $minRating): void
    {
        $this->minRating = $minRating;
    }

    /**
     * @return int
     */
    public function getMaxRating(): int
    {
        return $this->maxRating;
    }

    /**
     * @param int $maxRating
     */
    public function setMaxRating(int $maxRating): void
    {
        $this->maxRating = $maxRating;
    }

    /**
     * @return bool|null
     */
    public function isDefaultKpi(): ?bool
    {
        return $this->defaultKpi;
    }

    /**
     * @param bool|null $defaultKpi
     */
    public function setDefaultKpi(?bool $defaultKpi): void
    {
        $this->defaultKpi = $defaultKpi;
    }

    /**
     * @return JobTitle|null
     */
    public function getJobTitle(): ?JobTitle
    {
        return $this->jobTitle;
    }

    /**
     * @param JobTitle|null $jobTitle
     */
    public function setJobTitle(?JobTitle $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
    }

    /**
     * @return DateTime|null
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTime|null $deletedAt
     */
    public function setDeletedAt(?DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
