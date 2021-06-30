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
use OrangeHRM\Framework\Http\Request;

class SaveCustomFieldController extends AbstractVueController
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

    Const DROP_DOWN = 'Drop Down';
    Const TEXT_NUMBER = 'Text or Number';

    private array $screenList = [
        ['id' => self::SCREEN_PERSONAL_DETAILS, 'label' => self::SCREEN_PERSONAL_DETAILS],
        ['id' => self::SCREEN_CONTACT_DETAILS, 'label' => self::SCREEN_CONTACT_DETAILS],
        ['id' => self::SCREEN_EMERGENCY_CONTACTS, 'label' => self::SCREEN_EMERGENCY_CONTACTS],
        ['id' => self::SCREEN_DEPENDENTS, 'label' => self::SCREEN_DEPENDENTS],
        ['id' => self::SCREEN_IMMIGRATION, 'label' => self::SCREEN_IMMIGRATION],
        ['id' => self::SCREEN_JOB, 'label' => self::SCREEN_JOB],
        ['id' => self::SCREEN_SALARY, 'label' => self::SCREEN_SALARY],
        ['id' => self::SCREEN_TAX_EXEMPTIONS, 'label' => self::SCREEN_TAX_EXEMPTIONS],
        ['id' => self::SCREEN_REPORT_TO, 'label' => self::SCREEN_REPORT_TO],
        ['id' => self::SCREEN_QUALIFICATIONS, 'label' => self::SCREEN_QUALIFICATIONS],
        ['id' => self::SCREEN_MEMBERSHIP, 'label' => self::SCREEN_MEMBERSHIP]
    ];

    private array $fieldTypeList = [
        ['id' => 0, 'label' => self::TEXT_NUMBER],
        ['id' => 1, 'label' => self::DROP_DOWN]
    ];

    public function preRender(Request $request): void
    {
        $id = $request->get('id');
        if ($id) {
            $component = new Component('custom-field-edit');
            $component->addProp(new Prop('custom-field-id', Prop::TYPE_NUMBER, $id));
        } else {
            $component = new Component('custom-field-save');
        }
        $component->addProp(new Prop('screen-list', Prop::TYPE_ARRAY, $this->screenList));
        $component->addProp(new Prop('field-type-list', Prop::TYPE_ARRAY, $this->fieldTypeList));
        $this->setComponent($component);
    }
}
