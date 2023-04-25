<?php

namespace OrangeHRM\ORM\Tenancy;

use Doctrine\ORM\Mapping as ORM;

abstract class TenantAware implements TenantAwareInterface
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="org_id", type="integer",nullable=true)
     */
    protected ?int $orgId = null;

    /**
     * @param int|null $orgId
     */
    public function setOrgId(?int $orgId): void
    {
        $this->orgId = $orgId;
    }

    /**
     * @return int|null
     */
    public function getOrgId(): ?int
    {
        return $this->orgId;
    }
}