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
    /**
     * @var null|I18NService
     */
    protected $i18NService = null;
    /**
     * @var bool
     */
    protected $loadData = true;

    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @inheritDoc
     */
    public function save($catalogue = 'messages')
    {
        throw new Exception('The "save()" method is not implemented for this message source.');
    }

    /**
     * @inheritDoc
     */
    public function delete($message, $catalogue = 'messages')
    {
        throw new Exception('The "delete()" method is not implemented for this message source.');
    }

    /**
     * @inheritDoc
     */
    public function update($text, $target, $comments, $catalogue = 'messages')
    {
        throw new Exception('The "update()" method is not implemented for this message source.');
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
        return $variant === $this->culture;
    }

    /**
     * @inheritDoc
     */
    protected function getLastModified($source)
    {
        $language = $this->getI18NService()->getLanguageByCode($this->culture);
        if ($language instanceof I18NLanguage && !empty($language->getModifiedAt())) {
            return (new DateTime($language->getModifiedAt()))->getTimestamp();
        } else {
            // if no particular language in database (avoid fetching messages from database)
            $this->loadData = false;
            return 0;
        }
    }

    /**
     * @inheritDoc
     */
    public function &loadData($variant)
    {
        $translations = [];

        if (!$this->loadData) {
            return $translations;
        }

        $messages = $this->getI18NService()->getMessages($this->culture);

        foreach ($messages as $message) {
            $source = (string)$message->getI18NLangString()->getValue();
            $translations[$source][] = (string)$message->getValue();
            $translations[$source][] = (string)$message->getI18NLangString()->getId();
            $translations[$source][] = (string)$message->getI18NLangString()->getNote();
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
        return [$this->culture];
    }

    /**
     * @return I18NService
     */
    protected function getI18NService(): I18NService
    {
        if (is_null($this->i18NService)) {
            $this->i18NService = new I18NService();
        }
        return $this->i18NService;
    }
}
