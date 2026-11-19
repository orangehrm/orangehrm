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

use Doctrine\DBAL\Types\Types;
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
        $this->addOpenIdProviderSignInChecks();

        foreach (['admin', 'auth'] as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
        }
        $this->updateLangStringVersion($this->getVersion());
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): string
    {
        return '5.9.1';
    }

    /**
     * Existing providers get both checks off, so their sign-in behaves as before the upgrade.
     * Providers added later default to both on (see the OpenIdProvider entity).
     */
    private function addOpenIdProviderSignInChecks(): void
    {
        foreach (['require_verified_email', 'require_matching_subject'] as $column) {
            if (!$this->getSchemaHelper()->columnExists('ohrm_openid_provider', $column)) {
                $this->getSchemaHelper()->addColumn(
                    'ohrm_openid_provider',
                    $column,
                    Types::BOOLEAN,
                    ['Notnull' => true, 'Default' => false]
                );
            }
        }
    }

    /**
     * @return LangStringHelper
     */
    private function getLangStringHelper(): LangStringHelper
    {
        return $this->langStringHelper ??= new LangStringHelper($this->getConnection());
    }

    /**
     * @param string $version
     */
    private function updateLangStringVersion(string $version): void
    {
        $qb = $this->createQueryBuilder()
            ->update('ohrm_i18n_lang_string', 'lang_string')
            ->set('lang_string.version', ':version')
            ->setParameter('version', $version);
        $qb->andWhere($qb->expr()->isNull('lang_string.version'))
            ->executeStatement();
    }
}
