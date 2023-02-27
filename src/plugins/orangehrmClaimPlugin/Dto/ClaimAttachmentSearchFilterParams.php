<?php

namespace OrangeHRM\Claim\Dto;

use OrangeHRM\Core\Dto\FilterParams;

class ClaimAttachmentSearchFilterParams extends FilterParams
{
    protected ?int $claimRequestId;

    /**
     * @return int|null
     */
    public function getClaimRequestId(): ?int
    {
        return $this->claimRequestId;
    }

    /**
     * @param int|null $claimRequestId
     */
    public function setClaimRequestId(?int $claimRequestId): void
    {
        $this->claimRequestId = $claimRequestId;
    }
}
