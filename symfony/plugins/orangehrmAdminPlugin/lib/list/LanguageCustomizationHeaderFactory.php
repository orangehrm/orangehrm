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
 *
 */

class LanguageCustomizationHeaderFactory extends ohrmListConfigurationFactory
{
    /**
     * @var null|I18NLanguage
     */
    protected $language = null;

    /**
     * @return I18NLanguage|null
     */
    public function getLanguage(): I18NLanguage
    {
        return $this->language;
    }

    /**
     * @param I18NLanguage $language
     */
    public function setLanguage(I18NLanguage $language)
    {
        $this->language = $language;
    }

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();

        $sortUrl = null;
        if ($this->language instanceof I18NLanguage) {
            $sortUrl = url_for(
                    'admin/languageCustomization',
                    true
                ) . "?langId={$this->language->getId()}&sortField={sortField}&sortOrder={sortOrder}";
        }

        $header1->populateFromArray(
            [
                'name' => 'Source Text',
                'width' => '20%',
                'isSortable' => true,
                'sortField' => 'ls.value',
                'sortUrl' => $sortUrl,
                'elementType' => 'label',
                'elementProperty' => ['getter' => ['getI18NLangString', 'getValue']],
            ]
        );

        $header2->populateFromArray(
            [
                'name' => 'Source Note',
                'width' => '30%',
                'elementType' => 'label',
                'elementProperty' => ['getter' => ['getI18NLangString', 'getNote']],
            ]
        );

        $header3->populateFromArray(
            [
                'name' => 'Translated Text',
                'width' => '50%',
                'isSortable' => false,
                'sortField' => 't.value',
                'sortUrl' => $sortUrl,
                'elementType' => 'textarea',
                'elementProperty' => [
                    'getter' => 'getValue',
                    'props' => [
                        'rows' => '2',
                        'cols' => '50',
                        'class' => 'translated-textarea',
                        'disabled' => true,
                        'maxlength' => 1500,
                    ],
                    'placeholderGetters' => ['id' => 'getId'],
                    'hasHiddenField' => true,
                    'hiddenFieldName' => 'translatedText[{id}]',
                    'hiddenFieldId' => 'translatedText_{id}',
                    'hiddenFieldValueGetter' => 'getValue',
                    'hiddenFieldClass' => 'hidden-translated-text',
                    'name' => '{id}',
                    'id' => 'translatedTextarea_{id}'
                ],
                'filters' => ['HtmlSpecialCharsDecodeCellFilter' => []],
            ]
        );

        $this->headers = [$header1, $header2, $header3];
    }

    /**
     * @inheritDoc
     */
    public function getClassName()
    {
        return 'LanguageCustomization';
    }
}
