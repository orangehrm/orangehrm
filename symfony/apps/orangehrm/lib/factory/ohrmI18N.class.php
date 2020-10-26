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

class ohrmI18N extends sfI18N
{
    /**
     * @inheritDoc
     */
    public function setMessageSource($dirs, $culture = null)
    {
        if (null === $dirs) {
            $this->messageSource = $this->createMessageSource();
        } else {
            $defaultSource = array_map([$this, 'createMessageSource'], $dirs);
            $orangeHRMSource = [$this->createOrangeHRMMessageSource()];
            $this->messageSource = sfMessageSource::factory(
                'Aggregate',
                array_merge($orangeHRMSource, $defaultSource)
            );
        }

        if (null !== $this->cache) {
            $this->messageSource->setCache($this->cache);
        }

        if (null !== $culture) {
            $this->setCulture($culture);
        } else {
            $this->messageSource->setCulture($this->culture);
        }

        $this->messageFormat = null;
    }

    /**
     * @return sfMessageSource|sfMessageSource_OrangeHRM
     * @throws sfException
     */
    public function createOrangeHRMMessageSource()
    {
        return sfMessageSource::factory('OrangeHRM', null);
    }
}
