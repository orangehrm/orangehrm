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

namespace OrangeHRM\DevTools\Command\Util;

use Doctrine\DBAL\Query\QueryBuilder;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use Symfony\Component\Yaml\Yaml;

class TranslationGenerateTool
{
    use EntityManagerHelperTrait;

    public function generateTranslations(string $langCode)
    {
        $filename = 'devTools/core/src/Command/Util/translation/messages.' . $langCode . '.xml';
        $this->readTranslations($filename, $langCode);
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
            $translation = new TranslationUnit($string->target, null, $string->source);
            if (!empty($translation->getTarget())) {
                $translationUnit = $this->getTranslation($translation);
                if (!is_null($translationUnit)) {
                    $transArray['translations'][] = $translationUnit;
                }
            }
        }
        $this->createYml($transArray, $language);
    }

    private function createYml(array $translationArray, string $language): void
    {
        $yaml = Yaml::dump($translationArray, 2, 4);
        $filename = 'installer/Migration/V5_0_0/translation/' . $language . '.yaml';
        file_put_contents($filename, $yaml);
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    /**
     * @param TranslationUnit $transUnit
     * @return array|null
     */
    private function getTranslation(TranslationUnit $transUnit): ?array
    {
        $groups = ['admin', 'general', 'pim', 'leave', 'time', 'attendance', 'maintenance', 'help', 'auth'];
        $langStrings = [];
        foreach ($groups as $group) {
            $filepath2 = 'installer/Migration/V5_0_0/lang-string/' . $group . '.yaml';
            $yml2 = Yaml::parseFile($filepath2);
            $langStrings = array_shift($yml2);
            foreach ($langStrings as $langString) {
                if ($transUnit->getSource() === $langString['value']) {
                    return $translation = ['target' => $transUnit->getTarget(), 'unitId' => $langString['unitId'], 'group' => $group];
                }
            }
        }
        return null;
    }
}
