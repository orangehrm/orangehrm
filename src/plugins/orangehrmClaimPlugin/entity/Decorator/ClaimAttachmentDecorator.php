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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\ClaimAttachment;
use OrangeHRM\Entity\User;

class ClaimAttachmentDecorator
{
    use EntityManagerHelperTrait;
    use AuthUserTrait;

    /**
     * @var ClaimAttachment
     */
    protected ClaimAttachment $claimAttachment;

    /**
     * @var string|null
     */
    protected ?string $attachmentString = null;

    /**
     * @param ClaimAttachment $claimAttachment
     */
    public function __construct(ClaimAttachment $claimAttachment)
    {
        $this->claimAttachment = $claimAttachment;
    }

    /**
     * @return ClaimAttachment
     */
    public function getClaimAttachment(): ClaimAttachment
    {
        return $this->claimAttachment;
    }

    /**
     * @param int $userId
     */
    public function setUserByUserId(int $userId): void
    {
        $user = $this->getReference(User::class, $userId);
        $this->getClaimAttachment()->setUser($user);
    }


    /**
     * @return string
     */
    public function getAttachment(): string
    {
        if ($this->attachmentString === null) {
            $this->attachmentString = stream_get_contents($this->getClaimAttachment()->getAttachment());
        }
        return $this->attachmentString;
    }
}
