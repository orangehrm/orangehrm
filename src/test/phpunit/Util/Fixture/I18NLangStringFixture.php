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

namespace OrangeHRM\Tests\Util\Fixture;

use OrangeHRM\Entity\I18NGroup;
use OrangeHRM\Entity\I18NLangString;

class I18NLangStringFixture extends AbstractFixture
{
    /**
     * @inheritDoc
     */
    protected function getContent(): array
    {
        /** @var I18NGroup $group */
        $I18NGroups = $this->getEntityManager()->getRepository(I18NGroup::class)->findAll();
        $groupResults = [];
        foreach ($I18NGroups as $group) {
            $result = [];
            $result['id'] = $group->getId();
            $result['name'] = $group->getName();
            $result['title'] = $group->getTitle();
            $groupResults[] = $result;
        }

        /** @var I18NLangString[] $langStrings */
        $langStrings = $this->getEntityManager()->getRepository(I18NLangString::class)->findAll();
        $results = [];
        foreach ($langStrings as $langString) {
            $result = [];
            $result['unitId'] = $langString->getUnitId();
            $result['value'] = $langString->getValue();
            $result['group_id'] = $langString->getGroup()->getId();
            $result['note'] = $langString->getNote();
            $result['version'] = $langString->getVersion();
            $results[] = $result;
        }

        return ['I18NGroup' => $groupResults,'I18NLangString' => $results];
    }

    /**
     * @inheritDoc
     */
    public static function getFileName(): string
    {
        return 'LangString.yaml';
    }
}
