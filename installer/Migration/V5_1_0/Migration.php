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

namespace OrangeHRM\Installer\Migration\V5_1_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    protected ?LangStringHelper $langStringHelper = null;

    protected ?TranslationHelper $translationHelper = null;

    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');

        $this->getConnection()->executeStatement(
            'ALTER TABLE ohrm_kpi CHANGE job_title_code job_title_code INT(13) NOT NULL'
        );
        $kpiForeignKeyConstraint = new ForeignKeyConstraint(
            ['job_title_code'],
            'ohrm_job_title',
            ['id'],
            'ohrm_kpi_for_job_title_id',
            ['onCascade' => 'DELETE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_kpi', $kpiForeignKeyConstraint);

        $groups = ['recruitment'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->deleteNonCustomizedLangStrings($group);
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $groups = ['admin', 'general', 'pim', 'leave', 'time', 'attendance', 'maintenance', 'help', 'auth'];
        foreach ($groups as $group) {
            $this->getLangStringHelper()->insertOrUpdateLangStrings($group);
        }

        $langCodes = [
            'bg_BG',
            'da_DK',
            'de',
            'en_US',
            'es',
            'es_AR',
            'es_BZ',
            'es_CR',
            'es_ES',
            'fr',
            'fr_FR',
            'id_ID',
            'ja_JP',
            'nl',
            'om_ET',
            'th_TH',
            'vi_VN',
            'zh_Hans_CN',
            'zh_Hant_TW'
        ];
        foreach ($langCodes as $langCode) {
            $this->getTranslationHelper()->addTranslations($langCode);
        }
    }

    public function getVersion(): string
    {
        return '5.1.0';
    }

    /**
     * @return LangStringHelper
     */
    public function getLangStringHelper(): LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper($this->getConnection());
        }
        return $this->langStringHelper;
    }

    /**
     * @return TranslationHelper
     */
    public function getTranslationHelper(): TranslationHelper
    {
        if (is_null($this->translationHelper)) {
            $this->translationHelper = new TranslationHelper($this->getConnection());
        }
        return $this->translationHelper;
    }
}
