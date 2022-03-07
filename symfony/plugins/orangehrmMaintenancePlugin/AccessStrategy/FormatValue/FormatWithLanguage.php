<?php
/*
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be use
 */

namespace OrangeHRM\Maintenance\AccessStrategy\FormatValue;

use OrangeHRM\Maintenance\FormatValueStrategy\ValueFormatter;
use OrangeHRM\Admin\Service\LanguageService;
use OrangeHRM\Core\Exception\DaoException;

/**
 * Class FormatWithLanguage
 */
class FormatWithLanguage implements ValueFormatter
{
    private ?LanguageService $languageService = null;

    /**
     * @param $entityValue
     * @return null|string
     * @throws DaoException
     */
    public function getFormattedValue($entityValue): ?string
    {
        // TODO
        return $this->getLanguageService()->getLanguageById($entityValue)->getName();
    }

    /**
     * @return LanguageService
     */
    public function getLanguageService(): LanguageService
    {
        if (!($this->languageService instanceof LanguageService)) {
            $this->languageService = new LanguageService();
        }
        return $this->languageService;
    }
}
