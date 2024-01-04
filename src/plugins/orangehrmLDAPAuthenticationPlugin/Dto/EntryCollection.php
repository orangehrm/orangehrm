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

namespace OrangeHRM\LDAP\Dto;

use Countable;

class EntryCollection implements Countable
{
    private array $entryCollectionLookupSettingPairArray;
    private ?int $count = null;

    public function __construct(EntryCollectionLookupSettingPair ...$entryCollectionLookupSettingPairArray)
    {
        $this->entryCollectionLookupSettingPairArray = $entryCollectionLookupSettingPairArray;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        if (is_null($this->count)) {
            $this->count = 0;
            foreach ($this->entryCollectionLookupSettingPairArray as $collection) {
                $this->count += $collection->getCollection()->count();
            }
        }

        return $this->count;
    }

    /**
     * @return EntryCollectionLookupSettingPair[]
     */
    public function getCollections(): array
    {
        return $this->entryCollectionLookupSettingPairArray;
    }
}
