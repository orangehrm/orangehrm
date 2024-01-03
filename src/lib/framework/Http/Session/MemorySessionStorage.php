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

namespace OrangeHRM\Framework\Http\Session;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class MemorySessionStorage implements SessionStorageInterface
{
    /**
     * @var SessionBagInterface[]
     */
    protected array $bags = [];

    /**
     * @var MetadataBag
     */
    protected MetadataBag $metadataBag;

    public function __construct()
    {
        $this->metadataBag = new MetadataBag();
    }

    /**
     * @inheritDoc
     */
    public function start(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isStarted(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function setId(string $id): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'SESSID';
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): void
    {
    }

    /**
     * @inheritDoc
     */
    public function regenerate(bool $destroy = false, int $lifetime = null): bool
    {
        if ($destroy) {
            $this->metadataBag->stampNew();
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function save(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        foreach ($this->bags as $bag) {
            $bag->clear();
        }
    }

    /**
     * @inheritDoc
     */
    public function getBag(string $name)
    {
        if (!isset($this->bags[$name])) {
            throw new InvalidArgumentException(sprintf('The SessionBagInterface "%s" is not registered.', $name));
        }

        return $this->bags[$name];
    }

    /**
     * @inheritDoc
     */
    public function registerBag(SessionBagInterface $bag)
    {
        $this->bags[$bag->getName()] = $bag;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataBag(): MetadataBag
    {
        return $this->metadataBag;
    }
}
