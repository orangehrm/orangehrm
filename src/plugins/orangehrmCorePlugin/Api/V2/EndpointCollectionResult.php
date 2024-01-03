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

namespace OrangeHRM\Core\Api\V2;

use OrangeHRM\Core\Api\V2\Serializer\AbstractEndpointResult;
use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;

class EndpointCollectionResult extends AbstractEndpointResult
{
    /**
     * @inheritDoc
     * @throws NormalizeException
     */
    public function normalize(): array
    {
        if (in_array(CollectionNormalizable::class, array_values(class_implements($this->modelClass)))) {
            return $this->normalizeObject();
        }
        return $this->normalizeObjectsArray();
    }
}
