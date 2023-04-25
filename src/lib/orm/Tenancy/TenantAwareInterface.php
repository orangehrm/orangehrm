<?php

namespace OrangeHRM\ORM\Tenancy;

interface TenantAwareInterface
{
    public function setOrgId(?int $orgId): void;
}