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

namespace OrangeHRM\Pim\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\CustomField;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Pim\Service\CustomFieldService;

class CustomFieldController extends AbstractVueController
{
    public const SCREEN_PERSONAL_DETAILS = 'Personal Details';
    public const SCREEN_CONTACT_DETAILS = 'Contact Details';
    public const SCREEN_EMERGENCY_CONTACTS = 'Emergency Contacts';
    public const SCREEN_DEPENDENTS = 'Dependents';
    public const SCREEN_IMMIGRATION = 'Immigration';
    public const SCREEN_JOB = 'Job';
    public const SCREEN_SALARY = 'Salary';
    public const SCREEN_TAX_EXEMPTIONS = 'Tax Exemptions';
    public const SCREEN_REPORT_TO = 'Report-to';
    public const SCREEN_QUALIFICATIONS = 'Qualifications';
    public const SCREEN_MEMBERSHIP = 'Memberships';

    public const DROP_DOWN = 'Drop Down';
    public const TEXT_NUMBER = 'Text or Number';

    /**
     * @var null|CustomFieldService
     */
    protected ?CustomFieldService $customFieldService = null;

    /**
     * @return CustomFieldService
     */
    public function getCustomFieldService(): CustomFieldService
    {
        if (is_null($this->customFieldService)) {
            $this->customFieldService = new CustomFieldService();
        }
        return $this->customFieldService;
    }

    public const SCREEN_LIST = [
        ['id' => CustomField::SCREEN_PERSONAL_DETAILS, 'label' => self::SCREEN_PERSONAL_DETAILS],
        ['id' => CustomField::SCREEN_CONTACT_DETAILS, 'label' => self::SCREEN_CONTACT_DETAILS],
        ['id' => CustomField::SCREEN_EMERGENCY_CONTACTS, 'label' => self::SCREEN_EMERGENCY_CONTACTS],
        ['id' => CustomField::SCREEN_DEPENDENTS, 'label' => self::SCREEN_DEPENDENTS],
        ['id' => CustomField::SCREEN_IMMIGRATION, 'label' => self::SCREEN_IMMIGRATION],
        ['id' => CustomField::SCREEN_JOB, 'label' => self::SCREEN_JOB],
        ['id' => CustomField::SCREEN_SALARY, 'label' => self::SCREEN_SALARY],
        ['id' => CustomField::SCREEN_TAX_EXEMPTIONS, 'label' => self::SCREEN_TAX_EXEMPTIONS],
        ['id' => CustomField::SCREEN_REPORT_TO, 'label' => self::SCREEN_REPORT_TO],
        ['id' => CustomField::SCREEN_QUALIFICATIONS, 'label' => self::SCREEN_QUALIFICATIONS],
        ['id' => CustomField::SCREEN_MEMBERSHIP, 'label' => self::SCREEN_MEMBERSHIP]
    ];

    public const FIELD_TYPE_LIST = [
        ['id' => CustomField::FIELD_TYPE_STRING, 'label' => self::TEXT_NUMBER],
        ['id' => CustomField::FIELD_TYPE_SELECT, 'label' => self::DROP_DOWN]
    ];

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('custom-field-list');
        $customFieldsInUse = $this->getCustomFieldService()->getAllFieldsInUse();
        $component->addProp(new Prop('custom-field-limit', Prop::TYPE_NUMBER, CustomField::MAX_FIELD_NUM));
        $component->addProp(new Prop('screen-list', Prop::TYPE_ARRAY, self::SCREEN_LIST));
        $component->addProp(new Prop('field-type-list', Prop::TYPE_ARRAY, self::FIELD_TYPE_LIST));
        $component->addProp(new Prop('unselectable-ids', Prop::TYPE_ARRAY, $customFieldsInUse));
        $this->setComponent($component);
    }
}
