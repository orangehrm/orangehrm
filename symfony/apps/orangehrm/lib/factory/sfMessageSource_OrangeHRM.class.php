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

class sfMessageSource_OrangeHRM extends sfMessageSource
{
    protected $i18nDao = null;

    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @inheritDoc
     */
    public function save($catalogue = 'messages')
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete($message, $catalogue = 'messages')
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function update($text, $target, $comments, $catalogue = 'messages')
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function catalogues()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function isValidSource($variant)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getLastModified($source)
    {
        $lastModified = $this->getI18NDao()->getLastModified($this->culture);
        if ($lastModified instanceof I18NLanguage) {
            return (new DateTime($lastModified->getModifiedAt()))->getTimestamp();
        } else {
            return 0;
        }
    }

    /**
     * @inheritDoc
     */
    public function &loadData($variant)
    {
        $messages = $this->getI18NDao()->getMessages();

        $translations = [];
        foreach ($messages as $message) {
            $source = (string)$message->getI18NLangString()->getValue();
            $translations[$source][] = (string)$message->getValue();
            $translations[$source][] = (string)$message->getI18NLangString()->getId();
            $translations[$source][] = (string)$message->getNote();
        }

        return $translations;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return md5($this->source);
    }

    /**
     * @inheritDoc
     */
    public function getCatalogueList($catalogue)
    {
        return [];
    }

    protected function getI18NDao()
    {
        if (is_null($this->i18nDao)) {
            $this->i18nDao = new I18NDao();
        }
        return $this->i18nDao;
    }
}
