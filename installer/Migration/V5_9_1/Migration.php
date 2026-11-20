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

namespace OrangeHRM\Installer\Migration\V5_9_1;

use OrangeHRM\Installer\Util\V1\AbstractMigration;
use OrangeHRM\Installer\Util\V1\LangStringHelper;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        // Some 5.9 databases have the 'Employees on Leave Today' string under
        // `admin.workspace_notification_event_leave_today` instead of
        // `dashboard.employees_on_leave_today`. Then the Dashboard widget title and the
        // Workspace Notification event type show the raw key. The string is matched by
        // value, so this moves it back to the dashboard key and keeps its translations.
        $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, 'dashboard');
    }

    /**
     * @return LangStringHelper
     */
    private function getLangStringHelper(): LangStringHelper
    {
        if ($this->langStringHelper === null) {
            $this->langStringHelper = new LangStringHelper($this->getConnection());
        }
        return $this->langStringHelper;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.9.1';
    }
}
