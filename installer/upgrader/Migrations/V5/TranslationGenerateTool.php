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

namespace OrangeHRM\Installer\upgrader\Migrations\V5;

use Doctrine\DBAL\Query\QueryBuilder;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Installer\Util\V1\Dto\TransUnit;
use Symfony\Component\Yaml\Yaml;

class TranslationGenerateTool
{
    use EntityManagerHelperTrait;

    public function generateTranslations()
    {
        $langCodes = ['bg_BG', 'da_DK', 'de', 'en_US', 'es', 'es_AR', 'es_BZ', 'es_CR', 'es_ES', 'fr', 'fr_FR', 'id_ID', 'ja_JP', 'nl', 'om_ET', 'th_TH', 'vi_VN', 'zh_Hans_CN', 'zh_Hant_TW', 'zz_ZZ'];  //add the xml files inside installer/upgrader/Migrations/V5/translations/messages folder
        foreach ($langCodes as $langCode) {
            $filename = 'installer/upgrader/Migrations/V5/translations/messages.' . $langCode . '.xml';
            $this->readTranslations($filename, $langCode);
        }
    }

    /**
     * @param string $filepath
     * @param string $language
     * @return void
     */
    private function readTranslations(string $filepath, string $language): void
    {
        $xml = simplexml_load_file($filepath);
        $transArray = ['translations' => []];
        foreach ($xml->file->body->children() as $string) {
            $translation = new TransUnit($string->source, $string->target);
            if (!empty($translation->getTarget())) {
                $transArray['translations'][] = [
                    'source' => $translation->getSource(),
                    'target' => $translation->getTarget()
                ];
            }
        }
        $this->createYml($transArray, $language);
    }

    private function createYml(array $translationArray, string $language): void
    {
        $yaml = Yaml::dump($translationArray, 2, 4);
        $filename = 'installer/Migration/V5_0_0/translation/.' . $language . '.yml';
        file_put_contents($filename, $yaml);
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

}
