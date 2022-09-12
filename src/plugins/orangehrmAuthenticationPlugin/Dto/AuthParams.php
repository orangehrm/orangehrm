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
 * Boston, MA 02110-1301, USA
 */

namespace OrangeHRM\Authentication\Dto;

class AuthParams implements AuthParamsInterface
{
    private ?UserCredentialInterface $credential;
    private ?AuthAttributeBagInterface $attributeBag;

    /**
     * @param UserCredentialInterface|null $credential
     * @param AuthAttributeBagInterface|null $attributeBag
     */
    public function __construct(?UserCredentialInterface $credential, ?AuthAttributeBagInterface $attributeBag = null)
    {
        $this->credential = $credential;
        $this->attributeBag = $attributeBag;
    }

    /**
     * @inheritDoc
     */
    public function getCredential(): ?UserCredentialInterface
    {
        return clone $this->credential;
    }

    /**
     * @inheritDoc
     */
    public function getAttributeBag(): ?AuthAttributeBagInterface
    {
        return clone $this->attributeBag;
    }
}
